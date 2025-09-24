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
        Schema::create('menus_frontend', function (Blueprint $table) {
            $table->id();
    $table->string('title');
    $table->string('url')->nullable();
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->string('position')->default('main'); // main, footer, sidebar
    $table->integer('stt')->default(0); // Thứ tự hiển thị
    $table->boolean('active')->default(1);
    $table->softDeletes();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus_frontend');
    }
};
