<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        // Lấy loại đề thi từ query string (nếu có)
        $type = $request->get('type');
        
        // Query cơ bản
        $query = Exam::with('subject');
        
        // Lọc theo loại đề thi
        if ($type) {
            $query->whereHas('subject', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        // Lọc theo subject nếu có
        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        // Lấy danh sách đề thi
        $exams = $query->latest()->paginate(12);

        // Lấy danh sách môn học cho sidebar
        $subjects = Subject::withCount('exams')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('frontend.exams.index', compact('exams', 'subjects', 'type'));
    }

    public function show(Exam $exam)
    {
        $exam->load('subject');
        
        // Lấy thống kê của bài thi
        $examStats = DB::table('exam_attempts')
            ->where('exam_id', $exam->id)
            ->selectRaw('COUNT(*) as attempts_count, AVG(score) as average_score')
            ->first();
            
        $exam->attempts_count = $examStats->attempts_count ?? 0;
        $exam->average_score = $examStats->average_score ?? 0;

        // Nếu user đã đăng nhập, lấy lịch sử làm bài
        if (auth()->check()) {
            $userAttempts = ExamAttempt::where('user_id', auth()->id())
                ->where('exam_id', $exam->id)
                ->latest()
                ->take(5)
                ->get();

            $latestAttempt = $userAttempts->first();
        } else {
            $userAttempts = collect();
            $latestAttempt = null;
        }

        return view('frontend.exams.show', compact('exam', 'userAttempts', 'latestAttempt'));
    }

    public function start(Request $request, Exam $exam)
    {
        // Kiểm tra xem có attempt chưa hoàn thành không
        $existingAttempt = ExamAttempt::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->where('is_completed', false)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('exams.continue', $existingAttempt);
        }

        // Tạo attempt mới
        $attempt = ExamAttempt::create([
            'user_id' => auth()->id(),
            'exam_id' => $exam->id,
            'start_time' => now(),
            'end_time' => now()->addMinutes($exam->duration_minutes),
            'is_completed' => false
        ]);

        return redirect()->route('user.exams.take', $attempt);
    }

    public function continue(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->is_completed) {
            return redirect()->route('user.exams.result', $attempt);
        }

        return redirect()->route('user.exams.take', $attempt);
    }
}