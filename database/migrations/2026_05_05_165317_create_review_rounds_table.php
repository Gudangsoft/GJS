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
        Schema::create('review_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round')->default(1);
            $table->enum('status', ['pending', 'awaiting_reviewers', 'reviews_ready', 'reviews_completed', 'reviews_overdue', 'revisions_requested', 'resubmit_for_review', 'sent_to_copyediting', 'accepted', 'declined'])->default('pending');
            $table->timestamps();

            $table->unique(['submission_id', 'round']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_rounds');
    }
};
