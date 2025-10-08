<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        return view('admin.dashboard', compact(
            'stats',
            'attemptsByMonth',
            'topExams',
            'attemptsByType',
            'usersByMonth',
            'topUsers',
            'topSubscriptions',
            'startDate',
            'endDate'
        ));
    }
}