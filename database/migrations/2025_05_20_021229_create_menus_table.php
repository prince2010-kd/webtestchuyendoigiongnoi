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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // tiêu đề
            $table->string('url'); // đường dẫn
            $table->unsignedBigInteger('parent_id')->default(0); // id menu cha
            $table->timestamps();

            //$table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
        //sửa
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
