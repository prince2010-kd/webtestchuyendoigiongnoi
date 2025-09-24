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
        Schema::create('menu_frontend_translations', function (Blueprint $table) {
             $table->id();
    $table->unsignedBigInteger('menu_frontend_id');
    $table->string('locale', 5); // vi, en, etc.
    $table->string('title');
    $table->timestamps();

    $table->foreign('menu_frontend_id')->references('id')->on('menus_frontend')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_frontend_translations');
    }
};
