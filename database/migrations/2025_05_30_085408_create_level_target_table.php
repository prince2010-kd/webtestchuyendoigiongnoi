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
        Schema::create('level_map_target', function (Blueprint $table) {
            $table->unsignedBigInteger('id_level');
            $table->unsignedBigInteger('id_target');

            $table->primary(['id_level', 'id_target']);

            $table->foreign('id_level')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('id_target')->references('id')->on('targets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_map_target');
    }
};
