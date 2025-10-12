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
        $subscriptions = $user->subscriptions()->latest('created_at')->get();
        $attemptsByExam = $user->examAttempts()->with('exam')->orderByDesc('started_at')->get()->groupBy('exam_id');
        return view('frontend.user.profile', compact('user', 'attempts', 'allAttempts', 'subscriptions', 'attemptsByExam'));
    }
}
