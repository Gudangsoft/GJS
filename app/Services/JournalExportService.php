<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Collection;

class JournalExportService
{
    protected Journal $journal;
    protected Collection $issues;
    protected Collection $articles;

    public function __construct(int $journalId, ?int $issueId = null)
    {
        $this->journal = Journal::findOrFail($journalId);

        $query = Issue::where('journal_id', $journalId)
            ->where('published', true)
            ->with(['articles.submission.contributors', 'articles.galleys'])
            ->orderByDesc('date_published');

        if ($issueId) {
            $query->where('id', $issueId);
        }

        $this->issues   = $query->get();
        $this->articles = $this->issues->flatMap(fn($i) => $i->articles);
    }

    // ── CrossRef XML Export ────────────────────────────────────────────────────

    public function crossref(): string
    {
        $j   = $this->journal;
        $now = now();
        $ts  = $now->format('YmdHis');

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElementNS('http://www.crossref.org/schema/5.3.1', 'doi_batch');
        $root->setAttribute('version', '5.3.1');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.crossref.org/schema/5.3.1 https://www.crossref.org/schemas/crossref5.3.1.xsd');
        $dom->appendChild($root);

        // head
        $head = $dom->createElement('head');
        $root->appendChild($head);
        $head->appendChild($dom->createElement('doi_batch_id', 'gjs-' . $ts));
        $head->appendChild($dom->createElement('timestamp', $ts));
        $dep = $dom->createElement('depositor');
        $head->appendChild($dep);
        $dep->appendChild($dom->createElement('depositor_name', $j->publisher ?? $j->name));
        $dep->appendChild($dom->createElement('email_address', $j->email ?? 'admin@example.com'));
        $head->appendChild($dom->createElement('registrant', $j->publisher ?? $j->name));

        // body
        $body = $dom->createElement('body');
        $root->appendChild($body);

        foreach ($this->issues as $issue) {
            $journal = $dom->createElement('journal');
            $body->appendChild($journal);

            // journal_metadata
            $jm = $dom->createElement('journal_metadata');
            $jm->setAttribute('language', substr($j->primary_locale ?? 'id', 0, 2));
            $journal->appendChild($jm);
            $jm->appendChild($dom->createElement('full_title', htmlspecialchars($j->name)));
            if ($j->name_abbrev) $jm->appendChild($dom->createElement('abbrev_title', $j->name_abbrev));
            if ($j->issn_print)  { $el = $dom->createElement('issn', $j->issn_print);  $el->setAttribute('media_type', 'print');       $jm->appendChild($el); }
            if ($j->issn_online) { $el = $dom->createElement('issn', $j->issn_online); $el->setAttribute('media_type', 'electronic');  $jm->appendChild($el); }

            // journal_issue
            $ji = $dom->createElement('journal_issue');
            $journal->appendChild($ji);
            if ($issue->date_published) {
                $pd = $dom->createElement('publication_date');
                $pd->setAttribute('media_type', 'online');
                $ji->appendChild($pd);
                $pd->appendChild($dom->createElement('year', $issue->date_published->year));
            }
            if ($issue->volume) {
                $jv = $dom->createElement('journal_volume');
                $ji->appendChild($jv);
                $jv->appendChild($dom->createElement('volume', $issue->volume));
            }
            if ($issue->number) $ji->appendChild($dom->createElement('issue', $issue->number));

            // articles
            foreach ($issue->articles as $article) {
                $sub = $article->submission;
                if (!$sub || !$article->doi) continue;

                $ja = $dom->createElement('journal_article');
                $ja->setAttribute('publication_type', 'full_text');
                $journal->appendChild($ja);

                // titles
                $titles = $dom->createElement('titles');
                $ja->appendChild($titles);
                $titles->appendChild($dom->createElement('title', htmlspecialchars($sub->title ?? '')));

                // contributors
                $contributors = $sub->contributors ?? collect();
                if ($contributors->count()) {
                    $contribs = $dom->createElement('contributors');
                    $ja->appendChild($contribs);
                    foreach ($contributors as $idx => $c) {
                        $pn = $dom->createElement('person_name');
                        $pn->setAttribute('sequence', $idx === 0 ? 'first' : 'additional');
                        $pn->setAttribute('contributor_role', 'author');
                        $contribs->appendChild($pn);
                        if ($c->first_name) $pn->appendChild($dom->createElement('given_name', htmlspecialchars($c->first_name)));
                        $pn->appendChild($dom->createElement('surname', htmlspecialchars($c->last_name ?? '')));
                        if ($c->orcid) {
                            $orcid = $dom->createElement('ORCID', 'https://orcid.org/' . $c->orcid);
                            $orcid->setAttribute('authenticated', 'false');
                            $pn->appendChild($orcid);
                        }
                    }
                }

                // publication date
                $pubDate = $article->date_published ?? $issue->date_published;
                if ($pubDate) {
                    $pd2 = $dom->createElement('publication_date');
                    $pd2->setAttribute('media_type', 'online');
                    $ja->appendChild($pd2);
                    $pd2->appendChild($dom->createElement('month', str_pad($pubDate->month, 2, '0', STR_PAD_LEFT)));
                    $pd2->appendChild($dom->createElement('day',   str_pad($pubDate->day,   2, '0', STR_PAD_LEFT)));
                    $pd2->appendChild($dom->createElement('year',  $pubDate->year));
                }

                // pages
                if ($article->pages) {
                    [$fp, $lp] = $this->parsePages($article->pages);
                    if ($fp) {
                        $pages = $dom->createElement('pages');
                        $ja->appendChild($pages);
                        $pages->appendChild($dom->createElement('first_page', $fp));
                        if ($lp) $pages->appendChild($dom->createElement('last_page', $lp));
                    }
                }

                // doi_data
                $doiData = $dom->createElement('doi_data');
                $ja->appendChild($doiData);
                $doiData->appendChild($dom->createElement('doi', $article->doi));
                $doiData->appendChild($dom->createElement('resource', route('reader.article', [$this->journal->slug, $article->id])));
            }
        }

        return $dom->saveXML();
    }

