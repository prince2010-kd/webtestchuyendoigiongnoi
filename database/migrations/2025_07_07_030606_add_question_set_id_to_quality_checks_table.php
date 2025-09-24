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
        Schema::table('quality_checks', function (Blueprint $table) {
            $table->unsignedBigInteger('question_set_id')->nullable()->after('id');

            $table->foreign('question_set_id')
                  ->references('id')->on('question_sets')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_checks', function (Blueprint $table) {
            $table->dropForeign(['question_set_id']);
            $table->dropColumn('question_set_id');
        });
    }
};
