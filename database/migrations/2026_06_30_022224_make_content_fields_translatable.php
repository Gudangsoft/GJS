<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $defaultLocale = 'id';

    public function up(): void
    {
        $this->convertTable('journals', [
            'name', 'description', 'focus_scope', 'author_guidelines',
            'reviewer_guidelines', 'privacy_statement', 'about_journal',
            'open_access_statement', 'announcements_intro', 'ethics_statement',
            'apc_waiver_policy', 'submission_acknowledgement', 'copyright_notice',
        ]);

        $this->convertTable('submissions', [
            'title', 'subtitle', 'abstract', 'competing_interests',
        ]);

        $this->convertTable('issues', ['title', 'description']);

        $this->convertTable('announcements', ['title', 'description_short', 'description']);

        $this->convertTable('sections', ['title', 'policy', 'reviewer_guidelines']);
    }

    public function down(): void
    {
        $this->revertTable('journals', [
            'name', 'description', 'focus_scope', 'author_guidelines',
            'reviewer_guidelines', 'privacy_statement', 'about_journal',
            'open_access_statement', 'announcements_intro', 'ethics_statement',
            'apc_waiver_policy', 'submission_acknowledgement', 'copyright_notice',
        ]);
        $this->revertTable('submissions', ['title', 'subtitle', 'abstract', 'competing_interests']);
        $this->revertTable('issues', ['title', 'description']);
        $this->revertTable('announcements', ['title', 'description_short', 'description']);
        $this->revertTable('sections', ['title', 'policy', 'reviewer_guidelines']);
    }

    // ── helpers ───────────────────────────────────────────────────────────────

    private function convertTable(string $table, array $columns): void
    {
        // 1. Add temp JSON columns
        Schema::table($table, function (Blueprint $t) use ($columns) {
            foreach ($columns as $col) {
                $t->json("{$col}_i18n")->nullable()->after($col);
            }
        });

        // 2. Copy & wrap existing text into JSON
        $rows = DB::table($table)->get(['id', ...$columns]);
        foreach ($rows as $row) {
            $update = [];
            foreach ($columns as $col) {
                $raw = $row->$col;
                if ($raw === null) continue;
                $decoded = json_decode($raw, true);
                // Already an associative JSON object (translatable)
                if (is_array($decoded) && !isset($decoded[0])) {
                    $update["{$col}_i18n"] = $raw;
                } else {
                    $update["{$col}_i18n"] = json_encode(
                        [$this->defaultLocale => $raw],
                        JSON_UNESCAPED_UNICODE
                    );
                }
            }
            if ($update) {
                DB::table($table)->where('id', $row->id)->update($update);
            }
        }

        // 3. Drop original columns and rename temp columns
        Schema::table($table, function (Blueprint $t) use ($columns) {
            $t->dropColumn($columns);
        });
        Schema::table($table, function (Blueprint $t) use ($columns) {
            foreach ($columns as $col) {
                $t->renameColumn("{$col}_i18n", $col);
            }
        });
    }

    private function revertTable(string $table, array $columns): void
    {
        // 1. Add temp text columns
        Schema::table($table, function (Blueprint $t) use ($columns) {
            foreach ($columns as $col) {
                $t->text("{$col}_plain")->nullable()->after($col);
            }
        });

        // 2. Extract default locale value
        $rows = DB::table($table)->get(['id', ...$columns]);
        foreach ($rows as $row) {
            $update = [];
            foreach ($columns as $col) {
                $raw = $row->$col;
                if ($raw === null) continue;
                $decoded = json_decode($raw, true);
                $update["{$col}_plain"] = is_array($decoded)
                    ? ($decoded[$this->defaultLocale] ?? reset($decoded))
                    : $raw;
            }
            if ($update) {
                DB::table($table)->where('id', $row->id)->update($update);
            }
        }

        // 3. Drop JSON columns and rename back
        Schema::table($table, function (Blueprint $t) use ($columns) {
            $t->dropColumn($columns);
        });
        Schema::table($table, function (Blueprint $t) use ($columns) {
            foreach ($columns as $col) {
                $t->renameColumn("{$col}_plain", $col);
            }
        });
    }
};