    // ── DOAJ Export XML ────────────────────────────────────────────────────────

    public function doaj(): string
    {
        $j   = $this->journal;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $records = $dom->createElement('records');
        $dom->appendChild($records);

        foreach ($this->articles as $article) {
            $sub = $article->submission;
            if (!$sub) continue;

            $rec = $dom->createElement('record');
            $records->appendChild($rec);

            $lang = strtoupper(substr($sub->locale ?? $j->primary_locale ?? 'id', 0, 2));
            $rec->appendChild($dom->createElement('language', $lang));
            $rec->appendChild($dom->createElement('publisher', htmlspecialchars($j->publisher ?? $j->name)));
            $rec->appendChild($dom->createElement('journalTitle', htmlspecialchars($j->name)));
            if ($j->issn_print)  $rec->appendChild($dom->createElement('issn',  $j->issn_print));
            if ($j->issn_online) $rec->appendChild($dom->createElement('eissn', $j->issn_online));

            $pubDate = $article->date_published ?? $article->issue?->date_published;
            if ($pubDate) $rec->appendChild($dom->createElement('publicationDate', $pubDate->toDateString()));

            $issue = $article->issue;
            if ($issue?->volume) $rec->appendChild($dom->createElement('volume', $issue->volume));
            if ($issue?->number) $rec->appendChild($dom->createElement('issue',  $issue->number));

            if ($article->pages) {
                [$fp, $lp] = $this->parsePages($article->pages);
                if ($fp) $rec->appendChild($dom->createElement('startPage', $fp));
                if ($lp) $rec->appendChild($dom->createElement('endPage',   $lp));
            }

            if ($article->doi) $rec->appendChild($dom->createElement('doi', $article->doi));

            $titleEl = $dom->createElement('title');
            $titleEl->appendChild($dom->createCDATASection($sub->title ?? ''));
            $rec->appendChild($titleEl);

            $contributors = $sub->contributors ?? collect();
            if ($contributors->count()) {
                $authorsEl = $dom->createElement('authors');
                $rec->appendChild($authorsEl);
                foreach ($contributors as $c) {
                    $authorEl = $dom->createElement('author');
                    $authorsEl->appendChild($authorEl);
                    $authorEl->appendChild($dom->createElement('name', htmlspecialchars(trim("{$c->first_name} {$c->last_name}"))));
                    if ($c->affiliation) $authorEl->appendChild($dom->createElement('affiliations', htmlspecialchars($c->affiliation)));
                    if ($c->orcid)       $authorEl->appendChild($dom->createElement('orcid_id', $c->orcid));
                }
            }

            if ($sub->abstract) {
                $absEl = $dom->createElement('abstract');
                $absEl->appendChild($dom->createCDATASection($sub->abstract));
                $rec->appendChild($absEl);
            }

            // PDF galley URL
            $pdfGalley = $article->galleys?->first(fn($g) => str_contains(strtolower($g->label ?? ''), 'pdf'));
            if ($pdfGalley) {
                $url = $pdfGalley->remote_url ?? route('reader.galley', [$this->journal->slug, $article->id, $pdfGalley->id]);
                $fu = $dom->createElement('fullTextUrl', $url);
                $fu->setAttribute('format', 'pdf');
                $rec->appendChild($fu);
            }

            if (!empty($sub->keywords)) {
                $kwEl = $dom->createElement('keywords');
                $rec->appendChild($kwEl);
                foreach ((array)$sub->keywords as $kw) {
                    $kwEl->appendChild($dom->createElement('keyword', htmlspecialchars($kw)));
                }
            }
        }

        return $dom->saveXML();
    }

