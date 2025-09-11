<?php

namespace App\Http\Livewire\Quiz;

use Livewire\Component;
use App\Models\Category;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;

class QuizResult extends Component
{
    public Category $category;
    public $score;
    public $correct;
    public $total;

    public function mount(Category $category)
    {
        $this->category = $category;
        
        // Get latest score for this user and category
        $latestScore = Score::where('user_id', Auth::id())
                        ->where('best_category', $category->id)
                        ->latest()
                        ->first();
                        
        if ($latestScore) {
            $this->score = $latestScore->best_score;
        } else {
            $this->score = request()->query('score', 0);
        }
        
        $this->correct = request()->query('correct', 0);
        $this->total = request()->query('total', 0);
    }

    public function render()
    {
        return view('livewire.quiz.quiz-result');
    }
}
