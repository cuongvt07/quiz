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

        return view('frontend.exams.index', compact('exams', 'subjects', 'type'));
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

        return view('frontend.exams.show', compact('exam', 'canAttempt', 'attempts'));
    }

    /**
     * Bắt đầu làm bài thi
     */
    public function start(Exam $exam)
    {
        $user = Auth::user();

                // Kiểm tra lượt thi
        if (!$exam->canUserAttempt($user)) {
            return redirect()->back()->with('error', 'Bạn đã sử dụng hết 2 lượt thi miễn phí cho đề thi này! Vui lòng nâng cấp gói để tiếp tục.');
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
        if ($attempt->user_id !== Auth::id()) abort(403);
        if ($attempt->isCompleted()) return redirect()->route('user.exams.result', $attempt);

        $attempt->load('exam.questions.choices');
        $exam = $attempt->exam;

        $startedAt = Carbon::parse($attempt->started_at);
        $endAt = $startedAt->copy()->addMinutes($exam->duration_minutes);
        $now = now();

        if ($now->greaterThanOrEqualTo($endAt)) {
            return $this->submit($attempt);
        }

        // Lấy các câu trả lời đã lưu
        $savedAnswers = ExamAttemptAnswer::where('attempt_id', $attempt->id)
            ->pluck('choice_id', 'question_id');

        return view('frontend.user.exams.take', [
            'attempt' => $attempt,
            'exam' => $exam,
            'savedAnswers' => $savedAnswers,
            'endAt' => $endAt->toIso8601String(), // 🔹 gửi chuẩn sang JS
        ]);
    }

    /**
     * Lưu câu trả lời tạm thời trong session
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        // Kiểm tra quyền truy cập
        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Kiểm tra đã hoàn thành chưa
        if ($attempt->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Bài thi đã kết thúc'
            ], 400);
        }

        try {
            // Validate dữ liệu cơ bản
            $request->validate([
                'question_id' => 'required|exists:questions,id',
            ]);

            $questionId = $request->question_id;
            
            // Kiểm tra câu hỏi có thuộc đề thi này không
            $question = $attempt->exam->questions()->with('questionChoices')->find($questionId);
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại trong đề thi này'
                ], 400);
            }

            // Kiểm tra loại câu hỏi
            if ($question->questionChoices->count() == 1) {
                // Câu điền
                $request->validate([
                    'text_answer' => 'nullable|string|max:1000',
                ]);

                $textAnswer = $request->text_answer ?? '';
                $correctAnswer = $question->questionChoices->first();
                
                // Nếu câu trả lời rỗng, xóa câu trả lời
                if (trim($textAnswer) === '') {
                    ExamAttemptAnswer::where('attempt_id', $attempt->id)
                        ->where('question_id', $questionId)
                        ->delete();
                        
                    return response()->json(['success' => true]);
                }

                // So sánh với đáp án không phân biệt hoa thường
                $isCorrect = strtolower(trim($textAnswer)) === strtolower(trim($correctAnswer->text));

                // Lưu câu trả lời
                ExamAttemptAnswer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'choice_id' => $correctAnswer->id,
                        'text_answer' => $textAnswer,
                        'is_correct' => $isCorrect,
                    ]
                );
            } else {
                // Câu chọn
                $request->validate([
                    'choice_id' => 'required|exists:question_choices,id',
                ]);

                $choiceId = $request->choice_id;
                
                // Kiểm tra choice có thuộc question này không
                $choice = $question->questionChoices->find($choiceId);
                if (!$choice) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lựa chọn không hợp lệ'
                    ], 400);
                }

                // Lưu câu trả lời
                ExamAttemptAnswer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'choice_id' => $choiceId,
                        'is_correct' => $choice->is_correct ?? false,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu câu trả lời'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống, vui lòng thử lại'
            ], 500);
        }
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

        // Nếu đã hoàn thành thì chuyển tới trang kết quả
        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Kiểm tra số lượt thi free đã sử dụng
            $usedFreeAttempts = ExamAttempt::where('exam_id', $attempt->exam_id)
                ->where('user_id', $user->id)
                ->where('used_free_slot', true)
                ->count();

            // Nếu đã sử dụng 2 lượt free thì lượt này không tính là free
            $isFreeslot = $usedFreeAttempts < 2;
            
            // Load câu hỏi và câu trả lời
            $attempt->load(['exam.questions.questionChoices', 'examAttemptAnswers']);
            
            $correctCount = 0;
            $wrongCount = 0;
            $totalQuestions = $attempt->exam->total_questions;

            // Đếm số câu đúng/sai từ câu trả lời đã chọn
            foreach ($attempt->exam->questions as $question) {
                $userAnswer = $attempt->examAttemptAnswers->where('question_id', $question->id)->first();
                
                // Kiểm tra loại câu hỏi
                if ($question->questionChoices->count() == 1) {
                    // Câu điền
                    if ($userAnswer && $userAnswer->is_correct) {
                        $correctCount++;
                    } else {
                        $wrongCount++;
                    }
                } else {
                    // Câu chọn
                    $correctChoice = $question->questionChoices->where('is_correct', true)->first();
                    if ($userAnswer && $userAnswer->choice_id === $correctChoice->id) {
                        $correctCount++;
                    } else {
                        $wrongCount++;
                    }
                }
            }

            // Cập nhật kết quả
            $attempt->update([
                'finished_at' => now(),
                'score' => $correctCount,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
                'used_free_slot' => $isFreeslot
            ]);

            // Cập nhật kết quả và đánh dấu đã hoàn thành
            $attempt->update([
                'finished_at' => now(),
                'score' => $correctCount,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
                'used_free_slot' => $isFreeslot
            ]);

            // Nếu là lượt free và còn free_slots thì trừ đi 1
            if ($isFreeslot && $user->free_slots > 0) {
                $user->free_slots = $user->free_slots - 1;
                $user->save();
            }

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

        $attempt->load(['exam.subject', 'exam.questions.questionChoices', 'examAttemptAnswers.choice']);

        return view('frontend.exams.result', compact('attempt'));
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

        return view('frontend.exams.history', compact('attempts', 'type'));
    }
}
