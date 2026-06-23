<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class OaiPmhController extends Controller
{
    private string $baseUrl;
    private string $repositoryName;

    public function __construct()
    {
        $this->baseUrl        = url('/oai');
        $this->repositoryName = config('app.name') . ' OAI Repository';
    }

    public function forJournal(Journal $journal): static
    {
        $this->baseUrl        = url("/journals/{$journal->slug}/oai");
        $this->repositoryName = $journal->name . ' OAI Repository';
        return $this;
    }

    public function __invoke(Request $request): Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $verb = $request->input('verb', '');

        // No verb → show human-readable HTML info page
        if ($verb === '') {
            return $this->infoPage();
        }

        $xml = match ($verb) {
            'Identify'            => $this->identify($request),
            'ListMetadataFormats' => $this->listMetadataFormats($request),
            'ListSets'            => $this->listSets($request),
            'ListIdentifiers'     => $this->listIdentifiers($request),
            'ListRecords'         => $this->listRecords($request),
            'GetRecord'           => $this->getRecord($request),
            default               => $this->badVerb($request),
        };

        return response($xml, 200, [
            'Content-Type'  => 'text/xml; charset=utf-8',
            'Cache-Control' => 'no-cache',
        ]);
    }

    private function infoPage(): \Illuminate\Contracts\View\View
    {
        $journals      = Journal::where('status', 'active')->where('enabled', true)
                            ->select(['id', 'slug', 'name', 'issn_online', 'issn_print', 'publisher'])
                            ->orderBy('name')
                            ->get();

        $articleCount  = Article::whereNotNull('date_published')->count();
        $earliest      = Article::orderBy('date_published')->value('date_published');

        return view('oai.index', [
            'baseUrl'         => $this->baseUrl,
            'repositoryName'  => $this->repositoryName,
            'adminEmail'      => config('mail.from.address', 'admin@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost')),
            'earliestDate'    => $earliest ? \Carbon\Carbon::parse($earliest)->toDateString() : now()->toDateString(),
            'journals'        => $journals,
            'articleCount'    => $articleCount,
        ]);
    }

    // ── Verbs ────────────────────────────────────────────────────────────────

    private function identify(Request $request): string
    {
        $earliest = Article::orderBy('date_published')->value('date_published');
        $earliestDate = $earliest
            ? \Carbon\Carbon::parse($earliest)->toIso8601String()
            : now()->toIso8601String();

        return $this->envelope($request, 'Identify', <<<XML
        <Identify>
            <repositoryName>{$this->esc($this->repositoryName)}</repositoryName>
            <baseURL>{$this->esc($this->baseUrl)}</baseURL>
            <protocolVersion>2.0</protocolVersion>
            <adminEmail>{$this->esc(config('mail.from.address', 'admin@gjs.local'))}</adminEmail>
            <earliestDatestamp>{$this->esc($earliestDate)}</earliestDatestamp>
            <deletedRecord>no</deletedRecord>
            <granularity>YYYY-MM-DDThh:mm:ssZ</granularity>
            <description>
                <oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"
                                xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd">
                    <scheme>oai</scheme>
                    <repositoryIdentifier>{$this->esc(parse_url(config('app.url'), PHP_URL_HOST) ?? 'gjs.local')}</repositoryIdentifier>
                    <delimiter>:</delimiter>
                    <sampleIdentifier>{$this->esc($this->baseUrl)}:article:1</sampleIdentifier>
                </oai-identifier>
            </description>
        </Identify>
XML);
    }

    private function listMetadataFormats(Request $request): string
    {
        return $this->envelope($request, 'ListMetadataFormats', <<<XML
        <ListMetadataFormats>
            <metadataFormat>
                <metadataPrefix>oai_dc</metadataPrefix>
                <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
                <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
            </metadataFormat>
        </ListMetadataFormats>
XML);
    }

    private function listSets(Request $request): string
    {
        $sets = Journal::where('status', 'active')
            ->select(['id', 'slug', 'name'])
            ->get()
            ->map(fn ($j) => <<<XML
            <set>
                <setSpec>journal:{$this->esc($j->slug)}</setSpec>
                <setName>{$this->esc($j->name)}</setName>
            </set>
XML)->implode("\n");

        return $this->envelope($request, 'ListSets', "<ListSets>{$sets}</ListSets>");
    }

    private function listIdentifiers(Request $request): string
    {
        $prefix = $request->input('metadataPrefix');
        if ($prefix !== 'oai_dc') {
            return $this->error($request, 'cannotDisseminateFormat', 'Metadata format not supported.');
        }

        $setSpec = $request->input('set');
        $query   = $this->articlesQuery($setSpec);

        $headers = $query->get()->map(fn ($a) => <<<XML
            <header>
                <identifier>{$this->identifier($a->id)}</identifier>
                <datestamp>{$this->datestamp($a->date_published)}</datestamp>
                <setSpec>journal:{$this->esc($a->journal?->slug ?? 'unknown')}</setSpec>
            </header>
XML)->implode("\n");

        if (!$headers) {
            return $this->error($request, 'noRecordsMatch', 'No records found.');
        }

        return $this->envelope($request, 'ListIdentifiers', "<ListIdentifiers>{$headers}</ListIdentifiers>");
    }

    private function listRecords(Request $request): string
    {
        $prefix = $request->input('metadataPrefix');
        if ($prefix !== 'oai_dc') {
            return $this->error($request, 'cannotDisseminateFormat', 'Metadata format not supported.');
        }

        $setSpec = $request->input('set');
        $query   = $this->articlesQuery($setSpec)->with(['submission.contributors', 'journal', 'section', 'galleys.file']);

        $records = $query->get()->map(fn ($a) => $this->buildRecord($a))->implode("\n");

        if (!$records) {
            return $this->error($request, 'noRecordsMatch', 'No records found.');
        }

        return $this->envelope($request, 'ListRecords', "<ListRecords>{$records}</ListRecords>");
    }

    private function getRecord(Request $request): string
    {
        $prefix     = $request->input('metadataPrefix');
        $identifier = $request->input('identifier');

        if ($prefix !== 'oai_dc') {
            return $this->error($request, 'cannotDisseminateFormat', 'Metadata format not supported.');
        }

        // identifier format: oai:{host}:article:{id}
        $id = (int) (explode(':', $identifier)[3] ?? 0);
        $article = Article::with(['submission.contributors', 'journal', 'section', 'galleys.file'])
            ->find($id);

        if (!$article) {
            return $this->error($request, 'idDoesNotExist', 'The identifier does not exist.');
        }

        return $this->envelope($request, 'GetRecord', '<GetRecord>' . $this->buildRecord($article) . '</GetRecord>');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function articlesQuery(?string $setSpec)
    {
        $q = Article::with(['journal'])->whereNotNull('date_published');

        if ($setSpec && str_starts_with($setSpec, 'journal:')) {
            $slug = substr($setSpec, 8);
            $q->whereHas('journal', fn ($j) => $j->where('slug', $slug));
        }

        return $q->orderByDesc('date_published');
    }

    private function buildRecord(Article $article): string
    {
        $sub         = $article->submission;
        $journal     = $article->journal;
        $creators    = $sub->contributors->map(fn ($c) => "<dc:creator>{$this->esc($c->full_name)}</dc:creator>")->implode("\n");
        $keywords    = collect($sub->keywords ?? [])->map(fn ($k) => "<dc:subject>{$this->esc($k)}</dc:subject>")->implode("\n");
        $doiLink     = $article->doi ? "<dc:identifier>https://doi.org/{$this->esc($article->doi)}</dc:identifier>" : '';
        $urlId       = "<dc:identifier>" . url("/journals/{$journal?->slug}/articles/{$article->id}") . "</dc:identifier>";
        $formats     = $article->galleys->map(fn ($g) => "<dc:format>{$this->esc($g->file?->mime_type ?? 'application/pdf')}</dc:format>")->implode("\n");

        return <<<XML
        <record>
            <header>
                <identifier>{$this->identifier($article->id)}</identifier>
                <datestamp>{$this->datestamp($article->date_published)}</datestamp>
                <setSpec>journal:{$this->esc($journal?->slug ?? 'unknown')}</setSpec>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                           xmlns:dc="http://purl.org/dc/elements/1.1/"
                           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                           xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                    <dc:title>{$this->esc($sub->title)}</dc:title>
                    {$creators}
                    <dc:description>{$this->esc($sub->abstract ?? '')}</dc:description>
                    {$keywords}
                    <dc:date>{$this->datestamp($article->date_published)}</dc:date>
                    <dc:type>journal article</dc:type>
                    <dc:language>{$this->esc($sub->locale)}</dc:language>
                    <dc:publisher>{$this->esc($journal?->publisher ?? '')}</dc:publisher>
                    <dc:source>{$this->esc($journal?->name ?? '')}</dc:source>
                    {$doiLink}
                    {$urlId}
                    {$formats}
                    <dc:rights>Copyright (c) authors. Open access under CC BY 4.0.</dc:rights>
                </oai_dc:dc>
            </metadata>
        </record>
XML;
    }

    private function identifier(int $articleId): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?? 'gjs.local';
        return "oai:{$host}:article:{$articleId}";
    }

    private function datestamp(mixed $date): string
    {
        if (!$date) return now()->toIso8601String();
        return \Carbon\Carbon::parse($date)->toIso8601String();
    }

    private function envelope(Request $request, string $verb, string $content): string
    {
        $responseDate = now()->toIso8601String();
        $requestUrl   = $this->esc($this->baseUrl);
        $verbAttr     = $this->esc($verb);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{$responseDate}</responseDate>
    <request verb="{$verbAttr}">{$requestUrl}</request>
    {$content}
</OAI-PMH>
XML;
    }

    private function error(Request $request, string $code, string $message): string
    {
        return $this->envelope($request, 'error', <<<XML
        <error code="{$this->esc($code)}">{$this->esc($message)}</error>
XML);
    }

    private function badVerb(Request $request): string
    {
        return $this->error($request, 'badVerb', 'Illegal OAI verb.');
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
