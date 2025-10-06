<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionRenewalController extends Controller
{
    public function renew(UserSubscription $subscription)
    {
        // Kiểm tra gói có hết hạn chưa
        if (!Carbon::parse($subscription->end_date)->isPast()) {
            return redirect()->back()->with('error', 'Chỉ có thể tái đăng ký gói đã hết hạn!');
        }

        try {
            DB::beginTransaction();

            // Cập nhật thời gian và trạng thái
            $subscription->update([
                'start_date' => now(),
                'end_date' => now()->addDays($subscription->plan->duration_days),
                'status' => 'active'
            ]);

            // Cộng thêm lượt thi cho user
            $user = $subscription->user;
            $user->increment('free_slots', $subscription->plan->attempts);

            DB::commit();
            return redirect()->back()->with('success', 'Tái đăng ký gói thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tái đăng ký gói!');
        }
    }
}