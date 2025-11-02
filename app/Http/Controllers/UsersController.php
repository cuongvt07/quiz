<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
