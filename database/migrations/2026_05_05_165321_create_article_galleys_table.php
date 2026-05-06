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
        Schema::create('article_galleys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('label', 32);
            $table->string('locale', 14)->nullable();
            $table->foreignId('submission_file_id')->nullable()->constrained('submission_files')->nullOnDelete();
            $table->string('remote_url')->nullable();
            $table->decimal('sequence', 8, 2)->default(0);
            $table->boolean('is_approved')->default(true);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->index('article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_galleys');
    }
};
