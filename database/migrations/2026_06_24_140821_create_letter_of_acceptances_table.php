<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_of_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users');
            $table->string('loa_number')->unique();
            $table->string('article_title');
            $table->json('authors');
            $table->enum('status', ['draft', 'issued', 'revoked'])->default('draft');
            $table->text('notes')->nullable();
            $table->date('acceptance_date');
            $table->date('expected_publication_date')->nullable();
            $table->string('volume')->nullable();
            $table->string('number')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_of_acceptances');
    }
};
