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
        Schema::table('questions', function (Blueprint $table) {
            // Xóa ràng buộc khóa ngoại trước
            $table->dropForeign(['category_id']);

            // Đổi tên cột
            $table->renameColumn('category_id', 'loai');
        });

        // Đổi kiểu dữ liệu sang string và cho phép null
        Schema::table('questions', function (Blueprint $table) {
            $table->string('loai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Đổi kiểu dữ liệu về integer và cho phép null
        Schema::table('questions', function (Blueprint $table) {
            $table->integer('loai')->nullable()->change();
        });

        // Đổi tên lại về category_id
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('loai', 'category_id');
        });

        // Thêm lại khóa ngoại
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }
};
