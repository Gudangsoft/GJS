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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OjsImportService
{
    private string $baseUrl;
    private ?string $apiKey;
    private int $journalId;
    private int $importUserId;
    private array $log = [];
    private int $perPage = 50;

    /** Stats */
    public int $sectionsCreated  = 0;
    public int $issuesCreated    = 0;
    public int $issuesSkipped    = 0;
    public int $articlesCreated  = 0;
    public int $articlesSkipped  = 0;
    public int $articlesUpdated  = 0;
    public int $errors           = 0;

    public function __construct(string $baseUrl, int $journalId, ?string $apiKey = null)
    {
        $this->baseUrl   = rtrim($baseUrl, '/');
        $this->journalId = $journalId;
        $this->apiKey    = $apiKey;
        $this->importUserId = User::where('email', 'admin@gjs.local')->value('id')
            ?? User::first()?->id
            ?? 1;
    }

    public function getLogs(): array
    {
        return $this->log;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────────────

    public function testConnection(): array
    {
        try {
            $r = $this->get('/issues', ['count' => 1]);
            if (isset($r['items'])) {
                $total = $r['itemsMax'] ?? count($r['items']);
                return ['ok' => true, 'message' => "Koneksi berhasil. Total edisi di OJS: {$total}"];
            }
            return ['ok' => false, 'message' => 'Respons tidak dikenali: ' . json_encode($r)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal terhubung: ' . $e->getMessage()];
        }
    }

    public function importAll(callable $progress = null): void
    {
        $this->info('=== Memulai Import OJS ===');

        $this->importSections($progress);
        $this->importIssues($progress);
        $this->importArticles($progress);

        $this->info("=== Selesai. Edisi: +{$this->issuesCreated} | Artikel: +{$this->articlesCreated} | Diperbarui: {$this->articlesUpdated} | Lewati: {$this->articlesSkipped} | Error: {$this->errors} ===");
    }

    public function importSections(callable $progress = null): void
    {
        $this->info('Mengimpor seksi...');
        try {
            $data = $this->get('/sections', ['count' => 100]);
            $items = $data['items'] ?? $data ?? [];
        } catch (\Throwable $e) {
            $this->warn('Seksi tidak tersedia via API: ' . $e->getMessage());
            return;
        }

        foreach ($items as $s) {
            $this->upsertSection($s);
            if ($progress) $progress('section', $s);
        }
    }

    public function importIssues(callable $progress = null): void
    {
        $this->info('Mengimpor edisi...');
        $offset = 0;

        do {
            $data  = $this->get('/issues', ['count' => $this->perPage, 'offset' => $offset]);
            $items = $data['items'] ?? [];
            $total = $data['itemsMax'] ?? count($items);

            foreach ($items as $issue) {
                $this->upsertIssue($issue);
                if ($progress) $progress('issue', $issue);
            }

            $offset += count($items);
        } while ($offset < $total && count($items) > 0);
    }

    public function importArticles(callable $progress = null): void
    {
        $this->info('Mengimpor artikel...');
        $offset = 0;

        do {
            $data  = $this->get('/submissions', [
                'count'  => $this->perPage,
                'offset' => $offset,
                'status' => 3, // STATUS_PUBLISHED
            ]);
            $items = $data['items'] ?? [];
            $total = $data['itemsMax'] ?? count($items);

            foreach ($items as $submission) {
                try {
                    $this->upsertSubmission($submission);
                } catch (\Throwable $e) {
                    $this->errors++;
                    $this->error('Artikel ID ' . ($submission['id'] ?? '?') . ': ' . $e->getMessage());
                    Log::error('[OjsImport] ' . $e->getMessage(), ['submission' => $submission['id'] ?? null]);
                }
                if ($progress) $progress('article', $submission);
            }

            $offset += count($items);
        } while ($offset < $total && count($items) > 0);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Upsert helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function upsertSection(array $s): ?Section
    {
        $ojsId = $s['id'] ?? null;
        $title = $this->locale($s['title'] ?? $s['abbreviation'] ?? '');
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
        $ojsId  = $i['id'] ?? null;
        $volume = $i['volume'] ?? null;
        $number = $i['number'] ?? null;
        $year   = $i['year']   ?? null;
        $title  = $this->locale($i['title'] ?? []);

        $issue = Issue::where('journal_id', $this->journalId)
            ->where(function ($q) use ($ojsId, $volume, $number, $year) {
                $q->where('ojs_id', $ojsId)
                  ->orWhere(fn($q2) => $q2
                      ->where('volume', $volume)
                      ->where('number', $number)
                      ->where('year', $year)
                  );
            })->first();

        $datePublished = $i['datePublished'] ?? null;
        $published     = (bool)($i['published'] ?? false);

        $data = [
            'journal_id'    => $this->journalId,
            'ojs_id'        => $ojsId,
            'volume'        => $volume,
            'number'        => $number,
            'year'          => $year,
            'title'         => $title ?: null,
            'description'   => $this->locale($i['description'] ?? []),
            'published'     => $published,
            'current'       => (bool)($i['isCurrent'] ?? false),
            'show_volume'   => (bool)($i['showVolume'] ?? true),
            'show_number'   => (bool)($i['showNumber'] ?? true),
            'show_year'     => (bool)($i['showYear']   ?? true),
            'show_title'    => (bool)($i['showTitle']  ?? false),
            'date_published' => $datePublished,
        ];

        if (!$issue) {
            $issue = Issue::create($data);
            $this->issuesCreated++;
            $label = "Vol.{$volume} No.{$number} ({$year})";
            $this->info("  Edisi: {$label}");
        } else {
            $issue->update($data);
            $this->issuesSkipped++;
        }

        return $issue;
    }

    private function upsertSubmission(array $sub): void
    {
        $ojsId = $sub['id'] ?? null;

        // Get publication (OJS 3.x uses publications array)
        $pub = null;
        if (!empty($sub['publications'])) {
            $currentPubId = $sub['currentPublicationId'] ?? null;
            foreach ($sub['publications'] as $p) {
                if ($currentPubId && ($p['id'] ?? null) == $currentPubId) {
                    $pub = $p;
                    break;
                }
            }
            $pub = $pub ?? end($sub['publications']);
        } else {
            $pub = $sub; // OJS 3.1 flat structure
        }

        if (!$pub) return;

        $locale  = $sub['locale'] ?? 'en';
        $title   = $this->locale($pub['title'] ?? [], $locale);
        $doi     = $pub['pub-id::doi'] ?? $pub['doi'] ?? $sub['doi'] ?? null;
        $pages   = $pub['pages'] ?? null;
        $date    = $pub['datePublished'] ?? $sub['datePublished'] ?? null;
        $ojsSectionId = $pub['sectionId'] ?? $sub['sectionId'] ?? null;
        $ojsIssueId   = $pub['issueId']   ?? $sub['issueId']   ?? null;
        $seq          = (float)($pub['seq'] ?? $pub['sequence'] ?? 0);

        if (!$title) return;

        // Resolve section
        $section = null;
        if ($ojsSectionId) {
            $section = Section::where('journal_id', $this->journalId)
                ->where('ojs_id', $ojsSectionId)
                ->first();
        }

        // Resolve issue
        $issue = null;
        if ($ojsIssueId) {
            $issue = Issue::where('journal_id', $this->journalId)
                ->where('ojs_id', $ojsIssueId)
                ->first();
        }

        // Check existing: by ojs_id or doi
        $existing = null;
        if ($ojsId) {
            $existing = Submission::where('journal_id', $this->journalId)
                ->where('ojs_id', $ojsId)
                ->first();
        }
        if (!$existing && $doi) {
            $existing = Submission::where('doi', $doi)->first();
        }

        $keywords = $this->localeArray($pub['keywords'] ?? [], $locale);

        $submissionData = [
            'journal_id'    => $this->journalId,
            'section_id'    => $section?->id,
            'user_id'       => $this->importUserId,
            'status'        => 'published',
            'title'         => $title,
            'subtitle'      => $this->locale($pub['subtitle'] ?? [], $locale) ?: null,
            'abstract'      => $this->locale($pub['abstract'] ?? [], $locale) ?: null,
            'keywords'      => $keywords ?: null,
            'locale'        => $locale,
            'doi'           => $doi,
            'submission_type' => 'article',
            'ojs_id'        => $ojsId,
            'ojs_source_url' => $this->baseUrl,
            'submitted_at'  => $date,
        ];

        DB::transaction(function () use ($existing, $submissionData, $issue, $pub, $locale, $seq, $date, $doi, $ojsId) {
            if ($existing) {
                $existing->update($submissionData);
                $submission = $existing;
                $this->articlesUpdated++;
            } else {
                $submission = Submission::create($submissionData);
                $this->articlesCreated++;
                $this->info("  Artikel: {$submissionData['title']}");
            }

            // Article record
            $articleData = [
                'submission_id'  => $submission->id,
                'issue_id'       => $issue?->id,
                'journal_id'     => $this->journalId,
                'section_id'     => $submissionData['section_id'],
                'doi'            => $doi,
                'pages'          => $pub['pages'] ?? null,
                'sequence'       => $seq,
                'date_published' => $date,
                'access_status'  => 'open',
            ];

            $article = Article::updateOrCreate(
                ['submission_id' => $submission->id],
                $articleData
            );

            // Contributors (authors)
            if ($existing) {
                $submission->contributors()->delete();
            }
            foreach (($pub['authors'] ?? []) as $idx => $author) {
                $firstName  = $this->locale($author['givenName']  ?? [], $locale);
                $lastName   = $this->locale($author['familyName'] ?? [], $locale);
                $affil      = $this->locale($author['affiliation'] ?? [], $locale);
                $country    = $author['country'] ?? null;
                $orcid      = isset($author['orcid']) ? preg_replace('/^https?:\/\/orcid\.org\//', '', $author['orcid']) : null;

                SubmissionContributor::create([
                    'submission_id'      => $submission->id,
                    'first_name'         => $firstName,
                    'last_name'          => $lastName,
                    'email'              => $author['email'] ?? null,
                    'affiliation'        => $affil ?: null,
                    'country'            => $country,
                    'orcid'              => $orcid,
                    'sequence'           => (int)($author['seq'] ?? $idx),
                    'primary_contact'    => (bool)($author['primaryContact'] ?? ($idx === 0)),
                    'include_in_browse'  => (bool)($author['includeInBrowse'] ?? true),
                ]);
            }

            // Galleys
            if ($existing) {
                $article->galleys()->delete();
            }
            foreach (($pub['galleys'] ?? []) as $gIdx => $galley) {
                $remoteUrl = $galley['file']['url'] ?? $galley['urlRemote'] ?? null;
                $label     = $galley['label'] ?? 'PDF';

                ArticleGalley::create([
                    'article_id'  => $article->id,
                    'label'       => $label,
                    'locale'      => $galley['locale'] ?? $locale,
                    'remote_url'  => $remoteUrl,
                    'sequence'    => $gIdx,
                    'is_approved' => true,
                ]);
            }
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HTTP helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function get(string $endpoint, array $params = []): array
    {
        $req = Http::timeout(30)
            ->accept('application/json');

        if ($this->apiKey) {
            $req = $req->withToken($this->apiKey);
        }

        $url = $this->baseUrl . '/api/v1' . $endpoint;
        $response = $req->get($url, $params);

        if ($response->failed()) {
            throw new \RuntimeException("HTTP {$response->status()} dari {$url}");
        }

        return $response->json() ?? [];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Locale helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function locale(mixed $val, string $prefer = 'id'): string
    {
        if (is_string($val)) return trim($val);
        if (!is_array($val) || empty($val)) return '';

        return trim(
            $val[$prefer]
            ?? $val['en']
            ?? $val['id']
            ?? reset($val)
            ?? ''
        );
    }

    private function localeArray(mixed $val, string $prefer = 'id'): array
    {
        if (is_array($val) && isset($val[$prefer])) return (array)$val[$prefer];
        if (is_array($val) && isset($val['en']))     return (array)$val['en'];
        if (is_array($val) && isset($val['id']))     return (array)$val['id'];
        if (is_array($val)) {
            $first = reset($val);
            return is_array($first) ? $first : (array)$first;
        }
        return [];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Log helpers
    // ──────────────────────────────────────────────────────────────────────────

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
