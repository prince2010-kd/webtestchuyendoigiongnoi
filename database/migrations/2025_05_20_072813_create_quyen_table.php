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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->integer('menu_id');
            $table->tinyInteger('can_view');
            $table->tinyInteger('can_add');
            $table->tinyInteger('can_edit');
            $table->tinyInteger('can_delete');
            $table->tinyInteger('can_export');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyen');
    }
};
