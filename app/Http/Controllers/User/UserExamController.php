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
use Normalizer;

class UserExamController extends Controller
{
    /**
     * Hiển thị danh sách đề thi theo loại
     */
    public function index(Request $request)
    {
        $type = $request->get('type', Subject::TYPE_COMPETENCY);
        
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
        $canAttempt = $exam->canUserAttempt($user);
        
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

        $usedFreeAttempts = ExamAttempt::where('user_id', $user->id)
            ->where('used_free_slot', true)
            ->count();

        $hasFreeSlot = $usedFreeAttempts < 2;

        try {
            DB::beginTransaction();

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'used_free_slot' => $hasFreeSlot,
            ]);

            if ($hasFreeSlot && $user->free_slots > 0) {
                $user->decrement('free_slots');
            }

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
        
        // ✅ Nếu đã hoàn thành -> vào kết quả
        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        $attempt->load('exam.questions.choices');
        $exam = $attempt->exam;

        $startedAt = Carbon::parse($attempt->started_at);
        $endAt = $startedAt->copy()->addMinutes($exam->duration_minutes);
        $now = now();

        // ✅ CHECK 1: Hết giờ -> tự động finish
        if ($now->greaterThanOrEqualTo($endAt)) {
            $this->autoFinishAttempt($attempt);
            return redirect()->route('user.exams.result', $attempt)
                ->with('warning', 'Hết thời gian làm bài! Bài thi đã được tự động nộp.');
        }

        // ✅ CHECK 2: Reload lần 2+ -> tự động finish
        $sessionKey = "exam_attempt_{$attempt->id}_started";
        
        if (session()->has($sessionKey)) {
            // Đã vào trang này rồi -> reload -> finish ngay
            $this->autoFinishAttempt($attempt);
            return redirect()->route('user.exams.result', $attempt)
                ->with('warning', 'Bạn đã reload trang! Bài thi tự động được nộp.');
        }

        // ✅ Lần đầu vào -> đánh dấu session
        session()->put($sessionKey, true);

