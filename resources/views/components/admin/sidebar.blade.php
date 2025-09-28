<ul class="space-y-1">
    <li x-data="{ open: {{ request()->routeIs('admin.accounts.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center gap-2 px-2 py-2 rounded-md w-full transition hover:bg-blue-100 {{ request()->routeIs('admin.accounts.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="users"></i> Quản lý tài khoản
            <svg :class="{'rotate-180': open}" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="open" class="ml-6 mt-1 space-y-1">
            <a href="{{ route('admin.accounts.admins') }}" class="block px-2 py-1 rounded hover:bg-blue-50 {{ request()->routeIs('admin.accounts.admins') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                <i data-feather="shield"></i> Quản trị viên
            </a>
            <a href="{{ route('admin.accounts.users') }}" class="block px-2 py-1 rounded hover:bg-blue-50 {{ request()->routeIs('admin.accounts.users') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                <i data-feather="user"></i> Người dùng
            </a>
        </div>
    </li>
    <li>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="home"></i> Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="layers"></i> Danh mục
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subjects.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.subjects.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="book-open"></i> Môn học
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subscription_plans.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.subscription_plans.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="dollar-sign"></i> Gói nâng cấp
        </a>
    </li>
    <li>
        <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.exams.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="file-text"></i> Bài thi
        </a>
    </li>
    <li>
        <a href="{{ route('admin.import.form') }}" class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 {{ request()->routeIs('admin.import.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="upload"></i> Import câu hỏi
        </a>
    </li>
    <li>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-md hover:bg-red-100 text-red-600 transition">
                <i data-feather="log-out"></i> Đăng xuất
            </button>
        </form>
    </li>
</ul>
