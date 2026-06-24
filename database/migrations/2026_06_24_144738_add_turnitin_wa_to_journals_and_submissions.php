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
            $table->string('turnitin_api_key')->nullable()->after('loa_signer_title');
            $table->string('turnitin_account_id')->nullable()->after('turnitin_api_key');
            $table->string('wa_api_token')->nullable()->after('turnitin_account_id');
            $table->string('wa_sender_number')->nullable()->after('wa_api_token');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->float('similarity_score')->nullable()->after('doi');
            $table->timestamp('similarity_checked_at')->nullable()->after('similarity_score');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['turnitin_api_key','turnitin_account_id','wa_api_token','wa_sender_number']);
        });
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['similarity_score','similarity_checked_at']);
        });
    }
};
