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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_scholar')->nullable()->after('url');
            $table->string('scopus_id')->nullable()->after('google_scholar');
            $table->string('researchgate')->nullable()->after('scopus_id');
            $table->string('sinta_id')->nullable()->after('researchgate');
            $table->string('semantic_scholar')->nullable()->after('sinta_id');
            $table->string('position')->nullable()->after('affiliation');
            $table->string('department')->nullable()->after('position');
            $table->json('expertise_areas')->nullable()->after('bio');
            $table->integer('h_index')->unsigned()->nullable()->after('expertise_areas');
            $table->integer('total_citations')->unsigned()->nullable()->after('h_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_scholar', 'scopus_id', 'researchgate', 'sinta_id',
                'semantic_scholar', 'position', 'department',
                'expertise_areas', 'h_index', 'total_citations',
            ]);
        });
    }
};
