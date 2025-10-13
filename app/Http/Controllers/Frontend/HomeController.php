<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy số liệu thống kê để hiển thị ở trang chủ
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_exams' => \App\Models\Exam::count(),
            'total_attempts' => \App\Models\ExamAttempt::count(),
        ];

        // Lấy danh sách môn học cho mỗi loại đề thi
        $competencySubjects = Subject::where('type', 'nang_luc')
            ->withCount('exams')
            ->orderBy('name')
            ->get();

        $cognitiveSubjects = Subject::where('type', 'tu_duy')
            ->withCount('exams')
            ->orderBy('name')
            ->get();

        // Lấy danh sách đề thi cho filter
        $exams = \App\Models\Exam::orderBy('title')->get();
        
        // Lấy tháng được chọn hoặc mặc định là tháng hiện tại
        $selectedMonth = request('month', now()->month);
        $selectedYear = request('year', now()->year);
        $selectedExam = request('exam_id');

        // Query lấy top 3 người thi xuất sắc
        $topAttempts = \App\Models\ExamAttempt::with(['user', 'exam'])
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->when($selectedExam, function($query) use ($selectedExam) {
                return $query->where('exam_id', $selectedExam);
            })
            ->orderByDesc('score')
            ->orderBy('finished_at') // Nếu cùng điểm thì lấy người làm nhanh hơn
            ->limit(3)
            ->get();

        // Top 3 người dùng thi nhiều nhất
        $topUsers = \App\Models\User::withCount('examAttempts')
            ->orderByDesc('exam_attempts_count')
            ->limit(3)
            ->get();

        // Top 3 người dùng mua gói nhiều nhất
        $topSubscribers = \App\Models\User::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->limit(3)
            ->get();

        // Tạo danh sách tháng cho filter
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        return view('frontend.home', compact(
            'stats', 
            'competencySubjects', 
            'cognitiveSubjects',
            'topAttempts',
            'topUsers',
            'topSubscribers',
            'exams',
            'months',
            'selectedMonth',
            'selectedYear',
            'selectedExam'
        ));
    }
}