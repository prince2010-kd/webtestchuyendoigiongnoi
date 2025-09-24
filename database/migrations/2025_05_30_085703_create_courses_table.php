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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_stage');
            $table->string('code');
            $table->string('title');
            $table->text('des')->nullable();
            $table->integer('active')->default(1);
            $table->integer('stt')->default(0);
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('id_stage')->references('id')->on('stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
