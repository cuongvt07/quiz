<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Exam, Question, ExamAttempt, ExamAttemptAnswer, Subject, Category, QuestionChoice};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamAttemptsTestSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ExamAttemptAnswer::truncate();
        ExamAttempt::truncate();
        QuestionChoice::truncate();
        Question::truncate();
        Category::truncate();
        Exam::truncate();
        Subject::truncate();
        User::where('email', 'like', 'hocsinh_%@example.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        // 1. Tạo users mẫu
        $users = collect([
            User::create([
                'name' => 'Học sinh A',
                'email' => 'hocsinh_a@example.com',
                'password' => bcrypt('password'),
                'free_slots' => 5
            ]),
            User::create([
                'name' => 'Học sinh B',
                'email' => 'hocsinh_b@example.com',
                'password' => bcrypt('password'),
                'free_slots' => 3
            ]),
            User::create([
                'name' => 'Học sinh C',
                'email' => 'hocsinh_c@example.com',
                'password' => bcrypt('password'),
                'free_slots' => 0
            ])
        ]);

        // 2. Tạo subjects (môn thi)
        $subjects = [
            Subject::create(['name' => 'Năng lực Toán', 'type' => 'nang_luc']),
            Subject::create(['name' => 'Tư duy Logic', 'type' => 'tu_duy'])
        ];

        // 3. Tạo categories và questions
        $categories = [
            Category::create(['name' => 'Đại số']),
            Category::create(['name' => 'Hình học']),
            Category::create(['name' => 'Logic'])
        ];

        // Tạo câu hỏi mẫu cho mỗi category
        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                $question = Question::create([
                    'category_id' => $category->id,
                    'question' => "Câu hỏi {$i} của {$category->name}",
                    'is_active' => true
                ]);

                // Tạo 4 đáp án cho mỗi câu hỏi
                for ($j = 1; $j <= 4; $j++) {
                    QuestionChoice::create([
                        'question_id' => $question->id,
                        'name' => "Đáp án {$j}",
                        'is_correct' => $j === 1 // đáp án 1 là đúng
                    ]);
                }
            }
        }

        // 4. Tạo đề thi mẫu
        $exams = [
            Exam::create([
                'subject_id' => $subjects[0]->id,
                'title' => 'Đề thi Năng lực Toán số 1',
                'duration_minutes' => 45,
                'total_questions' => 10
            ]),
            Exam::create([
                'subject_id' => $subjects[1]->id,
                'title' => 'Đề thi Tư duy Logic số 1',
                'duration_minutes' => 30,
                'total_questions' => 10
            ])
        ];

        // Gán câu hỏi vào đề thi
        foreach ($exams as $exam) {
            $questions = Question::inRandomOrder()->limit(10)->get();
            $exam->questions()->attach($questions->pluck('id'));
        }

        // 5. Tạo các lượt thi mẫu với các trạng thái khác nhau
        $now = Carbon::now();

        foreach ($exams as $exam) {
            foreach ($users as $user) {
                // Lượt thi hoàn thành
                $attempt1 = ExamAttempt::create([
                    'exam_id' => $exam->id,
                    'user_id' => $user->id,
                    'started_at' => $now->copy()->subHours(2),
                    'finished_at' => $now->copy()->subHours(1),
                    'score' => rand(5, 10),
                    'correct_count' => rand(5, 10),
                    'wrong_count' => rand(0, 5),
                    'used_free_slot' => true
                ]);

                // Tạo câu trả lời cho lượt thi hoàn thành
                foreach ($exam->questions as $question) {
                    $correctChoice = $question->questionChoices->firstWhere('is_correct', true);
                    $selectedChoice = rand(0, 1) ? $correctChoice : $question->questionChoices->firstWhere('is_correct', false);
                    
                    ExamAttemptAnswer::create([
                        'attempt_id' => $attempt1->id,
                        'question_id' => $question->id,
                        'choice_id' => $selectedChoice->id,
                        'is_correct' => $selectedChoice->is_correct
                    ]);
                }

                // Một số lượt thi đang làm
                if ($user->free_slots > 0) {
                    ExamAttempt::create([
                        'exam_id' => $exam->id,
                        'user_id' => $user->id,
                        'started_at' => $now->copy()->subMinutes(15),
                        'finished_at' => null,
                        'used_free_slot' => true
                    ]);
                }
            }
        }
    }
}