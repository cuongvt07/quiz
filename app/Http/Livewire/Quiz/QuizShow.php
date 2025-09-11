<?php

namespace App\Http\Livewire\Quiz;

use Livewire\Component;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Answer;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;

class QuizShow extends Component
{
    public Category $category;
    public $questions;
    public $currentQuestion = 0;
    public $answers = [];
    public $timeLeft = 1800; // 30 minutes in seconds
    public $finished = false;
    public $totalQuestions = 0;
    public $totalCorrect = 0;
    public $selectedAnswer = null;

    protected $listeners = ['timeUp' => 'finishQuiz'];

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->questions = Question::where('category_id', $category->id)
                                    ->where('is_active', true)
                                    ->with('questionChoices')
                                    ->inRandomOrder()
                                    ->get();
        $this->totalQuestions = $this->questions->count();
        
        // Initialize answers array with nulls
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = null;
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestion < $this->totalQuestions - 1) {
            $this->currentQuestion++;
            $this->selectedAnswer = null;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestion > 0) {
            $this->currentQuestion--;
            $this->selectedAnswer = $this->answers[$this->questions[$this->currentQuestion]->id] ?? null;
        }
    }

    public function selectAnswer($questionId, $choiceId)
    {
        $this->answers[$questionId] = $choiceId;
        $this->selectedAnswer = $choiceId;
    }

    public function finishQuiz()
    {
        $this->validate([
            'answers.*' => 'required',
        ], [
            'answers.*.required' => 'Bạn phải trả lời tất cả các câu hỏi.',
        ]);

        $totalScore = 0;
        $correctAnswers = 0;

        foreach ($this->answers as $questionId => $choiceId) {
            $choice = QuestionChoice::find($choiceId);
            $score = $choice->is_correct ? 1 : 0;
            $totalScore += $score;
            
            if ($score > 0) {
                $correctAnswers++;
            }

            // Save answer
            Answer::create([
                'question_id' => $questionId,
                'question_choice_id' => $choiceId,
                'user_id' => Auth::id(),
                'score' => $score,
                'category' => $this->category->id,
            ]);
        }

        $percentage = ($correctAnswers / $this->totalQuestions) * 100;

        // Save overall score
        Score::create([
            'user_id' => Auth::id(),
            'best_score' => $percentage,
            'best_category' => $this->category->id,
        ]);

        $this->totalCorrect = $correctAnswers;
        $this->finished = true;
        
        $this->dispatchBrowserEvent('quiz-finished', [
            'score' => $percentage,
            'correct' => $correctAnswers,
            'total' => $this->totalQuestions
        ]);
        
        // Redirect to results page
        return redirect()->route('quiz.result', [
            'category' => $this->category->slug,
            'score' => $percentage,
            'correct' => $correctAnswers,
            'total' => $this->totalQuestions
        ]);
    }

    public function render()
    {
        return view('livewire.quiz.quiz-show', [
            'question' => $this->questions[$this->currentQuestion] ?? null,
            'currentIndex' => $this->currentQuestion + 1,
        ]);
    }
}
