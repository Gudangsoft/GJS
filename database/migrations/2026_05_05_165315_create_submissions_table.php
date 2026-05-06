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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'draft', 'submitted', 'queued', 'assigned',
                'review', 'revision_required', 'resubmit',
                'accepted', 'copyediting', 'production',
                'scheduled', 'published', 'declined', 'archived',
            ])->default('draft');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('abstract')->nullable();
            $table->json('keywords')->nullable();
            $table->json('disciplines')->nullable();
            $table->json('subjects')->nullable();
            $table->json('languages')->nullable();
            $table->string('cover_letter_file')->nullable();
            $table->string('locale', 14)->default('id');
            $table->string('doi')->nullable()->unique();
            $table->string('submission_type', 32)->default('article');
            $table->boolean('hide_author')->default(false);
            $table->text('competing_interests')->nullable();
            $table->string('current_publication_id')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['journal_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
