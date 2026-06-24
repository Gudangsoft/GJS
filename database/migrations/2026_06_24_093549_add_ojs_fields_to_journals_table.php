<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Masthead
            $table->string('publication_frequency')->nullable()->after('about_journal');

            // Kontak
            $table->string('contact_name')->nullable()->after('email');
            $table->string('contact_phone')->nullable()->after('contact_name');
            $table->string('tech_support_name')->nullable()->after('contact_phone');
            $table->string('tech_support_email')->nullable()->after('tech_support_name');
            $table->text('mailing_address')->nullable()->after('tech_support_email');

            // Pengiriman
            $table->boolean('disable_submissions')->default(false)->after('requires_reviewer_competinginterests');
            $table->json('submission_checklist')->nullable()->after('disable_submissions');
            $table->text('submission_acknowledgement')->nullable()->after('submission_checklist');
            $table->text('copyright_notice')->nullable()->after('submission_acknowledgement');

            // Distribusi & Lisensi
            $table->string('license_type')->default('CC BY 4.0')->after('garuda_id');
            $table->string('copyright_holder')->default('author')->after('license_type');
            $table->string('doi_prefix')->nullable()->after('copyright_holder');
            $table->string('doi_suffix_pattern')->nullable()->after('doi_prefix');
            $table->text('open_access_statement')->nullable()->after('doi_suffix_pattern');

            // Website & Tampilan
            $table->string('favicon')->nullable()->after('cover_image');
            $table->text('custom_header_html')->nullable()->after('favicon');
            $table->text('custom_footer_html')->nullable()->after('custom_header_html');
            $table->string('homepage_image')->nullable()->after('custom_footer_html');

            // Pengumuman
            $table->boolean('announcements_enabled')->default(true)->after('enabled');
            $table->text('announcements_intro')->nullable()->after('announcements_enabled');

            // OAI
            $table->string('oai_identifier')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'publication_frequency', 'contact_name', 'contact_phone',
                'tech_support_name', 'tech_support_email', 'mailing_address',
                'disable_submissions', 'submission_checklist',
                'submission_acknowledgement', 'copyright_notice',
                'license_type', 'copyright_holder', 'doi_prefix',
                'doi_suffix_pattern', 'open_access_statement',
                'favicon', 'custom_header_html', 'custom_footer_html', 'homepage_image',
                'announcements_enabled', 'announcements_intro', 'oai_identifier',
            ]);
        });
    }
};
