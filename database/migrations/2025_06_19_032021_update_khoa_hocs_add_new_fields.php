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
        // Thêm các cột mới
        $table->text('mo_ta_ngan')->nullable()->after('slug');
        $table->longText('noi_dung')->nullable()->after('mo_ta');
        $table->json('sections')->nullable()->after('noi_dung');

        $table->string('meta_title')->nullable()->after('sections');
        $table->string('meta_keywords')->nullable()->after('meta_title');
        $table->text('meta_description')->nullable()->after('meta_keywords');
        $table->string('meta_new_keyword')->nullable()->after('meta_description');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khoa_hocs', function (Blueprint $table) {
        // Xóa các cột nếu rollback
        $table->dropColumn([
            'mo_ta_ngan',
            'noi_dung',
            'sections',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'meta_new_keyword',
        ]);
    });
    }
};
