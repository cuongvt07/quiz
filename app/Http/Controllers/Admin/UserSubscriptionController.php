<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSubscriptionController extends Controller
{
    /**
     * Hiển thị danh sách Thành viên đăng ký gói
     */
    public function index()
    {
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Tạo đăng ký gói mới cho user
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        DB::transaction(function () use ($request) {
            // Tạo đăng ký mới
            $subscription = UserSubscription::create([
                'user_id' => $request->user_id,
                'plan_id' => $request->plan_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'active'
            ]);

            // Lấy số lượt thi từ gói đăng ký
            $plan = SubscriptionPlan::find($request->plan_id);
            
            // Cộng dồn số lượt vào free_slots của user
            $user = User::find($request->user_id);
            $user->increment('free_slots', $plan->attempts);
        });

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Thành viên đăng ký gói thành công');
    }

    /**
     * Form tạo đăng ký mới
     */
    public function create()
    {
        $users = User::where('role', 'user')->get();
        $plans = SubscriptionPlan::all();
        
        return view('admin.subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Xóa đăng ký gói
     */
    public function destroy(UserSubscription $subscription)
    {
        try {
            DB::beginTransaction();

            $user = $subscription->user;
            $attempts = $subscription->plan->attempts;
            $message = '';

            // Chỉ xử lý trừ lượt nếu gói đang active
            if ($subscription->status === 'active') {
                if ($user->free_slots >= $attempts) {
                    // Đủ lượt để trừ
                    $user->decrement('free_slots', $attempts);
                    $message = "Đã xóa gói đăng ký và trừ {$attempts} lượt thi!";
                } else {
                    // Không đủ lượt, set về 0
                    $user->update(['free_slots' => 0]);
                    $message = "Đã xóa gói đăng ký và đặt lại số lượt thi về 0!";
                }
            } else {
                $message = 'Đã xóa gói đăng ký!';
            }

            // Xóa gói đăng ký
            $subscription->delete();

            DB::commit();
            return redirect()->route('admin.subscriptions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi xóa gói đăng ký: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa gói đăng ký!');
        }
    }

    /**
     * Hiển thị thông tin chi tiết đăng ký
     */
    public function show(UserSubscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        
        return view('admin.subscriptions.show', compact('subscription'));
    }
}