    // ── PubMed XML Export ──────────────────────────────────────────────────────

    public function pubmed(): string
    {
        $j = $this->journal;

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<!DOCTYPE ArticleSet PUBLIC "-//NLM//DTD PubMed 2.7//EN" "https://dtd.nlm.nih.gov/ncbi/pubmed/in/PubMed.dtd">' . "\n";
        $xml .= "<ArticleSet>\n";

        foreach ($this->articles as $article) {
            $sub   = $article->submission;
            $issue = $article->issue;
            if (!$sub) continue;

            $pubDate = $article->date_published ?? $issue?->date_published;

            $xml .= "  <Article>\n";
            $xml .= "    <Journal>\n";
            $xml .= '      <PublisherName>' . e($j->publisher ?? $j->name) . "</PublisherName>\n";
            $xml .= '      <JournalTitle>' . e($j->name) . "</JournalTitle>\n";
            if ($j->issn_print)  $xml .= '      <Issn>' . $j->issn_print  . "</Issn>\n";
            if ($j->issn_online) $xml .= '      <Eissn>' . $j->issn_online . "</Eissn>\n";
            if ($issue?->volume) $xml .= '      <Volume>' . $issue->volume . "</Volume>\n";
            if ($issue?->number) $xml .= '      <Issue>'  . $issue->number . "</Issue>\n";
            if ($pubDate) {
                $status = 'epublish';
                $xml .= "      <PubDate PubStatus=\"{$status}\">\n";
                $xml .= '        <Year>'  . $pubDate->year  . "</Year>\n";
                $xml .= '        <Month>' . $pubDate->format('M') . "</Month>\n";
                $xml .= '        <Day>'   . str_pad($pubDate->day, 2, '0', STR_PAD_LEFT) . "</Day>\n";
                $xml .= "      </PubDate>\n";
            }
            $xml .= "    </Journal>\n";

            $xml .= '    <ArticleTitle>' . e($sub->title ?? '') . "</ArticleTitle>\n";

            if ($article->pages) {
                [$fp, $lp] = $this->parsePages($article->pages);
                if ($fp) $xml .= '    <FirstPage>' . $fp . "</FirstPage>\n";
                if ($lp) $xml .= '    <LastPage>'  . $lp . "</LastPage>\n";
            }

            $lang = strtolower(substr($sub->locale ?? $j->primary_locale ?? 'id', 0, 2));
            $xml .= '    <Language>' . $lang . "</Language>\n";

            $contributors = $sub->contributors ?? collect();
            if ($contributors->count()) {
                $xml .= "    <AuthorList>\n";
                foreach ($contributors as $c) {
                    $xml .= "      <Author>\n";
                    $xml .= '        <FirstName>' . e($c->first_name ?? '') . "</FirstName>\n";
                    $xml .= '        <LastName>'  . e($c->last_name  ?? '') . "</LastName>\n";
                    if ($c->affiliation) $xml .= '        <Affiliation>' . e($c->affiliation) . "</Affiliation>\n";
                    if ($c->orcid)       $xml .= '        <Identifier Source="ORCID">' . $c->orcid . "</Identifier>\n";
                    $xml .= "      </Author>\n";
                }
                $xml .= "    </AuthorList>\n";
            }

            if ($sub->abstract) {
                $xml .= '    <Abstract>' . e($sub->abstract) . "</Abstract>\n";
            }

            if (!empty($sub->keywords)) {
                $xml .= "    <ObjectList>\n";
                foreach ((array)$sub->keywords as $kw) {
                    $xml .= "      <Object Type=\"keyword\">\n";
                    $xml .= '        <Param Name="value">' . e($kw) . "</Param>\n";
                    $xml .= "      </Object>\n";
                }
                $xml .= "    </ObjectList>\n";
            }

            $xml .= "    <ArticleIdList>\n";
            if ($article->doi) $xml .= '      <ArticleId IdType="doi">' . $article->doi . "</ArticleId>\n";
            $xml .= '      <ArticleId IdType="pii">' . $article->id . "</ArticleId>\n";
            $xml .= "    </ArticleIdList>\n";

            $xml .= "  </Article>\n";
        }

        $xml .= "</ArticleSet>\n";
        return $xml;
    }

