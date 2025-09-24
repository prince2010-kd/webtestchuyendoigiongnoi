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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('url')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->integer('stt')->default(0)->nullable()->change();
            $table->tinyInteger('active')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('url')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->integer('stt')->default(0)->nullable()->change();
            $table->tinyInteger('active')->default(0)->nullable()->change();
        });
    }
};
