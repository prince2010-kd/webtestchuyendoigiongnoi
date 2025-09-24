<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('discount_percent')->nullable();
            $table->enum('status', ['con_hang', 'het_hang', 'sap_ve'])->default('con_hang');
            $table->string('main_image')->nullable();
            $table->json('images')->nullable();
            $table->json('features')->nullable();
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->string('shipping_note')->nullable();
            $table->json('support_policy')->nullable();
            $table->integer('stt')->default(0);
            $table->boolean('trangthai')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
