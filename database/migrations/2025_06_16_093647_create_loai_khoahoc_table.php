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
        Schema::create('loai_khoahoc', function (Blueprint $table) {
            $table->id();
    $table->string('ten'); // Tên loại, ví dụ: Anh ngữ học thuật
    $table->string('slug')->unique(); // ví dụ: anh-ngu-hoc-thuat
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loai_khoahoc');
    }
};
