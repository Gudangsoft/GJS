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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('abbrev', 16)->nullable();
            $table->text('policy')->nullable();
            $table->boolean('abstract_word_count')->default(false);
            $table->unsignedInteger('word_count')->nullable();
            $table->boolean('hide_title')->default(false);
            $table->boolean('hide_author')->default(false);
            $table->boolean('is_inactive')->default(false);
            $table->boolean('editor_restricted')->default(false);
            $table->boolean('submitter_restricted')->default(false);
            $table->unsignedSmallInteger('sequence')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
