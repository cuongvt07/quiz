<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category 1: Lập trình PHP
        $phpCategory = Category::create([
            'name' => 'Lập trình PHP',
            'slug' => 'lap-trinh-php',
        ]);
        
        // Câu hỏi 1
        $question1 = Question::create([
            'category_id' => $phpCategory->id,
            'question' => 'PHP là viết tắt của?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Personal Home Page',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'PHP: Hypertext Preprocessor',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Private Home Page',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Preprocessed Hypertext Page',
            'is_correct' => false,
        ]);
        
        // Câu hỏi 2
        $question2 = Question::create([
            'category_id' => $phpCategory->id,
            'question' => 'Trong PHP, cách để khai báo một biến là?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => '$name',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => '&name',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => 'var name',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => '#name',
            'is_correct' => false,
        ]);
        
        // Câu hỏi 3
        $question3 = Question::create([
            'category_id' => $phpCategory->id,
            'question' => 'Hàm nào sau đây dùng để kiểm tra một biến đã được định nghĩa hay chưa trong PHP?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'is_defined()',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'exists()',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'isset()',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'defined()',
            'is_correct' => false,
        ]);
        
        // Category 2: Laravel Framework
        $laravelCategory = Category::create([
            'name' => 'Laravel Framework',
            'slug' => 'laravel-framework',
        ]);
        
        // Câu hỏi 1
        $question1 = Question::create([
            'category_id' => $laravelCategory->id,
            'question' => 'Laravel được phát triển bởi ai?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Taylor Otwell',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Evan You',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Jeffrey Way',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question1->id,
            'name' => 'Fabien Potencier',
            'is_correct' => false,
        ]);
        
        // Câu hỏi 2
        $question2 = Question::create([
            'category_id' => $laravelCategory->id,
            'question' => 'Lệnh Artisan nào dùng để tạo controller trong Laravel?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => 'php artisan create:controller',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => 'php artisan controller:create',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => 'php artisan make:controller',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question2->id,
            'name' => 'php artisan new:controller',
            'is_correct' => false,
        ]);
        
        // Câu hỏi 3
        $question3 = Question::create([
            'category_id' => $laravelCategory->id,
            'question' => 'Trong Laravel, đâu là file chứa cấu hình kết nối cơ sở dữ liệu?',
            'is_active' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => '.env',
            'is_correct' => true,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'config.php',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'database.json',
            'is_correct' => false,
        ]);
        
        QuestionChoice::create([
            'question_id' => $question3->id,
            'name' => 'app.php',
            'is_correct' => false,
        ]);
    }
}
