<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamAttemptController extends Controller
{
    /**
     * Hiển thị danh sách user đã thi một đề thi cụ thể
     */
    public function examUsers(Exam $exam)
    {
        $exam->load('subject');
        
        // Lấy danh sách user đã thi đề này (kèm thống kê)
        $users = User::whereHas('examAttempts', function($q) use ($exam) {
                $q->where('exam_id', $exam->id)
                  ->where('used_free_slot', true);
            })
            ->withCount(['examAttempts' => function($q) use ($exam) {
                $q->where('exam_id', $exam->id)
                  ->where('used_free_slot', true);
            }])
            ->with(['examAttempts' => function($q) use ($exam) {
                $q->where('exam_id', $exam->id)
                  ->where('used_free_slot', true)
                  ->whereNotNull('finished_at')
                  ->latest()
                  ->limit(1);
            }])
            ->paginate(15);

        return view('admin.exam-attempts.exam-users', compact('exam', 'users'));
    }

    /**
     * Hiển thị lịch sử thi của một user cho một đề thi cụ thể
     */
    public function userAttempts(Exam $exam, User $user)
    {
        $exam->load('subject');
        
        // Lấy tất cả lượt thi của user cho đề này
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('used_free_slot', true)
            ->with('exam')
            ->orderByDesc('created_at')
            ->paginate(15);

        // Thống kê
        $stats = [
            'total_attempts' => $attempts->total(),
            'completed_attempts' => ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->count(),
            'average_score' => ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->avg('score'),
            'best_score' => ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->max('score'),
        ];

        return view('admin.exam-attempts.user-attempts', compact('exam', 'user', 'attempts', 'stats'));
    }

    /**
     * Hiển thị chi tiết một lần thi cụ thể
     */
    public function attemptDetail(ExamAttempt $attempt)
    {
        $attempt->load([
            'exam.subject',
            'user',
            'answers.question.choices',
            'answers.answer'
        ]);

        // Tính toán thống kê chi tiết
        $questions = $attempt->exam->questions()->with('choices')->get();
        $userAnswers = $attempt->answers->keyBy('question_id');

        $detailedResults = $questions->map(function($question) use ($userAnswers) {
            $userAnswer = $userAnswers->get($question->id);
            $correctChoice = $question->choices->firstWhere('is_correct', true);
            
            return [
                'question' => $question,
                'user_answer' => $userAnswer?->answer,
                'correct_choice' => $correctChoice,
                'is_correct' => $userAnswer && $userAnswer->answer_id === $correctChoice?->id,
            ];
        });

        return view('admin.exam-attempts.attempt-detail', compact('attempt', 'detailedResults'));
    }

    /**
     * Tổng quan tất cả lượt thi (theo loại thi)
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        
        $query = ExamAttempt::with(['exam.subject', 'user'])
            ->where('used_free_slot', true)
            ->whereNotNull('finished_at');

        if ($type && in_array($type, ['nangluc', 'tuduy'])) {
            $query->whereHas('exam.subject', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $attempts = $query->latest()->paginate(20);

        // Thống kê tổng quan
        $stats = [
            'total_attempts' => ExamAttempt::where('used_free_slot', true)->whereNotNull('finished_at')->count(),
            'competency_attempts' => ExamAttempt::where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->whereHas('exam.subject', function($q) {
                    $q->where('type', 'nangluc');
                })->count(),
            'cognitive_attempts' => ExamAttempt::where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->whereHas('exam.subject', function($q) {
                    $q->where('type', 'tuduy');
                })->count(),
            'average_score' => ExamAttempt::where('used_free_slot', true)
                ->whereNotNull('finished_at')
                ->avg('score'),
        ];

        return view('admin.exam-attempts.index', compact('attempts', 'stats', 'type'));
    }

    /**
     * Xóa lượt thi (nếu cần)
     */
    public function destroy(ExamAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            $user = $attempt->user;
            
            // Nếu đã sử dụng lượt thi miễn phí, hoàn lại lượt
            if ($attempt->used_free_slot) {
                $user->increment('free_slots', 1);
            }

            // Xóa các câu trả lời
            $attempt->answers()->delete();
            
            // Xóa lượt thi
            $attempt->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Đã xóa lượt thi và hoàn lại lượt cho user!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi xóa lượt thi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa lượt thi!');
        }
    }
}
