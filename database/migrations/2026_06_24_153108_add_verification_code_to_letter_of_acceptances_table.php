<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('letter_of_acceptances', function (Blueprint $table) {
            $table->string('verification_code', 64)->nullable()->unique()->after('loa_number');
        });
    }
    public function down(): void {
        Schema::table('letter_of_acceptances', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};