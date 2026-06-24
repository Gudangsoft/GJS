<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\ReviewRound;
use App\Models\Section;
use App\Models\Submission;
use App\Models\SubmissionContributor;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->truncateAll();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get the super admin as "uploader" for submission files
        $superAdmin = User::role('super_admin')->first();

        // 1. Users
        $managers  = User::factory(2)->create()->each(fn ($u) => $u->assignRole('journal_manager'));
        $editors   = User::factory(4)->create()->each(fn ($u) => $u->assignRole('editor'));
        $reviewers = User::factory(10)->create()->each(fn ($u) => $u->assignRole('reviewer'));
        $authors   = User::factory(20)->create()->each(fn ($u) => $u->assignRole('author'));

        // 2. Journals (5)
        $journals = Journal::factory(5)->create();

        foreach ($journals as $journalIndex => $journal) {
            // 3. Sections (4 per journal)
            $sections = Section::factory(4)
                ->create(['journal_id' => $journal->id]);

            // 4. Issues — 3 published past + 1 current
            $publishedIssues = collect();
            foreach (range(1, 3) as $num) {
                $year = now()->year - (3 - $num);
                $issue = Issue::factory()->published()->create([
                    'journal_id' => $journal->id,
                    'volume'     => $journalIndex + 1,
                    'number'     => $num,
                    'year'       => $year,
                ]);
                $publishedIssues->push($issue);
            }

            $currentIssue = Issue::factory()->current()->create([
                'journal_id' => $journal->id,
                'volume'     => $journalIndex + 1,
                'number'     => 4,
                'year'       => now()->year,
            ]);

            // 5. Published submissions → articles with real PDF galleys (6 per published issue)
            foreach ($publishedIssues as $issue) {
                $this->createPublishedArticles($journal, $issue, $sections, $authors, 6, $superAdmin);
            }

            // 5b. Current issue also gets published articles (shown on journal home)
            $this->createPublishedArticles($journal, $currentIssue, $sections, $authors, 5, $superAdmin);

            // 6. Active submissions at various pipeline stages
            $this->createActiveSubmissions($journal, $currentIssue, $sections, $authors, $editors, $reviewers);

            // 7. Declined / archived submissions
            $this->createDeclinedSubmissions($journal, $sections, $authors, 3);

            // 8. Announcements
            Announcement::factory(3)->create([
                'journal_id' => $journal->id,
                'user_id'    => $editors->random()->id,
            ]);
        }

        $this->command->info('DummyDataSeeder complete.');
    }

    private function truncateAll(): void
    {
        // Clean up stored PDF files from previous runs
        Storage::disk('public')->deleteDirectory('submissions');

        $tables = [
            'article_galleys', 'articles', 'announcements',
            'reviews', 'review_assignments', 'review_rounds',
            'submission_contributors', 'submission_files', 'submissions',
            'issues', 'sections', 'journals',
        ];

        foreach ($tables as $table) {
            DB::table($table)->delete();
        }

        User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))->forceDelete();
    }

    private function createPublishedArticles(
        Journal $journal, Issue $issue, $sections, $authors, int $count, ?User $uploader
    ): void {
        foreach (range(1, $count) as $seq) {
            $section = $sections->random();
            $author  = $authors->random();

            $submission = Submission::factory()->published()->create([
                'journal_id'   => $journal->id,
                'section_id'   => $section->id,
                'user_id'      => $author->id,
                'submitted_at' => $issue->date_published?->copy()->subMonths(rand(2, 5)),
            ]);

            // Contributors (1–3 authors)
            $this->createContributors($submission, $author, rand(1, 3));

            // Article record
            $article = Article::factory()->withDoi()->create([
                'submission_id'  => $submission->id,
                'issue_id'       => $issue->id,
                'journal_id'     => $journal->id,
                'section_id'     => $section->id,
                'date_published' => $issue->date_published,
                'sequence'       => $seq * 10.0,
            ]);

            // Generate actual PDF bytes
            $issueLabel = ($journal->name_abbrev ?? $journal->name)
                . ' Vol.' . $issue->volume
                . ' No.' . $issue->number
                . ' (' . $issue->year . ')';

            $pdfBytes = $this->makePdf(
                $submission->title,
                $author->first_name . ' ' . $author->last_name,
                $submission->abstract ?? '',
                $journal->name,
                $issueLabel,
                $article->doi
            );

            // Store file to disk
            $storedName = 'article_' . $article->id . '_galley.pdf';
            $filePath   = 'submissions/' . $journal->id . '/' . $submission->id . '/' . $storedName;
            Storage::disk('public')->put($filePath, $pdfBytes);

            // SubmissionFile record (file_stage 10 = proof/galley)
            $subFile = SubmissionFile::create([
                'submission_id'      => $submission->id,
                'user_id'            => $uploader?->id ?? User::value('id'),
                'file_stage'         => 10,
                'original_file_name' => Str::slug(mb_substr($submission->title, 0, 50)) . '.pdf',
                'stored_file_name'   => $storedName,
                'path'               => $filePath,
                'mime_type'          => 'application/pdf',
                'file_size'          => strlen($pdfBytes),
                'revision'           => 1,
                'viewable'           => true,
                'genre'              => 'article',
            ]);

            // PDF galley — linked to actual file
            ArticleGalley::factory()->pdf()->create([
                'article_id'         => $article->id,
                'label'              => 'PDF',
                'locale'             => $submission->locale,
                'submission_file_id' => $subFile->id,
                'remote_url'         => null,
                'sequence'           => 1,
            ]);

            // 40% chance to also have an HTML galley
            if (fake()->boolean(40)) {
                ArticleGalley::factory()->create([
                    'article_id'         => $article->id,
                    'label'              => 'HTML',
                    'locale'             => $submission->locale,
                    'submission_file_id' => null,
                    'remote_url'         => null,
                    'sequence'           => 2,
                ]);
            }
        }
    }

    /**
     * Generate a minimal but valid single-page A4 PDF with article metadata.
     * Uses only built-in Type1 fonts — no external libraries needed.
     */
    private function makePdf(
        string $title,
        string $authors,
        string $abstract,
        string $journalName,
        string $issueLabel,
        ?string $doi
    ): string {
        // Strip non-printable/non-ASCII and escape PDF special chars
        $esc = function (string $t): string {
            $t = preg_replace('/[^\x20-\x7E\t]/', ' ', $t);
            return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $t);
        };

        // Simple character-count word-wrap
        $wrap = function (string $text, int $max): array {
            $words = preg_split('/\s+/', trim($text));
            $lines = [''];
            foreach ($words as $word) {
                $cur = end($lines);
                $try = $cur === '' ? $word : "$cur $word";
                if (strlen($try) <= $max) {
                    $lines[count($lines) - 1] = $try;
                } else {
                    $lines[] = $word;
                }
            }
            return array_values(array_filter($lines, fn ($l) => $l !== ''));
        };

        $s = '';

        // ── Blue header bar ──────────────────────────────────────────────
        $s .= "0.067 0.239 0.541 rg\n0 810 595 32 re f\n0 0 0 rg\n";
        $s .= "1 1 1 rg BT /F1 8.5 Tf 50 819 Td (" . $esc(strtoupper(mb_substr($journalName, 0, 80))) . ") Tj ET\n";
        $s .= "0 0 0 rg\n";

        // Issue label below bar
        $s .= "BT /F1 9 Tf 50 796 Td 0.4 0.4 0.4 rg (" . $esc($issueLabel) . ") Tj ET\n";
        $s .= "0 0 0 rg\n";

        // Thin divider line
        $s .= "0.82 0.82 0.82 RG 0.5 w 50 788 m 545 788 l S 0 0 0 RG\n";

        // ── Title ────────────────────────────────────────────────────────
        $titleLines = $wrap($title, 64);
        $y = 768;
        $s .= "BT /F2 15 Tf\n50 $y Td 0 0 0 rg\n";
        foreach ($titleLines as $i => $line) {
            if ($i > 0) {
                $y -= 20;
                $s .= "0 -20 Td\n";
            }
            $s .= "(" . $esc($line) . ") Tj\n";
        }
        $s .= "ET\n";
        $y -= 28;

        // ── Authors ──────────────────────────────────────────────────────
        $s .= "BT /F1 11 Tf 50 $y Td 0.13 0.22 0.6 rg (" . $esc(mb_substr($authors, 0, 100)) . ") Tj ET\n";
        $s .= "0 0 0 rg\n";
        $y -= 18;

        // ── DOI ──────────────────────────────────────────────────────────
        if ($doi) {
            $s .= "BT /F1 9 Tf 50 $y Td 0.45 0.45 0.45 rg (DOI: " . $esc($doi) . ") Tj ET\n";
            $s .= "0 0 0 rg\n";
            $y -= 15;
        }

        $y -= 8;
        $s .= "0.88 0.88 0.88 RG 0.5 w 50 $y m 545 $y l S 0 0 0 RG\n";
        $y -= 22;

        // ── Abstract ─────────────────────────────────────────────────────
        $s .= "BT /F2 11 Tf 50 $y Td 0 0 0 rg (Abstract) Tj ET\n";
        $y -= 16;

        $abstractText = mb_substr(strip_tags($abstract), 0, 700);
        $absLines     = $wrap($abstractText, 90);
        $s .= "BT /F1 9.5 Tf\n50 $y Td\n0.12 0.12 0.12 rg\n";
        foreach (array_slice($absLines, 0, 14) as $i => $line) {
            if ($i > 0) {
                $y -= 14;
                $s .= "0 -14 Td\n";
            }
            $s .= "(" . $esc($line) . ") Tj\n";
        }
        $s .= "ET\n";
        $y -= 32;

        // ── Keywords callout ─────────────────────────────────────────────
        if ($y > 120) {
            $s .= "0.94 0.96 1 rg 50 " . ($y - 20) . " 495 26 re f 0 0 0 rg\n";
            $s .= "BT /F1 9 Tf 56 $y Td 0.13 0.22 0.6 rg (Keywords: ) Tj 0.25 0.25 0.25 rg (see full article online) Tj ET\n";
            $y -= 48;
        }

        // ── Body placeholder ─────────────────────────────────────────────
        if ($y > 120) {
            $s .= "BT /F2 11 Tf 50 $y Td 0 0 0 rg (1. Introduction) Tj ET\n";
            $y -= 18;

            $body      = 'This article presents original research findings. '
                       . 'The study employs a rigorous methodological framework to investigate the stated research questions. '
                       . 'Results demonstrate significant contributions to the current body of knowledge. '
                       . 'Full text is available in the published version of this article.';
            $bodyLines = $wrap($body, 90);
            $s .= "BT /F1 10 Tf\n50 $y Td\n0.1 0.1 0.1 rg\n";
            foreach ($bodyLines as $i => $line) {
                if ($i > 0) {
                    $y -= 15;
                    $s .= "0 -15 Td\n";
                }
                $s .= "(" . $esc($line) . ") Tj\n";
            }
            $s .= "ET\n";
        }

        // ── Footer ───────────────────────────────────────────────────────
        $s .= "0.7 0.7 0.7 RG 0.5 w 50 55 m 545 55 l S 0 0 0 RG\n";
        $s .= "BT /F1 8 Tf 50 43 Td 0.5 0.5 0.5 rg (" . $esc($journalName . '  |  ' . $issueLabel) . ") Tj ET\n";
        if ($doi) {
            $s .= "BT /F1 8 Tf 50 31 Td 0.5 0.5 0.5 rg (https://doi.org/" . $esc($doi) . ") Tj ET\n";
        }

        // ── Assemble PDF objects ─────────────────────────────────────────
        $objs    = [];
        $objs[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objs[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objs[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842]\n"
                 . "   /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>";
        $objs[4] = "<< /Length " . strlen($s) . " >>\nstream\n" . $s . "endstream";
        $objs[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objs[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";

        $pdf  = "%PDF-1.4\n";
        $offs = [];
        foreach ($objs as $n => $data) {
            $offs[$n] = strlen($pdf);
            $pdf .= "$n 0 obj\n$data\nendobj\n";
        }

        $xref = strlen($pdf);
        $size = count($objs) + 1;
        $pdf .= "xref\n0 $size\n0000000000 65535 f \n";
        foreach ($offs as $off) {
            $pdf .= str_pad($off, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size $size /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";

        return $pdf;
    }

    private function createActiveSubmissions(
        Journal $journal, Issue $currentIssue, $sections, $authors, $editors, $reviewers
    ): void {
        $editor = $editors->random();

        // Scheduled / production (2)
        foreach (range(1, 2) as $_) {
            $submission = Submission::factory()->create([
                'journal_id' => $journal->id,
                'section_id' => $sections->random()->id,
                'user_id'    => $authors->random()->id,
                'status'     => fake()->randomElement(['scheduled', 'production', 'copyediting']),
            ]);
            $this->createContributors($submission, User::find($submission->user_id), 2);

            $round = ReviewRound::factory()->accepted()->create([
                'submission_id' => $submission->id,
                'round'         => 1,
            ]);
            $this->createCompletedReviewAssignments($submission, $round, $reviewers->random(2), $editor);
        }

        // Under review (3)
        foreach (range(1, 3) as $_) {
            $submission = Submission::factory()->inReview()->create([
                'journal_id' => $journal->id,
                'section_id' => $sections->random()->id,
                'user_id'    => $authors->random()->id,
            ]);
            $this->createContributors($submission, User::find($submission->user_id), rand(1, 3));

            $round = ReviewRound::factory()->create([
                'submission_id' => $submission->id,
                'round'         => 1,
            ]);
            $this->createActiveReviewAssignments($submission, $round, $reviewers->random(2), $editor);
        }

        // Awaiting editor decision (revision_required) (2)
        foreach (range(1, 2) as $_) {
            $submission = Submission::factory()->create([
                'journal_id' => $journal->id,
                'section_id' => $sections->random()->id,
                'user_id'    => $authors->random()->id,
                'status'     => 'revision_required',
            ]);
            $this->createContributors($submission, User::find($submission->user_id), 2);

            $round = ReviewRound::factory()->revisionsRequested()->create([
                'submission_id' => $submission->id,
                'round'         => 1,
            ]);
            $this->createCompletedReviewAssignments($submission, $round, $reviewers->random(2), $editor);
        }

        // Queued / assigned (3)
        foreach (range(1, 3) as $_) {
            $submission = Submission::factory()->create([
                'journal_id' => $journal->id,
                'section_id' => $sections->random()->id,
                'user_id'    => $authors->random()->id,
                'status'     => fake()->randomElement(['queued', 'assigned']),
            ]);
            $this->createContributors($submission, User::find($submission->user_id), 1);
        }

        // New submissions (submitted) (4)
        foreach (range(1, 4) as $_) {
            $submission = Submission::factory()->create([
                'journal_id' => $journal->id,
                'section_id' => $sections->random()->id,
                'user_id'    => $authors->random()->id,
                'status'     => 'submitted',
            ]);
            $this->createContributors($submission, User::find($submission->user_id), rand(1, 2));
        }

        // Draft (2)
        Submission::factory(2)->draft()->create([
            'journal_id' => $journal->id,
            'section_id' => $sections->random()->id,
            'user_id'    => $authors->random()->id,
        ]);
    }

    private function createDeclinedSubmissions(Journal $journal, $sections, $authors, int $count): void
    {
        Submission::factory($count)->declined()->create([
            'journal_id' => $journal->id,
            'section_id' => $sections->random()->id,
            'user_id'    => $authors->random()->id,
        ]);
    }

    private function createContributors(Submission $submission, User $primaryAuthor, int $total): void
    {
        SubmissionContributor::factory()->primaryContact()->create([
            'submission_id'  => $submission->id,
            'user_id'        => $primaryAuthor->id,
            'first_name'     => $primaryAuthor->first_name,
            'last_name'      => $primaryAuthor->last_name,
            'email'          => $primaryAuthor->email,
            'affiliation'    => $primaryAuthor->affiliation,
            'country'        => $primaryAuthor->country,
            'sequence'       => 1,
        ]);

        foreach (range(2, $total) as $seq) {
            SubmissionContributor::factory()->create([
                'submission_id'   => $submission->id,
                'user_id'         => null,
                'primary_contact' => false,
                'sequence'        => $seq,
            ]);
        }
    }

    private function createActiveReviewAssignments(
        Submission $submission, ReviewRound $round, $reviewers, User $editor
    ): void {
        foreach ($reviewers as $reviewer) {
            ReviewAssignment::factory()->accepted()->create([
                'submission_id'   => $submission->id,
                'review_round_id' => $round->id,
                'reviewer_id'     => $reviewer->id,
                'editor_id'       => $editor->id,
                'round'           => $round->round,
            ]);
        }
    }

    private function createCompletedReviewAssignments(
        Submission $submission, ReviewRound $round, $reviewers, User $editor
    ): void {
        foreach ($reviewers as $reviewer) {
            $assignment = ReviewAssignment::factory()->completed()->create([
                'submission_id'   => $submission->id,
                'review_round_id' => $round->id,
                'reviewer_id'     => $reviewer->id,
                'editor_id'       => $editor->id,
                'round'           => $round->round,
            ]);

            Review::factory()->minorRevisions()->create([
                'review_assignment_id' => $assignment->id,
            ]);
        }
    }
}
