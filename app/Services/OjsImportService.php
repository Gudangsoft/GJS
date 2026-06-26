<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Section;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OjsImportService
{
    private int $journalId;
    private int $importUserId;
    private array $log = [];

    // OJS REST API
    private string $restBaseUrl = '';
    private ?string $apiKey     = null;
    private int $perPage        = 50;
    private bool $sslVerify     = true;
    private int $connectTimeout = 30;

    /** Stats */
    public int $sectionsCreated  = 0;
    public int $issuesCreated    = 0;
    public int $issuesSkipped    = 0;
    public int $articlesCreated  = 0;
    public int $articlesSkipped  = 0;
    public int $articlesUpdated  = 0;
    public int $errors           = 0;

    public function __construct(int $journalId)
    {
        $this->journalId    = $journalId;
        $this->importUserId = User::where('email', 'admin@gjs.local')->value('id')
            ?? User::first()?->id
            ?? 1;
    }

    // ─── REST API (OJS 3.x) ──────────────────────────────────────────────────

    public function setRestConfig(string $baseUrl, ?string $apiKey = null): void
    {
        $this->restBaseUrl = rtrim($baseUrl, '/');
        $this->apiKey      = $apiKey ?: null;
    }

    public function setSslVerify(bool $verify): void
    {
        $this->sslVerify = $verify;
    }

    public function testRestConnection(): array
    {
        try {
            $r = $this->restGet('/issues', ['count' => 1]);
            if (isset($r['items'])) {
                $total = $r['itemsMax'] ?? count($r['items']);
                return ['ok' => true, 'message' => "Koneksi berhasil. Total edisi: {$total}"];
            }
            return ['ok' => false, 'message' => 'Respons tidak dikenali: ' . json_encode($r)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function importFromRest(array $only = ['sections', 'issues', 'articles'], callable $progress = null): void
    {
        $this->info('=== Mulai Import via REST API OJS ===');
        if (in_array('sections', $only))  $this->importSections($progress);
        if (in_array('issues', $only))    $this->importIssues($progress);
        if (in_array('articles', $only))  $this->importArticles($progress);
        $this->info("=== Selesai. Edisi: +{$this->issuesCreated} | Artikel: +{$this->articlesCreated} diperbarui: {$this->articlesUpdated} | Error: {$this->errors} ===");
    }

    private function importSections(callable $progress = null): void
    {
        $this->info('Mengimpor seksi...');
        try {
            $data = $this->restGet('/sections', ['count' => 100]);
            foreach ($data['items'] ?? $data ?? [] as $s) {
                $this->upsertSection($s);
                if ($progress) $progress('section', $s);
            }
        } catch (\Throwable $e) {
            $this->warn('Seksi tidak tersedia: ' . $e->getMessage());
        }
    }

    private function importIssues(callable $progress = null): void
    {
        $this->info('Mengimpor edisi...');
        $offset = 0;
        do {
            $data  = $this->restGet('/issues', ['count' => $this->perPage, 'offset' => $offset]);
            $items = $data['items'] ?? [];
            $total = $data['itemsMax'] ?? count($items);
            foreach ($items as $issue) {
                $this->upsertIssue($issue);
                if ($progress) $progress('issue', $issue);
            }
            $offset += count($items);
        } while ($offset < $total && count($items) > 0);
    }

    private function importArticles(callable $progress = null): void
    {
        $this->info('Mengimpor artikel...');
        $offset = 0;
        do {
            $data  = $this->restGet('/submissions', [
                'count'  => $this->perPage,
                'offset' => $offset,
                'status' => 3,
            ]);
            $items = $data['items'] ?? [];
            $total = $data['itemsMax'] ?? count($items);
            foreach ($items as $submission) {
                try {
                    $this->upsertSubmission($submission);
                } catch (\Throwable $e) {
                    $this->errors++;
                    $this->error('Artikel ID ' . ($submission['id'] ?? '?') . ': ' . $e->getMessage());
                }
                if ($progress) $progress('article', $submission);
            }
            $offset += count($items);
        } while ($offset < $total && count($items) > 0);
    }

    private function restGet(string $endpoint, array $params = []): array
    {
        $req = Http::timeout(60)
            ->connectTimeout($this->connectTimeout)
            ->accept('application/json');
        if (!$this->sslVerify) $req = $req->withoutVerifying();
        if ($this->apiKey) $req = $req->withToken($this->apiKey);

        $url      = $this->restBaseUrl . '/api/v1' . $endpoint;
        $response = $req->get($url, $params);
        if ($response->failed()) {
            throw new \RuntimeException("HTTP {$response->status()} dari {$url}");
        }
        return $response->json() ?? [];
    }

    // ─── OAI-PMH ─────────────────────────────────────────────────────────────

    public function testOaiConnection(string $oaiUrl): array
    {
        try {
            $xml = $this->fetchXml($oaiUrl . '?verb=Identify');
            $repoName = (string)($xml->Identify->repositoryName ?? 'Unknown');

            // Try to get count
            $listXml = $this->fetchXml($oaiUrl . '?verb=ListRecords&metadataPrefix=oai_dc');
            $total   = (int)($listXml->ListRecords->resumptionToken['completeListSize'] ?? 0);
            $totalStr = $total > 0 ? ", total record: {$total}" : '';

            return ['ok' => true, 'message' => "Terhubung ke: {$repoName}{$totalStr}"];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function importFromOai(string $oaiUrl, ?string $setSpec = null, callable $progress = null): void
    {
        $this->info('=== Mulai Import via OAI-PMH ===');

        $params = 'verb=ListRecords&metadataPrefix=oai_dc';
        if ($setSpec) {
            $params .= '&set=' . urlencode($setSpec);
        }

        $resumptionToken = null;
        $page = 0;

        do {
            $url = $resumptionToken
                ? $oaiUrl . '?verb=ListRecords&resumptionToken=' . urlencode($resumptionToken)
                : $oaiUrl . '?' . $params;

            $xml = $this->fetchXml($url);

            if (isset($xml->error)) {
                $this->error('OAI error: ' . (string)$xml->error);
                break;
            }

            $records = $xml->ListRecords->record ?? [];
            $count   = 0;
            foreach ($records as $record) {
                // Skip deleted records
                $status = (string)($record->header['status'] ?? '');
                if ($status === 'deleted') continue;

                try {
                    $this->processOaiRecord($record);
                    $count++;
                } catch (\Throwable $e) {
                    $this->errors++;
                    $this->error('Record: ' . $e->getMessage());
                }
                if ($progress) $progress('oai', $record);
            }

            $page++;
            $this->info("  Halaman {$page}: {$count} record diproses.");

            $tokenEl = $xml->ListRecords->resumptionToken ?? null;
            $resumptionToken = $tokenEl ? trim((string)$tokenEl) : null;
        } while ($resumptionToken);

        $this->info("=== Selesai. Artikel: +{$this->articlesCreated} diperbarui: {$this->articlesUpdated} | Error: {$this->errors} ===");
    }

    private function processOaiRecord(\SimpleXMLElement $record): void
    {
        // Get oai_dc namespace element
        $metadata = $record->metadata;
        if (!$metadata) return;

        $nsOaiDc = 'http://www.openarchives.org/OAI/2.0/oai_dc/';
        $nsDc    = 'http://purl.org/dc/elements/1.1/';

        $dcRoot = $metadata->children($nsOaiDc)->dc ?? null;
        if (!$dcRoot) return;

        $dc = $dcRoot->children($nsDc);

        $title       = trim((string)($dc->title ?? ''));
        $description = trim((string)($dc->description ?? ''));
        $date        = trim((string)($dc->date ?? ''));
        $language    = trim((string)($dc->language ?? '')) ?: 'id';
        $source      = trim((string)($dc->source ?? ''));
        $rights      = trim((string)($dc->rights ?? ''));

        if (!$title) return;

        // Identifiers: DOI + article URL
        $doi        = null;
        $articleUrl = null;
        foreach ($dc->identifier as $id) {
            $str = trim((string)$id);
            if (preg_match('/^https?:\/\/doi\.org\//i', $str) || preg_match('/^10\.\d{4,}/', $str)) {
                $doi = $str;
            } elseif (str_contains($str, '/article/view/') || str_contains($str, '/article/')) {
                $articleUrl = $str;
            }
        }
        // Normalize DOI
        if ($doi && preg_match('/^https?:\/\/doi\.org\//i', $doi)) {
            $doi = 'https://doi.org/' . substr($doi, (int)strpos($doi, 'doi.org/') + 8);
        }

        // Relation: PDF download URL
        $pdfUrl = null;
        foreach ($dc->relation as $rel) {
            $str = trim((string)$rel);
            if ($str && (str_contains($str, '/article/view/') || str_contains($str, '.pdf'))) {
                $pdfUrl = $str;
                break;
            }
        }

        // Subjects/keywords
        $subjects = [];
        foreach ($dc->subject as $s) {
            $str = trim((string)$s);
            if ($str) $subjects[] = $str;
        }

        // Creators/authors
        $creators = [];
        foreach ($dc->creator as $c) {
            $str = trim((string)$c);
            if ($str) $creators[] = $str;
        }

        // Parse volume/number/year from dc:source
        $sourceData = $this->parseOaiSource($source);
        $volume     = $sourceData['volume'];
        $number     = $sourceData['number'];
        $year       = $sourceData['year'];

        // Resolve or create issue
        $issue = null;
        if ($volume || $number || $year) {
            $issue = Issue::where('journal_id', $this->journalId)
                ->where(function ($q) use ($volume, $number, $year) {
                    if ($volume) $q->where('volume', $volume);
                    if ($number) $q->where('number', $number);
                    if ($year)   $q->where('year', $year);
                })->first();

            if (!$issue) {
                $issue = Issue::create([
                    'journal_id'    => $this->journalId,
                    'volume'        => $volume,
                    'number'        => $number,
                    'year'          => $year,
                    'published'     => true,
                    'show_volume'   => true,
                    'show_number'   => true,
                    'show_year'     => true,
                ]);
                $this->issuesCreated++;
                $this->info("  Edisi baru: Vol.{$volume} No.{$number} ({$year})");
            }
        }

        // Check existing by DOI or title+journal
        $existing = null;
        if ($doi) {
            $existing = Submission::where('journal_id', $this->journalId)
                ->where('doi', $doi)->first();
        }
        if (!$existing && $articleUrl) {
            // Try match by ojs_source_url
            $existing = Submission::where('journal_id', $this->journalId)
                ->where('ojs_source_url', $articleUrl)->first();
        }

        $submissionData = [
            'journal_id'      => $this->journalId,
            'user_id'         => $this->importUserId,
            'status'          => 'published',
            'title'           => $title,
            'abstract'        => $description ?: null,
            'keywords'        => $subjects ?: null,
            'locale'          => $language,
            'doi'             => $doi,
            'submission_type' => 'article',
            'ojs_source_url'  => $articleUrl,
            'submitted_at'    => $date ?: null,
        ];

        DB::transaction(function () use ($existing, $submissionData, $issue, $creators, $pdfUrl, $language, $date) {
            if ($existing) {
                $existing->update($submissionData);
                $submission = $existing;
                $this->articlesUpdated++;
            } else {
                $submission = Submission::create($submissionData);
                $this->articlesCreated++;
                $this->info("  Artikel: {$submissionData['title']}");
            }

            $article = Article::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'submission_id'  => $submission->id,
                    'issue_id'       => $issue?->id,
                    'journal_id'     => $this->journalId,
                    'date_published' => $date ?: null,
                    'access_status'  => 'open',
                ]
            );

            // Authors
            if ($existing) {
                $submission->contributors()->delete();
            }
            foreach ($creators as $idx => $creator) {
                $parts = $this->parseOaiCreator($creator);
                SubmissionContributor::create([
                    'submission_id'   => $submission->id,
                    'first_name'      => $parts['first_name'],
                    'last_name'       => $parts['last_name'],
                    'sequence'        => $idx,
                    'primary_contact' => ($idx === 0),
                    'include_in_browse' => true,
                ]);
            }

            // Galley
            if ($pdfUrl) {
                if ($existing) $article->galleys()->delete();
                ArticleGalley::create([
                    'article_id'  => $article->id,
                    'label'       => 'PDF',
                    'locale'      => $language,
                    'remote_url'  => $pdfUrl,
                    'sequence'    => 0,
                    'is_approved' => true,
                ]);
            }
        });
    }

    private function parseOaiSource(string $source): array
    {
        $volume = null;
        $number = null;
        $year   = null;

        if (preg_match('/[Vv]ol\.?\s*(\d+)/u', $source, $m)) {
            $volume = (int)$m[1];
        }
        if (preg_match('/[Nn]o\.?\s*(\d+)/u', $source, $m) || preg_match('/[Ii]ssue\s*(\d+)/u', $source, $m)) {
            $number = (int)$m[1];
        }
        if (preg_match('/\((\d{4})\)/', $source, $m)) {
            $year = (int)$m[1];
        } elseif (preg_match('/\b(20\d{2}|19\d{2})\b/', $source, $m)) {
            $year = (int)$m[1];
        }

        return compact('volume', 'number', 'year');
    }

    private function parseOaiCreator(string $creator): array
    {
        // OJS usually outputs "Family, Given" format
        if (str_contains($creator, ',')) {
            [$last, $first] = explode(',', $creator, 2);
            return ['first_name' => trim($first), 'last_name' => trim($last)];
        }
        // Full name: split at last space
        $parts = explode(' ', trim($creator));
        $last  = array_pop($parts);
        return ['first_name' => implode(' ', $parts), 'last_name' => $last];
    }

    private function fetchXml(string $url): \SimpleXMLElement
    {
        $req = Http::timeout(90)
            ->connectTimeout($this->connectTimeout)
            ->withHeaders(['User-Agent' => 'GJS-OAI-Harvester/1.0 (+https://gjs.ac.id)']);
        if (!$this->sslVerify) $req = $req->withoutVerifying();
        $response = $req->get($url);

        if ($response->failed()) {
            throw new \RuntimeException("HTTP {$response->status()} dari {$url}");
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \RuntimeException('XML tidak valid: ' . ($errors[0]->message ?? 'parse error'));
        }

        return $xml;
    }

    // ─── CrossRef ─────────────────────────────────────────────────────────────

    public function testCrossrefByIssn(string $issn): array
    {
        try {
            $data = $this->crossrefGet('https://api.crossref.org/works', [
                'filter' => "issn:{$issn}",
                'rows'   => 1,
                'select' => 'DOI,title',
            ]);
            $total = $data['message']['total-results'] ?? 0;
            if ($total === 0) {
                return ['ok' => false, 'message' => "ISSN {$issn} tidak ditemukan di CrossRef."];
            }
            return ['ok' => true, 'message' => "Ditemukan {$total} artikel di CrossRef untuk ISSN {$issn}."];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function importFromCrossref(string $issn, callable $progress = null): void
    {
        $this->info("=== Mulai Import via CrossRef (ISSN: {$issn}) ===");

        $cursor = '*';
        $page   = 0;

        do {
            $data = $this->crossrefGet('https://api.crossref.org/works', [
                'filter' => "issn:{$issn}",
                'rows'   => 100,
                'cursor' => $cursor,
            ]);

            $items = $data['message']['items'] ?? [];
            $count = 0;

            foreach ($items as $item) {
                try {
                    $this->processCrossrefItem($item);
                    $count++;
                } catch (\Throwable $e) {
                    $this->errors++;
                    $this->error('CrossRef item ' . ($item['DOI'] ?? '?') . ': ' . $e->getMessage());
                }
                if ($progress) $progress('crossref', $item);
            }

            $page++;
            $this->info("  Halaman {$page}: {$count} artikel diproses.");

            $cursor = $data['message']['next-cursor'] ?? null;
        } while ($cursor && count($items) > 0);

        $this->info("=== Selesai. Artikel: +{$this->articlesCreated} diperbarui: {$this->articlesUpdated} | Error: {$this->errors} ===");
    }

    private function processCrossrefItem(array $item): void
    {
        $doi = $item['DOI'] ?? null;
        if (!$doi) return;

        $doi = 'https://doi.org/' . ltrim($doi, '/');

        $title = '';
        if (!empty($item['title'])) {
            $title = is_array($item['title']) ? ($item['title'][0] ?? '') : $item['title'];
        }
        $title = trim(strip_tags($title));
        if (!$title) return;

        // Date
        $dateParts = $item['published']['date-parts'][0]
            ?? $item['published-print']['date-parts'][0]
            ?? $item['issued']['date-parts'][0]
            ?? [];
        $date  = null;
        $year  = null;
        if (count($dateParts) >= 1) {
            $year = (int)$dateParts[0];
            $month = isset($dateParts[1]) ? str_pad((string)$dateParts[1], 2, '0', STR_PAD_LEFT) : '01';
            $day   = isset($dateParts[2]) ? str_pad((string)$dateParts[2], 2, '0', STR_PAD_LEFT) : '01';
            $date  = "{$year}-{$month}-{$day}";
        }

        $volume   = $item['volume'] ?? null;
        $number   = $item['issue']  ?? null;
        $pages    = $item['page']   ?? null;
        $abstract = isset($item['abstract']) ? $this->cleanJatsAbstract((string)$item['abstract']) : null;
        $language = $item['language'] ?? 'id';

        // Subjects/keywords
        $subjects = $item['subject'] ?? [];

        // Resolve or create issue
        $issue = null;
        if ($volume || $number || $year) {
            $issue = Issue::where('journal_id', $this->journalId)
                ->where(function ($q) use ($volume, $number, $year) {
                    if ($volume) $q->where('volume', $volume);
                    if ($number) $q->where('number', $number);
                    if ($year)   $q->where('year', $year);
                })->first();

            if (!$issue) {
                $issue = Issue::create([
                    'journal_id'  => $this->journalId,
                    'volume'      => $volume,
                    'number'      => $number,
                    'year'        => $year,
                    'published'   => true,
                    'show_volume' => true,
                    'show_number' => true,
                    'show_year'   => true,
                ]);
                $this->issuesCreated++;
                $this->info("  Edisi baru: Vol.{$volume} No.{$number} ({$year})");
            }
        }

        // PDF link
        $pdfUrl = null;
        foreach ($item['link'] ?? [] as $link) {
            if (($link['content-type'] ?? '') === 'application/pdf') {
                $pdfUrl = $link['URL'] ?? null;
                break;
            }
        }
        if (!$pdfUrl) {
            // Construct likely OJS download URL from DOI
            foreach ($item['link'] ?? [] as $link) {
                $url = $link['URL'] ?? '';
                if (str_contains($url, '/article/view/')) {
                    $pdfUrl = $url;
                    break;
                }
            }
        }

        // Existing check by DOI
        $existing = Submission::where('journal_id', $this->journalId)->where('doi', $doi)->first();

        $submissionData = [
            'journal_id'      => $this->journalId,
            'user_id'         => $this->importUserId,
            'status'          => 'published',
            'title'           => $title,
            'abstract'        => $abstract,
            'keywords'        => $subjects ?: null,
            'locale'          => $language,
            'doi'             => $doi,
            'submission_type' => 'article',
            'submitted_at'    => $date,
        ];

        DB::transaction(function () use ($existing, $submissionData, $issue, $item, $pages, $pdfUrl, $language, $date) {
            if ($existing) {
                $existing->update($submissionData);
                $submission = $existing;
                $this->articlesUpdated++;
            } else {
                $submission = Submission::create($submissionData);
                $this->articlesCreated++;
                $this->info("  Artikel: {$submissionData['title']}");
            }

            $article = Article::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'submission_id'  => $submission->id,
                    'issue_id'       => $issue?->id,
                    'journal_id'     => $this->journalId,
                    'doi'            => $submissionData['doi'],
                    'pages'          => $pages,
                    'date_published' => $date,
                    'access_status'  => 'open',
                ]
            );

            // Authors
            if ($existing) $submission->contributors()->delete();
            foreach (($item['author'] ?? []) as $idx => $author) {
                $orcid = isset($author['ORCID'])
                    ? preg_replace('/^https?:\/\/orcid\.org\//', '', $author['ORCID'])
                    : null;
                $affil = '';
                if (!empty($author['affiliation'])) {
                    $affil = $author['affiliation'][0]['name'] ?? '';
                }

                SubmissionContributor::create([
                    'submission_id'     => $submission->id,
                    'first_name'        => $author['given']  ?? '',
                    'last_name'         => $author['family'] ?? '',
                    'affiliation'       => $affil ?: null,
                    'orcid'             => $orcid,
                    'sequence'          => $idx,
                    'primary_contact'   => ($idx === 0),
                    'include_in_browse' => true,
                ]);
            }

            // Galley
            if ($pdfUrl) {
                if ($existing) $article->galleys()->delete();
                ArticleGalley::create([
                    'article_id'  => $article->id,
                    'label'       => 'PDF',
                    'locale'      => $language,
                    'remote_url'  => $pdfUrl,
                    'sequence'    => 0,
                    'is_approved' => true,
                ]);
            }
        });
    }

    private function cleanJatsAbstract(string $jats): string
    {
        // Remove JATS XML tags but keep text
        $text = preg_replace('/<jats:[^>]+>/i', '', $jats);
        $text = preg_replace('/<\/jats:[^>]+>/i', ' ', $text);
        $text = strip_tags($text);
        return trim((string)preg_replace('/\s+/', ' ', $text));
    }

    private function crossrefGet(string $url, array $params = []): array
    {
        // Add mailto for polite pool (faster rate limits)
        $email = config('mail.from.address', 'admin@example.com');
        $params['mailto'] = $email;

        $req = Http::timeout(30)
            ->connectTimeout($this->connectTimeout)
            ->withHeaders(['User-Agent' => "GJS-CrossRef-Harvester/1.0 (mailto:{$email})"]);
        if (!$this->sslVerify) $req = $req->withoutVerifying();
        $response = $req->get($url, $params);

        if ($response->failed()) {
            throw new \RuntimeException("HTTP {$response->status()} dari CrossRef API");
        }

        return $response->json() ?? [];
    }

    // ─── OJS REST API upsert helpers (unchanged from original) ───────────────

    private function upsertSection(array $s): ?Section
    {
        $ojsId  = $s['id'] ?? null;
        $title  = $this->locale($s['title'] ?? $s['abbreviation'] ?? '');
        $abbrev = $this->locale($s['abbreviation'] ?? $s['title'] ?? '');
        if (!$title) return null;

        $section = Section::where('journal_id', $this->journalId)
            ->where(fn($q) => $q->where('ojs_id', $ojsId)->orWhere('title', $title))
            ->first();

        if (!$section) {
            $section = Section::create([
                'journal_id' => $this->journalId,
                'ojs_id'     => $ojsId,
                'title'      => $title,
                'abbrev'     => $abbrev ?: $title,
                'sequence'   => $s['seq'] ?? 0,
            ]);
            $this->sectionsCreated++;
            $this->info("  Seksi: {$title}");
        } elseif ($ojsId && !$section->ojs_id) {
            $section->update(['ojs_id' => $ojsId]);
        }

        return $section;
    }

    private function upsertIssue(array $i): ?Issue
    {
        $ojsId  = $i['id']     ?? null;
        $volume = $i['volume'] ?? null;
        $number = $i['number'] ?? null;
        $year   = $i['year']   ?? null;
        $title  = $this->locale($i['title'] ?? []);

        $issue = Issue::where('journal_id', $this->journalId)
            ->where(fn($q) => $q->where('ojs_id', $ojsId)
                ->orWhere(fn($q2) => $q2->where('volume', $volume)->where('number', $number)->where('year', $year))
            )->first();

        $data = [
            'journal_id'    => $this->journalId,
            'ojs_id'        => $ojsId,
            'volume'        => $volume,
            'number'        => $number,
            'year'          => $year,
            'title'         => $title ?: null,
            'description'   => $this->locale($i['description'] ?? []),
            'published'     => (bool)($i['published'] ?? false),
            'current'       => (bool)($i['isCurrent'] ?? false),
            'show_volume'   => (bool)($i['showVolume'] ?? true),
            'show_number'   => (bool)($i['showNumber'] ?? true),
            'show_year'     => (bool)($i['showYear']   ?? true),
            'show_title'    => (bool)($i['showTitle']  ?? false),
            'date_published' => $i['datePublished'] ?? null,
        ];

        if (!$issue) {
            $issue = Issue::create($data);
            $this->issuesCreated++;
            $this->info("  Edisi: Vol.{$volume} No.{$number} ({$year})");
        } else {
            $issue->update($data);
            $this->issuesSkipped++;
        }

        return $issue;
    }

    private function upsertSubmission(array $sub): void
    {
        $ojsId = $sub['id'] ?? null;
        $pub   = null;

        if (!empty($sub['publications'])) {
            $currentPubId = $sub['currentPublicationId'] ?? null;
            foreach ($sub['publications'] as $p) {
                if ($currentPubId && ($p['id'] ?? null) == $currentPubId) { $pub = $p; break; }
            }
            $pub = $pub ?? end($sub['publications']);
        } else {
            $pub = $sub;
        }

        if (!$pub) return;

        $locale       = $sub['locale'] ?? 'en';
        $title        = $this->locale($pub['title'] ?? [], $locale);
        $doi          = $pub['pub-id::doi'] ?? $pub['doi'] ?? $sub['doi'] ?? null;
        $date         = $pub['datePublished'] ?? $sub['datePublished'] ?? null;
        $ojsSectionId = $pub['sectionId'] ?? $sub['sectionId'] ?? null;
        $ojsIssueId   = $pub['issueId']   ?? $sub['issueId']   ?? null;
        $seq          = (float)($pub['seq'] ?? $pub['sequence'] ?? 0);

        if (!$title) return;

        $section = $ojsSectionId
            ? Section::where('journal_id', $this->journalId)->where('ojs_id', $ojsSectionId)->first()
            : null;

        $issue = $ojsIssueId
            ? Issue::where('journal_id', $this->journalId)->where('ojs_id', $ojsIssueId)->first()
            : null;

        $existing = null;
        if ($ojsId) {
            $existing = Submission::where('journal_id', $this->journalId)->where('ojs_id', $ojsId)->first();
        }
        if (!$existing && $doi) {
            $existing = Submission::where('doi', $doi)->first();
        }

        $keywords = $this->localeArray($pub['keywords'] ?? [], $locale);

        $submissionData = [
            'journal_id'      => $this->journalId,
            'section_id'      => $section?->id,
            'user_id'         => $this->importUserId,
            'status'          => 'published',
            'title'           => $title,
            'subtitle'        => $this->locale($pub['subtitle'] ?? [], $locale) ?: null,
            'abstract'        => $this->locale($pub['abstract'] ?? [], $locale) ?: null,
            'keywords'        => $keywords ?: null,
            'locale'          => $locale,
            'doi'             => $doi,
            'submission_type' => 'article',
            'ojs_id'          => $ojsId,
            'ojs_source_url'  => $this->restBaseUrl,
            'submitted_at'    => $date,
        ];

        DB::transaction(function () use ($existing, $submissionData, $issue, $pub, $locale, $seq, $date, $doi) {
            if ($existing) {
                $existing->update($submissionData);
                $submission = $existing;
                $this->articlesUpdated++;
            } else {
                $submission = Submission::create($submissionData);
                $this->articlesCreated++;
                $this->info("  Artikel: {$submissionData['title']}");
            }

            $article = Article::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'submission_id'  => $submission->id,
                    'issue_id'       => $issue?->id,
                    'journal_id'     => $this->journalId,
                    'section_id'     => $submissionData['section_id'],
                    'doi'            => $doi,
                    'pages'          => $pub['pages'] ?? null,
                    'sequence'       => $seq,
                    'date_published' => $date,
                    'access_status'  => 'open',
                ]
            );

            if ($existing) $submission->contributors()->delete();
            foreach (($pub['authors'] ?? []) as $idx => $author) {
                SubmissionContributor::create([
                    'submission_id'     => $submission->id,
                    'first_name'        => $this->locale($author['givenName']  ?? [], $locale),
                    'last_name'         => $this->locale($author['familyName'] ?? [], $locale),
                    'email'             => $author['email'] ?? null,
                    'affiliation'       => $this->locale($author['affiliation'] ?? [], $locale) ?: null,
                    'country'           => $author['country'] ?? null,
                    'orcid'             => isset($author['orcid']) ? preg_replace('/^https?:\/\/orcid\.org\//', '', $author['orcid']) : null,
                    'sequence'          => (int)($author['seq'] ?? $idx),
                    'primary_contact'   => (bool)($author['primaryContact'] ?? ($idx === 0)),
                    'include_in_browse' => (bool)($author['includeInBrowse'] ?? true),
                ]);
            }

            if ($existing) $article->galleys()->delete();
            foreach (($pub['galleys'] ?? []) as $gIdx => $galley) {
                ArticleGalley::create([
                    'article_id'  => $article->id,
                    'label'       => $galley['label'] ?? 'PDF',
                    'locale'      => $galley['locale'] ?? $locale,
                    'remote_url'  => $galley['file']['url'] ?? $galley['urlRemote'] ?? null,
                    'sequence'    => $gIdx,
                    'is_approved' => true,
                ]);
            }
        });
    }

    // ─── Locale helpers ──────────────────────────────────────────────────────

    private function locale(mixed $val, string $prefer = 'id'): string
    {
        if (is_string($val)) return trim($val);
        if (!is_array($val) || empty($val)) return '';
        return trim($val[$prefer] ?? $val['en'] ?? $val['id'] ?? reset($val) ?? '');
    }

    private function localeArray(mixed $val, string $prefer = 'id'): array
    {
        if (is_array($val) && isset($val[$prefer])) return (array)$val[$prefer];
        if (is_array($val) && isset($val['en']))     return (array)$val['en'];
        if (is_array($val) && isset($val['id']))     return (array)$val['id'];
        if (is_array($val)) { $first = reset($val); return is_array($first) ? $first : (array)$first; }
        return [];
    }

    // ─── Log helpers ─────────────────────────────────────────────────────────

    public function getLogs(): array { return $this->log; }

    private function info(string $msg): void
    {
        $this->log[] = ['level' => 'info', 'msg' => $msg, 'time' => now()->toTimeString()];
    }

    private function warn(string $msg): void
    {
        $this->log[] = ['level' => 'warn', 'msg' => $msg, 'time' => now()->toTimeString()];
    }

    private function error(string $msg): void
    {
        $this->log[] = ['level' => 'error', 'msg' => $msg, 'time' => now()->toTimeString()];
    }
}
