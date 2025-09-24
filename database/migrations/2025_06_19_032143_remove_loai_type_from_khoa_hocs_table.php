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
        Schema::table('khoa_hocs', function (Blueprint $table) {
        $table->dropColumn(['loai', 'type']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khoa_hocs', function (Blueprint $table) {
        $table->string('loai')->default('section');
        $table->string('type')->nullable();
    });
    }
};
