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
        Schema::create('general', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keyword', 255)->default('');
            $table->string('label', 255)->default('');
            $table->text('val')->nullable()->comment('mặc định là ngôn ngữ tiếng việt');
            $table->dateTime('created')->nullable();
            $table->integer('stt')->default(0);
            $table->tinyInteger('public')->default(0);
            $table->string('type', 255)->default('');
            $table->string('group_conf', 255)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general');
    }
};
