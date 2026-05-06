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
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('review_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'awaiting_response', 'accepted', 'declined',
                'completed', 'cancelled', 'request_removed',
            ])->default('awaiting_response');
            $table->enum('review_method', ['single_blind', 'double_blind', 'triple_blind', 'open'])->default('double_blind');
            $table->unsignedSmallInteger('round')->default(1);
            $table->timestamp('date_assigned')->nullable();
            $table->timestamp('date_notified')->nullable();
            $table->timestamp('date_confirmed')->nullable();
            $table->timestamp('date_due')->nullable();
            $table->timestamp('date_response_due')->nullable();
            $table->timestamp('date_reminded')->nullable();
            $table->timestamp('date_completed')->nullable();
            $table->timestamp('date_cancelled')->nullable();
            $table->unsignedTinyInteger('reminder_was_automatic')->default(0);
            $table->text('competing_interests')->nullable();
            $table->boolean('unconsidered')->default(false);
            $table->timestamps();

            $table->index(['submission_id', 'status']);
            $table->index(['reviewer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_assignments');
    }
};
