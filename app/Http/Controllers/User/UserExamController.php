<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserExamController extends Controller
{
    /**
     * Hiển thị danh sách đề thi theo loại
     */
    public function index(Request $request)
    {
        $type = $request->get('type', Subject::TYPE_COMPETENCY);
        
        // Validate type
        if (!in_array($type, [Subject::TYPE_COMPETENCY, Subject::TYPE_COGNITIVE])) {
            $type = Subject::TYPE_COMPETENCY;
        }

        $subjects = Subject::where('type', $type)
            ->withCount('exams')
            ->orderBy('name')
            ->get();

        $exams = Exam::with(['subject'])
            ->whereHas('subject', function($q) use ($type) {
                $q->where('type', $type);
            })
            ->paginate(12);

        return view('user.exams.index', compact('exams', 'subjects', 'type'));
    }

    /**
     * Hiển thị chi tiết đề thi trước khi bắt đầu
     */
    public function show(Exam $exam)
    {
        $exam->load(['subject', 'questions']);
        
        $user = Auth::user();
        
        // Kiểm tra số lượt thi còn lại
        $canAttempt = $exam->canUserAttempt($user);
        
        // Lấy lịch sử thi của user
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('used_free_slot', true)
            ->orderByDesc('created_at')
            ->get();

        return view('user.exams.show', compact('exam', 'canAttempt', 'attempts'));
    }

    /**
     * Bắt đầu làm bài thi
     */
    public function start(Exam $exam)
    {
        $user = Auth::user();

        // Kiểm tra lượt thi
        if (!$exam->canUserAttempt($user)) {
            return redirect()->back()->with('error', 'Bạn đã hết lượt thi! Vui lòng nâng cấp gói để tiếp tục.');
        }

        try {
            DB::beginTransaction();

            // Tạo lượt thi mới
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'used_free_slot' => true,
            ]);

            // Trừ lượt thi của user
            $user->decrement('free_slots', 1);

            DB::commit();

            return redirect()->route('user.exams.take', $attempt);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi bắt đầu thi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi bắt đầu bài thi!');
        }
    }

    /**
     * Trang làm bài thi
     */
    public function take(ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra đã hoàn thành chưa
        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        $exam = $attempt->exam->load(['questions.choices']);
        
        // Lấy câu trả lời đã lưu (nếu có)
        $savedAnswers = $attempt->answers()->pluck('choice_id', 'question_id');

        return view('user.exams.take', compact('attempt', 'exam', 'savedAnswers'));
    }

    /**
     * Lưu câu trả lời
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra đã hoàn thành chưa
        if ($attempt->isCompleted()) {
            return response()->json(['error' => 'Bài thi đã kết thúc'], 400);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'choice_id' => 'required|exists:question_choices,id',
        ]);

        // Lưu hoặc cập nhật câu trả lời
        ExamAttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'choice_id' => $validated['choice_id'],
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Nộp bài thi
     */
    public function submit(ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra đã hoàn thành chưa
        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        try {
            DB::beginTransaction();

            // Tính điểm
            $answers = $attempt->answers()->with('answer')->get();
            $correctCount = 0;
            $wrongCount = 0;

            foreach ($answers as $answer) {
                if ($answer->choice && $answer->choice->is_correct) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            }

            // Cập nhật kết quả
            $attempt->update([
                'finished_at' => now(),
                'score' => $correctCount,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
            ]);

            DB::commit();

            return redirect()->route('user.exams.result', $attempt)
                ->with('success', 'Nộp bài thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi nộp bài thi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi nộp bài!');
        }
    }

    /**
     * Xem kết quả bài thi
     */
    public function result(ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['exam.subject', 'answers.question.choices', 'answers.answer']);

        return view('user.exams.result', compact('attempt'));
    }

    /**
     * Lịch sử thi của user
     */
    public function history(Request $request)
    {
        $type = $request->get('type');
        
        $query = ExamAttempt::with(['exam.subject'])
            ->where('user_id', Auth::id())
            ->where('used_free_slot', true)
            ->whereNotNull('finished_at');

        if ($type && in_array($type, [Subject::TYPE_COMPETENCY, Subject::TYPE_COGNITIVE])) {
            $query->whereHas('exam.subject', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $attempts = $query->orderByDesc('finished_at')->paginate(15);

        return view('user.exams.history', compact('attempts', 'type'));
    }
}
