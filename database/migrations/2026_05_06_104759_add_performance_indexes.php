<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // articles: date_published used in ORDER BY on public pages
        Schema::table('articles', function (Blueprint $table) {
            $table->index('date_published');
        });

        // issues: current flag queried on journal homepage
        Schema::table('issues', function (Blueprint $table) {
            $table->index(['journal_id', 'current']);
        });
    }

    public function down(): void
    {
        Schema::table('articles', fn ($t) => $t->dropIndex(['date_published']));
        Schema::table('issues', fn ($t) => $t->dropIndex(['journal_id', 'current']));
    }
};
