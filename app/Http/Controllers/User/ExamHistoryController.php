<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamHistoryController extends Controller
{
    public function index()
    {
        $attempts = ExamAttempt::with(['exam.subject'])
            ->where('user_id', Auth::id())
            ->where('is_completed', true)
            ->latest()
            ->paginate(10);

        return view('user.exam-history.index', compact('attempts'));
    }

    public function show(ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $detailedResults = collect($attempt->answers)->map(function ($answer) {
            $question = $answer->question;
            $userAnswer = $answer->selected_choice;
            $correctChoice = $question->choices->where('is_correct', true)->first();
            
            return [
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_choice' => $correctChoice,
                'is_correct' => $userAnswer && $userAnswer->id === $correctChoice->id
            ];
        });

        return view('user.exam-history.show', [
            'attempt' => $attempt,
            'detailedResults' => $detailedResults
        ]);
    }
}