    // ── DataCite Export XML ────────────────────────────────────────────────────

    public function datacite(): string
    {
        $j   = $this->journal;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $resources = $dom->createElement('resources');
        $resources->setAttribute('xmlns', 'http://datacite.org/schema/kernel-4');
        $resources->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $resources->setAttribute('xsi:schemaLocation', 'http://datacite.org/schema/kernel-4 https://schema.datacite.org/meta/kernel-4.5/metadata.xsd');
        $dom->appendChild($resources);

        foreach ($this->articles as $article) {
            $sub   = $article->submission;
            $issue = $article->issue;
            if (!$sub) continue;

            $res = $dom->createElement('resource');
            $resources->appendChild($res);

            if ($article->doi) {
                $id = $dom->createElement('identifier', $article->doi);
                $id->setAttribute('identifierType', 'DOI');
                $res->appendChild($id);
            }

            // creators
            $contributors = $sub->contributors ?? collect();
            if ($contributors->count()) {
                $creators = $dom->createElement('creators');
                $res->appendChild($creators);
                foreach ($contributors as $c) {
                    $creator = $dom->createElement('creator');
                    $creators->appendChild($creator);
                    $fullName = trim("{$c->last_name}, {$c->first_name}");
                    $cn = $dom->createElement('creatorName', htmlspecialchars($fullName));
                    $cn->setAttribute('nameType', 'Personal');
                    $creator->appendChild($cn);
                    if ($c->first_name) $creator->appendChild($dom->createElement('givenName', htmlspecialchars($c->first_name)));
                    if ($c->last_name)  $creator->appendChild($dom->createElement('familyName', htmlspecialchars($c->last_name)));
                    if ($c->orcid) {
                        $ni = $dom->createElement('nameIdentifier', $c->orcid);
                        $ni->setAttribute('nameIdentifierScheme', 'ORCID');
                        $ni->setAttribute('schemeURI', 'https://orcid.org');
                        $creator->appendChild($ni);
                    }
                    if ($c->affiliation) {
                        $aff = $dom->createElement('affiliation', htmlspecialchars($c->affiliation));
                        $creator->appendChild($aff);
                    }
                }
            }

            $titles = $dom->createElement('titles');
            $res->appendChild($titles);
            $title = $dom->createElement('title', htmlspecialchars($sub->title ?? ''));
            $titles->appendChild($title);

            $res->appendChild($dom->createElement('publisher', htmlspecialchars($j->publisher ?? $j->name)));

            $pubDate = $article->date_published ?? $issue?->date_published;
            if ($pubDate) {
                $res->appendChild($dom->createElement('publicationYear', $pubDate->year));
                $dates = $dom->createElement('dates');
                $res->appendChild($dates);
                $dateEl = $dom->createElement('date', $pubDate->toDateString());
                $dateEl->setAttribute('dateType', 'Issued');
                $dates->appendChild($dateEl);
            }

            $rt = $dom->createElement('resourceType', 'Journal Article');
            $rt->setAttribute('resourceTypeGeneral', 'Text');
            $res->appendChild($rt);

            if ($sub->abstract) {
                $descs = $dom->createElement('descriptions');
                $res->appendChild($descs);
                $desc = $dom->createElement('description', htmlspecialchars($sub->abstract));
                $desc->setAttribute('descriptionType', 'Abstract');
                $descs->appendChild($desc);
            }

            if (!empty($sub->keywords)) {
                $subjects = $dom->createElement('subjects');
                $res->appendChild($subjects);
                foreach ((array)$sub->keywords as $kw) {
                    $subjects->appendChild($dom->createElement('subject', htmlspecialchars($kw)));
                }
            }

            // related identifiers (journal ISSN)
            $rels = $dom->createElement('relatedIdentifiers');
            $res->appendChild($rels);
            if ($j->issn_print) {
                $ri = $dom->createElement('relatedIdentifier', $j->issn_print);
                $ri->setAttribute('relatedIdentifierType', 'ISSN');
                $ri->setAttribute('relationType', 'IsPartOf');
                $ri->setAttribute('resourceTypeGeneral', 'Collection');
                $rels->appendChild($ri);
            }
            if ($j->issn_online) {
                $ri = $dom->createElement('relatedIdentifier', $j->issn_online);
                $ri->setAttribute('relatedIdentifierType', 'ISSN');
                $ri->setAttribute('relationType', 'IsPartOf');
                $ri->setAttribute('resourceTypeGeneral', 'Collection');
                $rels->appendChild($ri);
            }
        }

        return $dom->saveXML();
    }

