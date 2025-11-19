<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ExamAttempt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExamSummaryExport;
use Carbon\Carbon;

class UsersController extends Controller
{
    // Hiển thị danh sách quản trị viên
    public function admins(Request $request)
    {
        $role = 'admin';
        $query = User::where('role', $role);
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }
        $users = $query->orderByDesc('id')->paginate(10);
        $tab = 'admins';
        return view('admin.users.index', compact('users', 'tab', 'role'));
    }

    // Hiển thị danh sách người dùng
    public function users(Request $request)
    {
        $role = 'user';
        $query = User::where('role', $role);
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }
        $users = $query->orderByDesc('id')->paginate(10);
        $tab = 'users';
        return view('admin.users.index', compact('users', 'tab', 'role'));
    }
    public function index(Request $request)
    {
        $role = $request->get('role');
        $query = User::query();
        if ($role) {
            $query->where('role', $role);
        }
        $users = $query->orderByDesc('id')->paginate(10);
        return view('admin.users.index', compact('users', 'role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);
    }
    // Xuất danh sách tài khoản ra Excel
    public function export(Request $request)
    {
        $query = User::query();
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }
        $users = $query->orderByDesc('id')->get();

        // Chuẩn bị dữ liệu cho Excel
        $data = $users->map(function($user) {
            return [
                'ID' => $user->id,
                'Tên' => $user->name,
                'Email' => $user->email,
                'Vai trò' => $user->role,
                'Ngày tạo' => $user->created_at->format('d/m/Y H:i'),
            ];
        });

        // Xuất file Excel
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GenericArrayExport($data->toArray(), 'Danh sách tài khoản'), 'users.xlsx');
    }

    // Xuất báo cáo kết quả thi của 1 người dùng
    public function exportReport(Request $request, User $user)
    {
        // Optional: support month/year or start/end filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $month = $request->get('month');
        $year = $request->get('year');

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::parse($startDate)->endOfDay();
            $periodLabel = sprintf('%s - %s', $start->format('d/m/Y'), $end->format('d/m/Y'));
        } elseif ($month && $year) {
            $start = Carbon::createFromDate((int)$year, (int)$month, 1)->startOfDay();
            $end = (clone $start)->endOfMonth()->endOfDay();
            $periodLabel = sprintf('Tháng %d/%d', (int)$month, (int)$year);
        } else {
            $start = null;
            $end = null;
            $periodLabel = '';
        }

        $query = ExamAttempt::where('user_id', $user->id)->whereNotNull('finished_at')->with('exam');
        if ($start && $end) {
            $query->whereBetween('started_at', [$start, $end]);
        }
        $attempts = $query->get();

        $grouped = $attempts->groupBy('exam_id');

        $rows = $grouped->map(function($group) {
            $exam = $group->first()->exam;
            $maxScore = $group->max('score');
            $attemptsCount = $group->count();
            $bestAttempt = $group->where('score', $maxScore)->sortByDesc('finished_at')->first();
            $duration = optional($bestAttempt)->duration_in_minutes;
            return [
                'exam_title' => $exam?->title ?? '---',
                'max_score' => $maxScore,
                'duration_minutes' => $duration,
                'attempts_count' => $attemptsCount,
            ];
        })->values()->toArray();

        $fileName = sprintf('bao_cao_nguoi_dung_%s_%s.xlsx', str_replace(' ', '_', strtolower($user->name)), now()->format('Ymd'));
        return Excel::download(new UserExamSummaryExport($rows, $user->name, $periodLabel), $fileName);
    }
}
