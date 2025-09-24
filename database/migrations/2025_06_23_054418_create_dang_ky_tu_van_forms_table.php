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
        Schema::create('dang_ky_tu_van_forms', function (Blueprint $table) {
            $table->id();
        $table->string('hoten');
        $table->string('tuoi')->nullable();
        $table->string('sdt');
        $table->string('email');
        $table->string('khuvuc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dang_ky_tu_van_forms');
    }
};
