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
        Schema::table('journals', function (Blueprint $table) {
            $table->string('sinta_id')->nullable()->after('settings');
            $table->string('sinta_level')->nullable()->after('sinta_id');       // S1–S6
            $table->decimal('sinta_score', 6, 2)->nullable()->after('sinta_level');
            $table->decimal('sinta_score_3yr', 6, 2)->nullable()->after('sinta_score');
            $table->string('accreditation_no')->nullable()->after('sinta_score_3yr');
            $table->string('accreditation_period')->nullable()->after('accreditation_no'); // e.g. 2022–2026
            $table->string('doaj_id')->nullable()->after('accreditation_period');
            $table->string('garuda_id')->nullable()->after('doaj_id');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'sinta_id', 'sinta_level', 'sinta_score', 'sinta_score_3yr',
                'accreditation_no', 'accreditation_period', 'doaj_id', 'garuda_id',
            ]);
        });
    }
};
