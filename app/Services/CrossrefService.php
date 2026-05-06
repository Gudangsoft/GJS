<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrossrefService
{
    private string $depositUrl = 'https://api.crossref.org/deposits';

    public function registerDoi(Article $article): bool
    {
        $article->load(['submission.contributors', 'issue', 'journal', 'section']);

        if (!$article->doi) {
            Log::warning("CrossrefService: article #{$article->id} has no DOI set.");
            return false;
        }

        $xml  = $this->buildDepositXml($article);
        $user = config('crossref.user');
        $pass = config('crossref.password');

        if (!$user || !$pass) {
            Log::warning('CrossrefService: credentials not configured (CROSSREF_USER / CROSSREF_PASSWORD).');
            return false;
        }

        $response = Http::withBasicAuth($user, $pass)
            ->timeout(30)
            ->withHeaders(['Content-Type' => 'application/vnd.crossref.deposit+xml'])
            ->post($this->depositUrl, ['operation' => 'doQueryUpload', 'login_id' => $user, 'login_passwd' => $pass])
            ->withBody($xml, 'text/xml');

        // Crossref deposit is asynchronous — HTTP 200 means queued, not registered
        if (!$response->successful()) {
            Log::error("CrossrefService: deposit failed for article #{$article->id}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        Log::info("CrossrefService: DOI deposit queued for article #{$article->id} — {$article->doi}");
        return true;
    }

    private function buildDepositXml(Article $article): string
    {
        $j          = $article->journal;
        $sub        = $article->submission;
        $issue      = $article->issue;
        $batchId    = 'GJS-' . now()->format('YmdHis') . '-' . $article->id;
        $timestamp  = now()->format('YmdHis');
        $pubDate    = $article->date_published ?? now();
        $depositor  = config('crossref.depositor_name', 'GJS - Go Journal System');
        $depositorEmail = config('crossref.depositor_email', $j->email ?? 'crossref@gjs.id');
        $articleUrl = url("/journals/{$j->slug}/articles/{$article->id}");

        // Build contributors XML
        $contributorsXml = '';
        foreach ($sub->contributors as $i => $c) {
            $seq  = $i === 0 ? 'first' : 'additional';
            $contributorsXml .= <<<XML
                            <person_name sequence="{$seq}" contributor_role="author">
                                <given_name>{$this->esc($c->first_name)}</given_name>
                                <surname>{$this->esc($c->last_name)}</surname>
                                {$this->orcidXml($c->orcid)}
                            </person_name>
XML;
        }

        $issueNum    = $issue?->number ?? '';
        $volumeNum   = $issue?->volume ?? '';
        $issueYear   = $issue?->year ?? $pubDate->year;
        $issueMonth  = $pubDate->month;
        $issueDay    = $pubDate->day;

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doi_batch version="5.3.1"
           xmlns="http://www.crossref.org/schema/5.3.1"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://www.crossref.org/schema/5.3.1 https://www.crossref.org/schemas/crossref5.3.1.xsd">
    <head>
        <doi_batch_id>{$batchId}</doi_batch_id>
        <timestamp>{$timestamp}</timestamp>
        <depositor>
            <depositor_name>{$this->esc($depositor)}</depositor_name>
            <email_address>{$this->esc($depositorEmail)}</email_address>
        </depositor>
        <registrant>{$this->esc($depositor)}</registrant>
    </head>
    <body>
        <journal>
            <journal_metadata language="{$j->primary_locale}">
                <full_title>{$this->esc($j->name)}</full_title>
                <abbrev_title>{$this->esc($j->name_abbrev)}</abbrev_title>
                {$this->issnXml($j->issn_print, 'print')}
                {$this->issnXml($j->issn_online, 'electronic')}
            </journal_metadata>
            <journal_issue>
                <publication_date media_type="online">
                    <year>{$issueYear}</year>
                </publication_date>
                <journal_volume>
                    <volume>{$volumeNum}</volume>
                </journal_volume>
                <issue>{$issueNum}</issue>
            </journal_issue>
            <journal_article publication_type="full_text" language="{$sub->locale}">
                <titles>
                    <title>{$this->esc($sub->title)}</title>
                </titles>
                <contributors>
{$contributorsXml}
                </contributors>
                <abstract xmlns="http://www.ncbi.nlm.nih.gov/JATS1">
                    <p>{$this->esc($sub->abstract)}</p>
                </abstract>
                <publication_date media_type="online">
                    <month>{$issueMonth}</month>
                    <day>{$issueDay}</day>
                    <year>{$issueYear}</year>
                </publication_date>
                <doi_data>
                    <doi>{$this->esc($article->doi)}</doi>
                    <resource>{$articleUrl}</resource>
                </doi_data>
            </journal_article>
        </journal>
    </body>
</doi_batch>
XML;
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function issnXml(?string $issn, string $type): string
    {
        if (!$issn) return '';
        return "<issn media_type=\"{$type}\">{$this->esc($issn)}</issn>";
    }

    private function orcidXml(?string $orcid): string
    {
        if (!$orcid) return '';
        return "<ORCID>https://orcid.org/{$this->esc($orcid)}</ORCID>";
    }
}
