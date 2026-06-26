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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('locale', 10)->default('id');
            $table->string('discipline')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->unique(['keyword', 'locale']);
            $table->index(['discipline', 'usage_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
