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
        Schema::table('scores', function (Blueprint $table) {
            $table->dropForeign(['answer_id']);
            $table->dropColumn('answer_id');
        });

        Schema::dropIfExists('answers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained();
            $table->foreignId('question_choice_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('score');
            $table->string('category');
            $table->timestamps();
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->foreignId('answer_id')->constrained();
        });
    }
};