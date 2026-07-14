<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM(
            'draft','submitted','queued','accepted_for_review','assigned','review',
            'revision_required','resubmit','accepted','copyediting','production',
            'scheduled','published','declined','archived'
        ) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE submissions MODIFY COLUMN status ENUM(
            'draft','submitted','queued','assigned','review',
            'revision_required','resubmit','accepted','copyediting','production',
            'scheduled','published','declined','archived'
        ) NOT NULL DEFAULT 'draft'");
    }
};
