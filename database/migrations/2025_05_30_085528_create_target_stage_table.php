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
        Schema::create('target_map_stage', function (Blueprint $table) {
            $table->unsignedBigInteger('id_target');
            $table->unsignedBigInteger('id_stage');

            $table->primary(['id_target', 'id_stage']);

            $table->foreign('id_target')->references('id')->on('targets')->onDelete('cascade');
            $table->foreign('id_stage')->references('id')->on('stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_map_stage');
    }
};
