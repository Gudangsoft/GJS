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
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('file_stage');
            $table->string('original_file_name');
            $table->string('stored_file_name');
            $table->string('path');
            $table->string('mime_type', 128);
            $table->unsignedBigInteger('file_size');
            $table->unsignedSmallInteger('revision')->default(1);
            $table->unsignedBigInteger('source_submission_file_id')->nullable();
            $table->string('uploaderUserGroupId')->nullable();
            $table->string('assoc_type', 32)->nullable();
            $table->unsignedBigInteger('assoc_id')->nullable();
            $table->string('genre')->nullable();
            $table->boolean('viewable')->default(false);
            $table->timestamps();

            $table->index(['submission_id', 'file_stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
