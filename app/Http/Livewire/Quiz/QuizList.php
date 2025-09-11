<?php

namespace App\Http\Livewire\Quiz;

use Livewire\Component;
use App\Models\Category;

class QuizList extends Component
{
    public function render()
    {
        $categories = Category::with('question')->get();
        
        return view('livewire.quiz.quiz-list', [
            'categories' => $categories
        ]);
    }
}
