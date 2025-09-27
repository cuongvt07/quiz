<ul class="space-y-1">
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
