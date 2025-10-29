<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamAttempt::with(['exam.subject'])
            ->where('user_id', Auth::id());
            
        // Lọc theo đề thi nếu có
        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $attempts = $query->latest()->paginate(10);

        return view('frontend.user.exam-history.index', compact('attempts'));
    }

    public function show(ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load([
            'exam.subject',
            'answers.question.choices',
            'exam.questions.choices'
        ]);

        // Lấy câu trả lời của lượt thi này
        $answers = $attempt->answers()
            ->with(['question.choices', 'choice'])
            ->get()
            ->keyBy('question_id');

        // Map với các câu hỏi trong đề thi
            $detailedResults = $attempt->exam->questions->map(function ($question) use ($answers) {
                $answer = $answers->get($question->id);
                $correctChoice = $question->choices->firstWhere('is_correct', 1);
                // The ExamAttemptAnswer model defines a 'choice' relation; use it to get the selected choice model
                $userChoice = $answer?->choice;
                $hasAnswer = !is_null($userChoice) || !is_null($answer?->text_answer);

                return [
                    'question' => $question,
                    'user_answer' => $userChoice ?: $answer, // keep the choice model if available, otherwise the answer (for text answers)
                    'correct_choice' => $correctChoice,
                    'is_correct' => $answer ? (bool) $answer->is_correct : false,
                    'answered' => $hasAnswer
                ];
            });

        // Tính toán số liệu thống kê
        $stats = [
            'total_questions' => $attempt->exam->questions->count(),
            'correct_count' => $detailedResults->where('is_correct', 1)->count(),
            'wrong_count' => $detailedResults->where('answered', true)->where('is_correct', false)->count() + $detailedResults->where('answered', false)->count(),
            'unanswered_count' => $detailedResults->where('answered', false)->count()
        ];

        // Cập nhật lại attempt nếu chưa có thông tin
        if (!$attempt->correct_count) {
            $attempt->update([
                'score' => $stats['correct_count'],
                'correct_count' => $stats['correct_count']
            ]);
        }

        return view('frontend.user.exam-history.show', [
            'attempt' => $attempt,
            'detailedResults' => $detailedResults,
            'stats' => $stats
        ]);
    }
}