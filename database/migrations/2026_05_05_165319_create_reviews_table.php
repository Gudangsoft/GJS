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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_assignment_id')->constrained()->cascadeOnDelete();
            $table->enum('recommendation', [
                'accept', 'pending_revisions', 'resubmit_here', 'resubmit_elsewhere',
                'decline', 'see_comments',
            ])->nullable();
            $table->text('comments_for_author')->nullable();
            $table->text('comments_for_editors')->nullable();
            $table->json('form_responses')->nullable();
            $table->foreignId('reviewed_file_id')->nullable()->constrained('submission_files')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
