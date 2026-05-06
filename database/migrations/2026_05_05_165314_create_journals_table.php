<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_abbrev')->nullable();
            $table->text('description')->nullable();
            $table->string('issn_print', 9)->nullable();
            $table->string('issn_online', 9)->nullable();
            $table->string('publisher')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('primary_locale', 14)->default('id');
            $table->json('supported_locales')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone')->default('Asia/Jakarta');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->boolean('enabled')->default(true);
            $table->text('focus_scope')->nullable();
            $table->text('ethics_statement')->nullable();
            $table->text('author_guidelines')->nullable();
            $table->text('reviewer_guidelines')->nullable();
            $table->text('privacy_statement')->nullable();
            $table->text('about_journal')->nullable();
            $table->enum('review_mode', ['single_blind', 'double_blind', 'triple_blind', 'open'])->default('double_blind');
            $table->unsignedSmallInteger('num_weeks_per_review')->default(4);
            $table->unsignedSmallInteger('num_weeks_per_response')->default(3);
            $table->boolean('requires_author_competinginterests')->default(false);
            $table->boolean('requires_reviewer_competinginterests')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
