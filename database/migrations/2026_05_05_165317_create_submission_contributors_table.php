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
        Schema::create('submission_contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('orcid')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('country', 2)->nullable();
            $table->text('bio')->nullable();
            $table->string('url')->nullable();
            $table->string('user_group_id')->nullable();
            $table->boolean('primary_contact')->default(false);
            $table->boolean('include_in_browse')->default(true);
            $table->unsignedSmallInteger('sequence')->default(0);
            $table->timestamps();

            $table->index('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_contributors');
    }
};
