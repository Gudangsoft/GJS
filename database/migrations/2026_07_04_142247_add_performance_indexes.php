<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (! $this->hasIndex('submissions', 'submissions_submitted_at_index')) {
                $table->index('submitted_at');
            }
            if (! $this->hasIndex('submissions', 'submissions_journal_status_submitted_index')) {
                $table->index(['journal_id', 'status', 'submitted_at'], 'submissions_journal_status_submitted_index');
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if (! $this->hasIndex('articles', 'articles_journal_id_index')) {
                $table->index('journal_id');
            }
            if (! $this->hasIndex('articles', 'articles_date_published_index')) {
                $table->index('date_published');
            }
        });

        Schema::table('issues', function (Blueprint $table) {
            if (! $this->hasIndex('issues', 'issues_journal_id_published_index')) {
                $table->index(['journal_id', 'published']);
            }
        });

        if (Schema::hasTable('keywords') && Schema::hasColumn('keywords', 'locale')) {
            Schema::table('keywords', function (Blueprint $table) {
                if (! $this->hasIndex('keywords', 'keywords_keyword_locale_index')) {
                    $table->index(['keyword', 'locale']);
                }
            });
        }

        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                if (! $this->hasIndex('activity_log', 'activity_log_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndexIfExists('submissions_submitted_at_index');
            $table->dropIndexIfExists('submissions_journal_status_submitted_index');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndexIfExists('articles_journal_id_index');
            $table->dropIndexIfExists('articles_date_published_index');
        });
        Schema::table('issues', function (Blueprint $table) {
            $table->dropIndexIfExists('issues_journal_id_published_index');
        });
        if (Schema::hasTable('keywords')) {
            Schema::table('keywords', function (Blueprint $table) {
                $table->dropIndexIfExists('keywords_keyword_locale_index');
            });
        }
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropIndexIfExists('activity_log_created_at_index');
            });
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains('Key_name', $index);
    }
};