    // ── Native XML Export (OJS-compatible) ────────────────────────────────────

    public function nativeXml(): string
    {
        $j   = $this->journal;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('issues');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($root);

        foreach ($this->issues as $issue) {
            $issueEl = $dom->createElement('issue');
            $root->appendChild($issueEl);

            foreach (['volume' => $issue->volume, 'number' => $issue->number, 'year' => $issue->year] as $field => $val) {
                if ($val !== null) $issueEl->appendChild($dom->createElement($field, $val));
            }
            if ($issue->date_published) {
                $issueEl->appendChild($dom->createElement('date_published', $issue->date_published->toDateString()));
            }

            $articlesEl = $dom->createElement('articles');
            $issueEl->appendChild($articlesEl);

            foreach ($issue->articles as $article) {
                $sub = $article->submission;
                if (!$sub) continue;

                $artEl = $dom->createElement('article');
                $articlesEl->appendChild($artEl);

                $artEl->appendChild($dom->createElement('id',    $article->id));
                if ($article->doi) $artEl->appendChild($dom->createElement('doi', $article->doi));
                if ($article->pages) $artEl->appendChild($dom->createElement('pages', $article->pages));
                if ($article->sequence) $artEl->appendChild($dom->createElement('seq', $article->sequence));

                $pubDate = $article->date_published ?? $issue->date_published;
                if ($pubDate) $artEl->appendChild($dom->createElement('date_published', $pubDate->toDateString()));

                $titleEl = $dom->createElement('title');
                $titleEl->appendChild($dom->createCDATASection($sub->title ?? ''));
                $artEl->appendChild($titleEl);

                if ($sub->abstract) {
                    $absEl = $dom->createElement('abstract');
                    $absEl->appendChild($dom->createCDATASection($sub->abstract));
                    $artEl->appendChild($absEl);
                }

                if (!empty($sub->keywords)) {
                    $kwEl = $dom->createElement('keywords');
                    $artEl->appendChild($kwEl);
                    foreach ((array)$sub->keywords as $kw) {
                        $kwEl->appendChild($dom->createElement('keyword', htmlspecialchars($kw)));
                    }
                }

                $contributors = $sub->contributors ?? collect();
                if ($contributors->count()) {
                    $authorsEl = $dom->createElement('authors');
                    $artEl->appendChild($authorsEl);
                    foreach ($contributors as $c) {
                        $authorEl = $dom->createElement('author');
                        $authorsEl->appendChild($authorEl);
                        if ($c->first_name)  $authorEl->appendChild($dom->createElement('firstname',   htmlspecialchars($c->first_name)));
                        if ($c->last_name)   $authorEl->appendChild($dom->createElement('lastname',    htmlspecialchars($c->last_name)));
                        if ($c->email)       $authorEl->appendChild($dom->createElement('email',       htmlspecialchars($c->email)));
                        if ($c->affiliation) $authorEl->appendChild($dom->createElement('affiliation', htmlspecialchars($c->affiliation)));
                        if ($c->country)     $authorEl->appendChild($dom->createElement('country',     $c->country));
                        if ($c->orcid)       $authorEl->appendChild($dom->createElement('orcid',       $c->orcid));
                    }
                }
            }
        }

        return $dom->saveXML();
    }

    // ── Copernicus Export XML ──────────────────────────────────────────────────

