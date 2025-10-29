@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Quản lý tài khoản</h1>
    <div>
        <button id="btnAddUser" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
            <i data-feather="plus" class="mr-2"></i> Thêm tài khoản
        </button>
    </div>
</div>
<div class="mb-4">
    <div class="flex gap-2 border-b">
        <a href="{{ route('admin.accounts.admins') }}" class="px-4 py-2 -mb-px border-b-2 {{ (isset($tab) && $tab=='admins') ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-gray-600' }}">Quản trị viên</a>
        <a href="{{ route('admin.accounts.users') }}" class="px-4 py-2 -mb-px border-b-2 {{ (isset($tab) && $tab=='users') ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-gray-600' }}">Người dùng</a>
    </div>
</div>
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Tên</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Vai trò</th>
                <th class="px-4 py-2">Ngày tạo</th>
                <th class="px-4 py-2 text-center">Hành động</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @foreach($users as $user)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2">{{ $user->id }}</td>
                <td class="px-4 py-2">{{ $user->name }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                <td class="px-4 py-2">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-center">
                    <button class="btnEditUser text-blue-600 hover:underline mr-2" data-id="{{ $user->id }}">Sửa</button>
                    <button class="btnDeleteUser text-red-600 hover:underline" data-id="{{ $user->id }}">Xóa</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{!! $users->appends(['role'=>$role])->links() !!}</div>
</div>

<!-- Modal User Form -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button id="closeUserModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
            <i data-feather="x"></i>
        </button>
        <h2 id="userModalTitle" class="text-xl font-bold mb-4">Thêm tài khoản</h2>
        <form id="userForm">
            <input type="hidden" name="id" id="userId">
            <div class="mb-3">
                <label for="userName" class="block font-semibold mb-1">Tên</label>
                <input type="text" name="name" id="userName" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="userEmail" class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" id="userEmail" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="userPassword" class="block font-semibold mb-1">Mật khẩu</label>
                <input type="password" name="password" id="userPassword" class="w-full border rounded px-3 py-2">
                <small id="passwordNote" class="text-gray-500">Để trống nếu không đổi mật khẩu</small>
            </div>
            <div class="mb-3">
                <label for="userRole" class="block font-semibold mb-1">Vai trò</label>
                <select name="role" id="userRole" class="w-full border rounded px-3 py-2" required>
                    <option value="admin">Admin</option>
                    <option value="user">Người dùng</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script src="/js/users.js"></script>
<script>
    const roleEl = document.getElementById('role');
    if (roleEl) {
        roleEl.addEventListener('change', function() {
            const form = document.getElementById('filterForm');
            if (form) form.submit();
        });
    }
    feather.replace();
</script>
@endsection
