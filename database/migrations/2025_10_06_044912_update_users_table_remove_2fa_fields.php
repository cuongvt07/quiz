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
        Schema::table('users', function (Blueprint $table) {
            // Xóa các cột cũ không còn dùng
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'remember_token',
                'current_team_id'
            ]);

            // Thêm cột trạng thái
            $table->string('status')->default('active');

            // 🆕 Thêm cột số lần thi miễn phí
            $table->integer('free_slots')
                ->default(2)
                ->comment('Số lần thi miễn phí, mặc định 2 lần')
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Khôi phục các cột bị xóa
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();

            // Xóa cột mới thêm
            $table->dropColumn(['status', 'free_slots']);
        });
    }
};
