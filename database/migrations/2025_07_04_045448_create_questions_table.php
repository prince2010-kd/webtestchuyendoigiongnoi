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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content'); // Nội dung câu hỏi có dấu
            $table->string('correct_answer'); // Đáp án đúng
            $table->text('explanation')->nullable(); // Giải thích (tuỳ chọn)
            $table->integer('order')->default(0); // Thứ tự câu hỏi
            $table->boolean('is_active')->default(true); // Kích hoạt
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
