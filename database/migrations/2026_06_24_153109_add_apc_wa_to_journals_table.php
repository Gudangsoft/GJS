<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('journals', function (Blueprint $table) {
            $table->boolean('apc_enabled')->default(false)->after('doi_prefix');
            $table->decimal('apc_amount', 10, 2)->nullable()->after('apc_enabled');
            $table->string('apc_currency', 10)->default('IDR')->after('apc_amount');
            $table->text('apc_waiver_policy')->nullable()->after('apc_currency');
            $table->string('wa_contact', 50)->nullable()->after('apc_waiver_policy');
        });
    }
    public function down(): void {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['apc_enabled','apc_amount','apc_currency','apc_waiver_policy','wa_contact']);
        });
    }
};