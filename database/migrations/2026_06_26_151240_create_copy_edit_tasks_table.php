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
        Schema::create('copy_edit_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('round')->default(1);
            $table->enum('status', ['pending','assigned','in_progress','awaiting_author','completed'])->default('pending');
            $table->text('editor_notes')->nullable();
            $table->text('copyeditor_notes')->nullable();
            $table->text('author_notes')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'status']);
        });

        Schema::create('copy_edit_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('copy_edit_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submission_file_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['manuscript','proof','author_revision','final'])->default('manuscript');
            $table->unsignedTinyInteger('round')->default(1);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copy_edit_files');
        Schema::dropIfExists('copy_edit_tasks');
    }
};
