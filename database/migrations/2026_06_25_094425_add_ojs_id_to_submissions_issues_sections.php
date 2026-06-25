<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('ojs_id')->nullable()->after('id');
            $table->string('ojs_source_url', 512)->nullable()->after('ojs_id');
            $table->index(['ojs_id', 'journal_id'], 'submissions_ojs_journal_idx');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->unsignedBigInteger('ojs_id')->nullable()->after('id');
            $table->index(['ojs_id', 'journal_id'], 'issues_ojs_journal_idx');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('ojs_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_ojs_journal_idx');
            $table->dropColumn(['ojs_id', 'ojs_source_url']);
        });
        Schema::table('issues', function (Blueprint $table) {
            $table->dropIndex('issues_ojs_journal_idx');
            $table->dropColumn('ojs_id');
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('ojs_id');
        });
    }
};
