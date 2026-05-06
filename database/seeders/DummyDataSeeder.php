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
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->truncateAll();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

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

            // 5. Published submissions → articles with galleys (6 per published issue)
            foreach ($publishedIssues as $issue) {
                $this->createPublishedArticles($journal, $issue, $sections, $authors, 6);
            }

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
        Journal $journal, Issue $issue, $sections, $authors, int $count
    ): void {
        foreach (range(1, $count) as $seq) {
            $section  = $sections->random();
            $author   = $authors->random();

            $submission = Submission::factory()->published()->create([
                'journal_id'   => $journal->id,
                'section_id'   => $section->id,
                'user_id'      => $author->id,
                'submitted_at' => $issue->date_published?->copy()->subMonths(rand(2, 5)),
            ]);

            // Contributors (1–3 authors)
            $this->createContributors($submission, $author, rand(1, 3));

            // Article
            $article = Article::factory()->withDoi()->create([
                'submission_id'  => $submission->id,
                'issue_id'       => $issue->id,
                'journal_id'     => $journal->id,
                'section_id'     => $section->id,
                'date_published' => $issue->date_published,
                'sequence'       => $seq * 10.0,
            ]);

            // Galleys — PDF (id) always, sometimes PDF (en) too
            ArticleGalley::factory()->pdf()->create([
                'article_id' => $article->id,
                'label'      => 'PDF',
                'locale'     => $submission->locale,
                'sequence'   => 1,
            ]);

            if (fake()->boolean(40)) {
                ArticleGalley::factory()->create([
                    'article_id' => $article->id,
                    'label'      => 'HTML',
                    'locale'     => $submission->locale,
                    'sequence'   => 2,
                ]);
            }
        }
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

            // Completed review round
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
        // First contributor = primary contact (mirrors the submitting author)
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

        // Additional co-authors
        foreach (range(2, $total) as $seq) {
            SubmissionContributor::factory()->create([
                'submission_id'  => $submission->id,
                'user_id'        => null,
                'primary_contact'=> false,
                'sequence'       => $seq,
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
