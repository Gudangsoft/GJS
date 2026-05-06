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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('volume')->nullable();
            $table->unsignedSmallInteger('number')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_image_alt_text')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('current')->default(false);
            $table->boolean('show_volume')->default(true);
            $table->boolean('show_number')->default(true);
            $table->boolean('show_year')->default(true);
            $table->boolean('show_title')->default(false);
            $table->enum('access_status', ['open', 'subscription'])->default('open');
            $table->string('doi')->nullable()->unique();
            $table->timestamp('date_published')->nullable();
            $table->timestamp('date_notified')->nullable();
            $table->timestamps();

            $table->index(['journal_id', 'published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
