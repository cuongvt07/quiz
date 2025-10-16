<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $attempts = $user->examAttempts()->latest('started_at')->take(5)->get();
        $allAttempts = $user->examAttempts;
        $subscriptions = $user->subscriptions()->orderByDesc('created_at')->get();
        $attemptsByExam = $user->examAttempts()->with('exam')->orderByDesc('started_at')->get()->groupBy('exam_id');

        // Thống kê theo từng đề thi
        $examStats = $attemptsByExam->map(function($examAttempts, $examId) {
            $exam = $examAttempts->first()->exam;
            return [
                'exam_id' => $examId,
                'title' => $exam->title ?? 'Đề đã xóa',
                'subject' => optional($exam->subject)->name ?? 'Chưa phân loại',
                'avg_score' => $examAttempts->avg('score') ?? 0,
                'max_score' => $examAttempts->max('score') ?? 0,
                'count' => $examAttempts->count(),
            ];
        });
        return view('frontend.user.profile', compact('user', 'attempts', 'allAttempts', 'subscriptions', 'attemptsByExam', 'examStats'));
    }
}
