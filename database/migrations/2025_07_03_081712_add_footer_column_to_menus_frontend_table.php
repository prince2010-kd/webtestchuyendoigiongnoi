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
        Schema::table('menus_frontend', function (Blueprint $table) {
            $table->unsignedTinyInteger('footer_column')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus_frontend', function (Blueprint $table) {
            $table->dropColumn('footer_column');
        });
    }
};
