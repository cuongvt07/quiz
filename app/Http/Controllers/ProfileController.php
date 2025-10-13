<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Lấy tất cả các lượt thi và sắp xếp theo thời gian mới nhất
        $allAttempts = $user->examAttempts()
            ->with(['exam', 'exam.subject'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Nhóm các lượt thi theo đề thi
        $attemptsByExam = $allAttempts->groupBy('exam_id');

        // Tính toán thống kê cho từng đề thi
        $examStats = [];
        foreach ($attemptsByExam as $examId => $attempts) {
            $firstAttempt = $attempts->last(); // Lấy lần thi đầu tiên
            $lastAttempt = $attempts->first(); // Lấy lần thi gần nhất
            $bestAttempt = $attempts->sortByDesc('score')->first();

            $examStats[$examId] = [
                'total_attempts' => $attempts->count(),
                'best_score' => $bestAttempt ? $bestAttempt->score : 0,
                'avg_score' => round($attempts->avg('score'), 1),
                'last_attempt_date' => $lastAttempt ? $lastAttempt->created_at : null,
                'first_score' => $firstAttempt ? $firstAttempt->score : 0,
                'latest_score' => $lastAttempt ? $lastAttempt->score : 0,
                'improvement' => $firstAttempt && $lastAttempt ? 
                    round($lastAttempt->score - $firstAttempt->score, 1) : 0,
                'time_spent' => $attempts->sum('duration'),
            ];
        }

        // Lấy danh sách đăng ký gói
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->get();

            dd($examStats);
        return view('frontend.user.profile', compact(
            'user',
            'allAttempts',
            'attemptsByExam',
            'subscriptions',
            'examStats'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự',
        ]);

        Auth::user()->update([
            'name' => $request->name,
        ]);

        return back()->with('status', 'profile-updated')
                    ->with('message', 'Cập nhật thông tin thành công!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'different:current_password'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated')
                    ->with('message', 'Đổi mật khẩu thành công!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}