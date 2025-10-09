<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy số liệu thống kê để hiển thị ở trang chủ (nếu cần)
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

        return view('frontend.home', compact('stats', 'competencySubjects', 'cognitiveSubjects'));
    }
}