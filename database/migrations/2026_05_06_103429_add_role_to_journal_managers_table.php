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
        Schema::table('journal_managers', function (Blueprint $table) {
            // Drop FKs first so we can modify the unique index
            $table->dropForeign(['journal_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique(['journal_id', 'user_id']);

            $table->string('role')->default('manager')->after('user_id');
            $table->unique(['journal_id', 'user_id', 'role']);

            // Re-add FKs
            $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('journal_managers', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique(['journal_id', 'user_id', 'role']);
            $table->dropColumn('role');
            $table->unique(['journal_id', 'user_id']);
            $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
