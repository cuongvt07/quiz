<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubscriptionRevenueExport;
use App\Exports\UserRevenueExport;

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

    /**
     * Xuất báo cáo doanh thu theo tháng
     */
    public function export(Request $request)
    {
        $month = (int) ($request->get('month') ?? now()->month);
        $year = (int) ($request->get('year') ?? now()->year);

        // Lấy danh sách gói và số lượt đăng ký trong tháng (bao gồm gói 0)
        $planCounts = UserSubscription::selectRaw('plan_id, count(*) as count')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('plan_id')
            ->pluck('count', 'plan_id')
            ->toArray();

        $rows = [];
        $plans = SubscriptionPlan::all();
        foreach ($plans as $plan) {
            $price = $plan->price ?? 0;
            $count = intval($planCounts[$plan->id] ?? 0);
            $revenue = $price * $count;

            $rows[] = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'price' => $price,
                'count' => $count,
                'revenue' => $revenue,
            ];
        }

        $fileName = sprintf('bao_cao_doanh_thu_%04d_%02d.xlsx', $year, $month);
        return Excel::download(new SubscriptionRevenueExport($rows, $month, $year), $fileName);
    }

    /**
     * Xuất báo cáo doanh thu theo người dùng
     */
    public function exportUsers(Request $request)
    {
        $month = (int) ($request->get('month') ?? now()->month);
        $year = (int) ($request->get('year') ?? now()->year);

        $subs = UserSubscription::with(['user', 'plan'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $grouped = $subs->groupBy('user_id');
        $rows = [];
        foreach ($grouped as $userId => $items) {
            $user = $items->first()->user;
            $subscriptionsCount = $items->count();
            $totalAttempts = $items->reduce(function ($carry, $item) {
                return $carry + ($item->plan->attempts ?? 0);
            }, 0);
            $revenue = $items->reduce(function ($carry, $item) {
                return $carry + ($item->plan->price ?? 0);
            }, 0);

            $rows[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'subscriptions_count' => $subscriptionsCount,
                'total_attempts' => $totalAttempts,
                'revenue' => $revenue,
            ];
        }

        $fileName = sprintf('bao_cao_doanh_thu_nguoi_dung_%04d_%02d.xlsx', $year, $month);
        return Excel::download(new UserRevenueExport($rows, $month, $year), $fileName);
    }
}