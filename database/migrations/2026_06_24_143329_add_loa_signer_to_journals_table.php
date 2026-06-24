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
            $table->string('loa_signer_name')->nullable()->after('open_access_statement');
            $table->string('loa_signer_title')->nullable()->after('loa_signer_name');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['loa_signer_name', 'loa_signer_title']);
        });
    }
};