        return view('frontend.user.exams.take', [
            'attempt' => $attempt,
            'exam' => $exam,
            'endAt' => $endAt->toIso8601String(),
        ]);
    }

    /**
     * Nộp bài thi
     */
    public function submit(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        try {
            DB::beginTransaction();

            $exam = $attempt->exam;
            $exam->load('questions.choices');

            $answers = $request->input('answers', []);
            $correctCount = 0;
            $totalQuestions = $exam->total_questions;

            // Xử lý từng câu trả lời
            foreach ($exam->questions as $question) {
                $questionId = $question->id;
                $userAnswer = $answers[$questionId] ?? null;

                if (!$userAnswer) continue; // Bỏ qua câu không trả lời

                $isCorrect = false;
                $choiceId = null;
                $textAnswer = null;

                // Kiểm tra loại câu hỏi
                if ($question->choices->count() == 1) {

                    $textAnswer = trim($userAnswer);
                    $correctAnswer = trim($question->choices->first()->name);
                    $choiceId = $question->choices->first()->id;

                    // --- Nếu là số (có thể chứa dấu phẩy hoặc chấm) ---
                    $numUser = str_replace(',', '.', $textAnswer);
                    $numCorrect = str_replace(',', '.', $correctAnswer);

                    if (is_numeric($numUser) && is_numeric($numCorrect)) {
                        $isCorrect = abs(floatval($numUser) - floatval($numCorrect)) < 0.0001;
                    } else {
                        // --- So sánh chữ: bỏ dấu, bỏ khoảng trắng dư, không phân biệt hoa/thường ---
                        $normalize = function ($text) {
                            $text = mb_strtolower(trim($text));
                            $text = str_replace([' ', ' '], '', $text); // bỏ khoảng trắng
                            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text); // bỏ dấu tiếng Việt
                            return preg_replace('/[^a-z0-9]/', '', $text); // bỏ ký tự đặc biệt
                        };

                        $isCorrect = $normalize($textAnswer) === $normalize($correctAnswer);
                    }

                } else {
                    // Câu trắc nghiệm
                    $choiceId = $userAnswer;
                    $choice = $question->choices->find($choiceId);
                    
                    if ($choice) {
                        $isCorrect = $choice->is_correct ?? false;
                    }
                }

                // Lưu câu trả lời
                ExamAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'choice_id' => $choiceId,
                    'text_answer' => $textAnswer,
                    'is_correct' => $isCorrect,
                ]);

                if ($isCorrect) {
                    $correctCount++;
                }
            }

            $wrongCount = $totalQuestions - $correctCount;

            // Cập nhật kết quả
            $attempt->update([
                'finished_at' => now(),
                'score' => $correctCount,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount
            ]);

            DB::commit();

            return redirect()->route('user.exams.result', $attempt)
                ->with('success', 'Nộp bài thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi nộp bài thi: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi nộp bài thi!');
        }
    }

    /**
     * Tự động nộp bài khi hết giờ
     */
    private function autoSubmit(ExamAttempt $attempt)
    {
        if ($attempt->isCompleted()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        try {
            DB::beginTransaction();

            $exam = $attempt->exam;
            $exam->load('questions.choices');

            $correctCount = 0;
            $totalQuestions = $exam->total_questions;

            // Không có câu trả lời nào
            $attempt->update([
                'finished_at' => now(),
                'score' => 0,
                'correct_count' => 0,
                'wrong_count' => $totalQuestions
            ]);

            DB::commit();

            return redirect()->route('user.exams.result', $attempt)
                ->with('warning', 'Hết thời gian làm bài! Bài thi đã được tự động nộp.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi tự động nộp bài: ' . $e->getMessage());
            return redirect()->route('user.dashboard')->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Hiển thị kết quả bài thi
     */
    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$attempt->isCompleted()) {
            return redirect()->route('user.exams.take', $attempt);
        }

        $attempt->load(['exam.subject', 'answers.question.choices', 'answers.choice', 'exam.questions.choices']);

        $questions = $attempt->exam->questions;
        $answers = $attempt->answers->keyBy('question_id');

        $detailedResults = $questions->map(function ($question) use ($answers) {
            $userAnswer = $answers->get($question->id);
            $correctChoice = $question->choices->where('is_correct', true)->first();
            return [
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_choice' => $correctChoice,
                'is_correct' => $userAnswer ? $userAnswer->is_correct : false,
                'answered' => $userAnswer && ($userAnswer->choice_id || $userAnswer->text_answer),
            ];
        });

        $unansweredCount = $detailedResults->where('answered', false)->count();
        $correctCount = $detailedResults->where('is_correct', true)->count();
        $wrongCount = $detailedResults->where('answered', true)->where('is_correct', false)->count();

        return view('frontend.user.exams.result', [
            'attempt' => $attempt,
            'detailedResults' => $detailedResults,
            'unansweredCount' => $unansweredCount,
            'correctCount' => $correctCount,
            'wrongCount' => $wrongCount,
        ]);
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

        $attempts = $query->orderByDesc('finished_at')->paginate(10);

        return view('frontend.exams.history', compact('attempts', 'type'));
    }

    /**
     * Chuẩn hóa text để so sánh
     */
    private function normalizeText($text) {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = Normalizer::normalize($text, Normalizer::FORM_D);
        $text = preg_replace('/\p{M}/u', '', $text);
        return $text;
    }

    /**
     * ✅ Tự động finish attempt (không có câu trả lời)
     */
    private function autoFinishAttempt(ExamAttempt $attempt)
    {
        if ($attempt->isCompleted()) {
            return;
        }

        try {
            $totalQuestions = $attempt->exam->total_questions;

            $attempt->update([
                'finished_at' => now(),
                'score' => 0,
                'correct_count' => 0,
                'wrong_count' => $totalQuestions
            ]);

            // ✅ Xóa session tracking
            session()->forget("exam_attempt_{$attempt->id}_started");

        } catch (\Exception $e) {
            \Log::error('Lỗi khi tự động finish: ' . $e->getMessage());
        }
    }
}