    public function copernicus(): string
    {
        $j   = $this->journal;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('indexation');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($root);

        // journal metadata
        $jEl = $dom->createElement('journal');
        $root->appendChild($jEl);
        $jEl->appendChild($dom->createElement('title',     htmlspecialchars($j->name)));
        $jEl->appendChild($dom->createElement('abbr',      $j->name_abbrev ?? ''));
        $jEl->appendChild($dom->createElement('publisher', htmlspecialchars($j->publisher ?? '')));
        $jEl->appendChild($dom->createElement('email',     $j->email ?? ''));
        if ($j->issn_print)  $jEl->appendChild($dom->createElement('pissn', $j->issn_print));
        if ($j->issn_online) $jEl->appendChild($dom->createElement('eissn', $j->issn_online));

        $pubs = $dom->createElement('publications');
        $root->appendChild($pubs);

        foreach ($this->issues as $issue) {
            $pubEl = $dom->createElement('publication');
            $pubs->appendChild($pubEl);

            $pubEl->appendChild($dom->createElement('volume', $issue->volume ?? ''));
            $pubEl->appendChild($dom->createElement('number', $issue->number ?? ''));
            $pubEl->appendChild($dom->createElement('year',   $issue->year   ?? ''));

            $articlesEl = $dom->createElement('articles');
            $pubEl->appendChild($articlesEl);

            foreach ($issue->articles as $article) {
                $sub = $article->submission;
                if (!$sub) continue;

                $artEl = $dom->createElement('article');
                $articlesEl->appendChild($artEl);

                $titleEl = $dom->createElement('title');
                $titleEl->appendChild($dom->createCDATASection($sub->title ?? ''));
                $artEl->appendChild($titleEl);

                $contributors = $sub->contributors ?? collect();
                if ($contributors->count()) {
                    $authorsEl = $dom->createElement('authors');
                    $artEl->appendChild($authorsEl);
                    foreach ($contributors as $c) {
                        $nameEl = $dom->createElement('author', htmlspecialchars(trim("{$c->first_name} {$c->last_name}")));
                        if ($c->affiliation) $nameEl->setAttribute('affil', htmlspecialchars($c->affiliation));
                        $authorsEl->appendChild($nameEl);
                    }
                }

                if ($sub->abstract) {
                    $absEl = $dom->createElement('abstract');
                    $absEl->appendChild($dom->createCDATASection($sub->abstract));
                    $artEl->appendChild($absEl);
                }

                if ($article->pages) $artEl->appendChild($dom->createElement('pages', $article->pages));
                if ($article->doi)   $artEl->appendChild($dom->createElement('doi',   $article->doi));

                if (!empty($sub->keywords)) {
                    $kwEl = $dom->createElement('keywords');
                    $artEl->appendChild($kwEl);
                    foreach ((array)$sub->keywords as $kw) {
                        $kwEl->appendChild($dom->createElement('keyword', htmlspecialchars($kw)));
                    }
                }
            }
        }

        return $dom->saveXML();
    }

    // ── Users XML Export ───────────────────────────────────────────────────────

    public function usersXml(): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('PKPUsers');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($root);

        // Journal members (managers + editors)
        $memberIds = collect();
        $memberIds = $memberIds->merge($this->journal->managers()->pluck('users.id'));
        $memberIds = $memberIds->merge($this->journal->editors()->pluck('users.id'));

        $users = User::whereIn('id', $memberIds->unique())->orderBy('last_name')->get();

        foreach ($users as $user) {
            $userEl = $dom->createElement('user');
            $root->appendChild($userEl);

            $userEl->appendChild($dom->createElement('username', htmlspecialchars($user->email)));

            if ($user->first_name) {
                $gn = $dom->createElement('givenname', htmlspecialchars($user->first_name));
                $gn->setAttribute('locale', $user->locale ?? 'id');
                $userEl->appendChild($gn);
            }
            if ($user->last_name) {
                $fn = $dom->createElement('familyname', htmlspecialchars($user->last_name));
                $fn->setAttribute('locale', $user->locale ?? 'id');
                $userEl->appendChild($fn);
            }

            $userEl->appendChild($dom->createElement('email', htmlspecialchars($user->email)));

            if ($user->affiliation) $userEl->appendChild($dom->createElement('affiliation', htmlspecialchars($user->affiliation)));
            if ($user->country)     $userEl->appendChild($dom->createElement('country', $user->country));
            if ($user->orcid)       $userEl->appendChild($dom->createElement('orcid', $user->orcid));

            $roles = $dom->createElement('roles');
            $userEl->appendChild($roles);
            foreach ($user->getRoleNames() as $roleName) {
                $roles->appendChild($dom->createElement('role', $roleName));
            }

            $userEl->appendChild($dom->createElement('registered', $user->created_at->toDateString()));
        }

        return $dom->saveXML();
    }

    // ── Helper ─────────────────────────────────────────────────────────────────

    protected function parsePages(string $pages): array
    {
        if (str_contains($pages, '-')) {
            [$first, $last] = explode('-', $pages, 2);
            return [trim($first), trim($last)];
        }
        return [trim($pages), ''];
    }
}
