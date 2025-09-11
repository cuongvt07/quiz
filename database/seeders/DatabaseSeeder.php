<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Answer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi QuizSeeder
        $this->call([
            QuizSeeder::class,
        ]);
        
        // Tạo 10 user
        $users = User::factory(10)->create();

        // Tạo 5 category
        $categories = Category::factory(5)->create();

        $categories->each(function ($category) use ($users) {
            // Mỗi category có 20 câu hỏi
            $questions = Question::factory(20)->create([
                'category_id' => $category->id,
            ]);

            $questions->each(function ($question) use ($users, $category) {
                // Mỗi câu hỏi có 4 đáp án lựa chọn
                $choices = QuestionChoice::factory(4)->create([
                    'question_id' => $question->id,
                ]);

                // Random user trả lời
                $users->each(function ($user) use ($question, $choices, $category) {
                    Answer::create([
                        'question_id'        => $question->id,
                        'question_choice_id' => $choices->random()->id,
                        'user_id'            => $user->id,
                        'score'              => rand(0, 100),
                        'category'           => $category->name, // 👈 dùng trực tiếp category đã biết
                    ]);
                });
            });
        });
    }
}
