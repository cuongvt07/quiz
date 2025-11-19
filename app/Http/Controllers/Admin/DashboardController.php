<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\UserSubscription;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamTypeExport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Lấy thông số filter
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->startOfYear();
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();
        
        Log::info('Dashboard Filter Dates', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);
        
        // Cache key dựa trên filter
        $cacheKey = "dashboard.stats.{$startDate->format('Y-m-d')}.{$endDate->format('Y-m-d')}";
        
        // Thống kê tổng quan (tắt cache tạm thời để debug)
        $stats = [
            'users' => User::count(),
            'exams' => Exam::count(),
            'questions' => DB::table('exam_questions')->count(),
            'attempts' => ExamAttempt::count()
        ];
        
        Log::info('Dashboard Stats', $stats);

        // Thống kê lượt thi theo tháng
        $attemptsByMonth = ExamAttempt::whereBetween('exam_attempts.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(exam_attempts.created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top 5 đề thi có nhiều lượt thi nhất
        $topExams = Exam::withCount(['attempts' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('exam_attempts.created_at', [$startDate, $endDate]);
            }])
            ->having('attempts_count', '>', 0)
            ->orderByDesc('attempts_count')
            ->take(5)
            ->get();

        // Thống kê lượt thi theo loại (năng lực/tư duy)
        try {
            // Kiểm tra cấu trúc bảng
            $subjectTypes = DB::table('subjects')->select('type')->distinct()->get();
            Log::info('Available subject types:', ['types' => $subjectTypes]);

            // Query số lượt thi theo loại
            $query = ExamAttempt::query()
                ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
                ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
                ->whereBetween('exam_attempts.created_at', [$startDate, $endDate])
                ->select('subjects.type', DB::raw('COUNT(*) as total'))
                ->groupBy('subjects.type');

            $attemptsByType = $query->get();
            
            // Log kết quả chi tiết
            Log::info('Attempts By Type Analysis:', [
                'query' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'raw_results' => $attemptsByType->toArray(),
                'date_range' => [
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $endDate->format('Y-m-d H:i:s')
                ]
            ]);

            // Kiểm tra nếu không có dữ liệu
            if ($attemptsByType->isEmpty()) {
                Log::warning('No exam attempts by type found for the given date range');
                $attemptsByType = collect([
                    ['type' => 'nang_luc', 'total' => 0],
                    ['type' => 'tu_duy', 'total' => 0]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in exam types analysis: ' . $e->getMessage());
            // Trả về dữ liệu mặc định nếu có lỗi
            $attemptsByType = collect([
                ['type' => 'nang_luc', 'total' => 0],
                ['type' => 'tu_duy', 'total' => 0]
            ]);
        }

        // Thống kê tài khoản mới theo tháng
        $usersByMonth = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top 5 user tích cực nhất
        $topUsers = User::withCount(['examAttempts' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('exam_attempts_count', '>', 0)
            ->orderByDesc('exam_attempts_count')
            ->take(5)
            ->with(['subscriptions' => function($query) {
                $query->latest();
            }])
            ->get();

        // Top 5 gói đăng ký phổ biến
        $topSubscriptions = UserSubscription::whereBetween('created_at', [$startDate, $endDate])
            ->select('plan_id', DB::raw('COUNT(*) as total'))
            ->with('subscriptionPlan:id,name')
            ->groupBy('plan_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // === Thống kê doanh thu ===
        // Hôm nay
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $todayRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereDate('user_subscriptions.created_at', $today)
            ->sum('subscription_plans.price');
            
        $yesterdayRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereDate('user_subscriptions.created_at', $yesterday)
            ->sum('subscription_plans.price');
            
        $todaySubscriptions = UserSubscription::whereDate('created_at', $today)->count();
        $todayNewUsers = User::whereDate('created_at', $today)->count();
        $todayConversionRate = $todayNewUsers > 0 ? ($todaySubscriptions / $todayNewUsers) * 100 : 0;
        $revenueGrowth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;

        // Tuần này
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $weeklyRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereBetween('user_subscriptions.created_at', [$weekStart, $weekEnd])
            ->sum('subscription_plans.price');

        $lastWeekRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereBetween('user_subscriptions.created_at', [$lastWeekStart, $lastWeekEnd])
            ->sum('subscription_plans.price');

        $weeklySubscriptions = UserSubscription::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $weeklyGrowth = $lastWeekRevenue > 0 ? (($weeklyRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 : 0;

        // Theo ngày trong tuần
        $dailyRevenueData = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereBetween('user_subscriptions.created_at', [$weekStart, $weekEnd])
            ->select(
                DB::raw('DAYOFWEEK(user_subscriptions.created_at) as day'),
                DB::raw('SUM(subscription_plans.price) as revenue')
            )
            ->groupBy('day')
            ->get()
            ->pluck('revenue', 'day')
            ->toArray();

        // Tháng này
        $monthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $monthlyRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereMonth('user_subscriptions.created_at', Carbon::now()->month)
            ->sum('subscription_plans.price');

        $lastMonthRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereBetween('user_subscriptions.created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('subscription_plans.price');

        $monthlySubscriptions = UserSubscription::whereMonth('created_at', Carbon::now()->month)->count();
        $monthlyGrowth = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        $avgDailySubscriptions = $monthlySubscriptions / Carbon::now()->daysInMonth;

        // Doanh thu theo ngày trong tháng
        $dailyRevenueMonth = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereMonth('user_subscriptions.created_at', Carbon::now()->month)
            ->select(
                DB::raw('DAY(user_subscriptions.created_at) as day'),
                DB::raw('SUM(subscription_plans.price) as revenue')
            )
            ->groupBy('day')
            ->get()
            ->pluck('revenue', 'day')
            ->toArray();

        // Năm nay
        $yearStart = Carbon::now()->startOfYear();
        $yearRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereYear('user_subscriptions.created_at', Carbon::now()->year)
            ->sum('subscription_plans.price');

        $yearlySubscriptions = UserSubscription::whereYear('created_at', Carbon::now()->year)->count();
        $avgMonthlySubscriptions = $yearlySubscriptions / Carbon::now()->month;

        // Doanh thu theo quý
        $quarterlyRevenue = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereYear('user_subscriptions.created_at', Carbon::now()->year)
            ->select(
                DB::raw('QUARTER(user_subscriptions.created_at) as quarter'),
                DB::raw('SUM(subscription_plans.price) as revenue')
            )
            ->groupBy('quarter')
            ->get()
            ->pluck('revenue', 'quarter')
            ->toArray();

        // Dữ liệu cho biểu đồ theo giờ trong ngày
        $hourlyRevenue = [];
        // Khởi tạo mảng với 24 giờ, mỗi giờ có doanh thu là 0
        for ($i = 0; $i < 24; $i++) {
            $hourlyRevenue[$i] = 0;
        }
        
        // Lấy dữ liệu thực từ database
        $todaySubscriptions = UserSubscription::join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->whereDate('user_subscriptions.created_at', $today)
            ->select(
                'user_subscriptions.created_at',
                'subscription_plans.price'
            )
            ->get();

        // Phân bổ doanh thu vào đúng giờ
        foreach ($todaySubscriptions as $subscription) {
            $hour = (int) $subscription->created_at->format('G'); // Lấy giờ không có số 0 ở đầu (0-23)
            $hourlyRevenue[$hour] += $subscription->price;
        }

        $revenueStats = [
            'today' => [
                'revenue' => $todayRevenue,
                'growth' => $revenueGrowth,
                'subscriptions' => $todaySubscriptions,
                'conversion_rate' => $todayConversionRate,
                'hourly_data' => $hourlyRevenue
            ],
            'weekly' => [
                'revenue' => $weeklyRevenue,
                'growth' => $weeklyGrowth,
                'subscriptions' => $weeklySubscriptions,
                'daily_data' => $dailyRevenueData
            ],
            'monthly' => [
                'revenue' => $monthlyRevenue,
                'growth' => $monthlyGrowth,
                'subscriptions' => $monthlySubscriptions,
                'avg_daily' => $avgDailySubscriptions,
                'daily_data' => $dailyRevenueMonth
            ],
            'yearly' => [
                'revenue' => $yearRevenue,
                'subscriptions' => $yearlySubscriptions,
                'avg_monthly' => $avgMonthlySubscriptions,
                'quarterly_data' => $quarterlyRevenue
            ]
        ];

        return view('admin.dashboard', compact(
            'stats',
            'attemptsByMonth',
            'topExams',
            'attemptsByType',
            'usersByMonth',
            'topUsers',
            'topSubscriptions',
            'startDate',
            'endDate',
            'revenueStats'
        ));
    }

    /**
     * Export exam attempts by subject type for a month
     */
    public function exportExamTypes(Request $request)
    {
        $month = (int) ($request->get('month') ?? now()->month);
        $year = (int) ($request->get('year') ?? now()->year);

        $data = ExamAttempt::join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
            ->join('subjects', 'exams.subject_id', '=', 'subjects.id')
            ->whereMonth('exam_attempts.created_at', $month)
            ->whereYear('exam_attempts.created_at', $year)
            ->select('subjects.type', DB::raw('COUNT(*) as total'))
            ->groupBy('subjects.type')
            ->get();

        $types = [];
        foreach (Subject::getTypes() as $typeKey => $typeName) {
            $count = optional($data->firstWhere('type', $typeKey))->total ?? 0;
            $types[] = [
                'type' => $typeKey,
                'type_name' => $typeName,
                'count' => (int) $count,
            ];
        }

        $fileName = sprintf('bao_cao_loai_bai_thi_%04d_%02d.xlsx', $year, $month);
        return Excel::download(new ExamTypeExport($types, $month, $year), $fileName);
    }
}