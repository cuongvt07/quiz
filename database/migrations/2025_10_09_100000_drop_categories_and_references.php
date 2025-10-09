<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa foreign key từ questions trước
        if (Schema::hasColumn('questions', 'category_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        // Xóa bảng categories
        Schema::dropIfExists('categories');
    }

    public function down(): void
    {
        // Tạo lại bảng categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Thêm lại foreign key cho questions
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->constrained();
        });
    }
};