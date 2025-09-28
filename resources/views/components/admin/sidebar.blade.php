<ul class="space-y-1 text-[15px]">
    <!-- Nhóm: Quản lý tài khoản -->
    <li 
        x-data="{ open: {{ request()->routeIs('admin.accounts.*') ? 'true' : 'false' }} }" 
        class="relative"
    >
        <button 
            @click="open = !open"
            class="flex items-center gap-2 px-2 py-2 rounded-md w-full transition hover:bg-blue-100 
                {{ request()->routeIs('admin.accounts.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}"
        >
            <i data-feather="users" class="w-4 h-4"></i>
            <span>Quản lý tài khoản</span>
            <svg 
                :class="{'rotate-180': open}" 
                class="ml-auto w-4 h-4 transition-transform" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Menu con -->
        <ul 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-40"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 max-h-40"
            x-transition:leave-end="opacity-0 max-h-0"
            class="ml-6 mt-1 space-y-1 overflow-hidden"
            style="max-height:10rem"
        >
            <li>
                <a href="{{ route('admin.accounts.admins') }}"
                   class="flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50 
                   {{ request()->routeIs('admin.accounts.admins') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                    <i data-feather="shield" class="w-4 h-4"></i>
                    <span>Quản trị viên</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.accounts.users') }}"
                   class="flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50 
                   {{ request()->routeIs('admin.accounts.users') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                    <i data-feather="user" class="w-4 h-4"></i>
                    <span>Người dùng</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- Dashboard -->
    <li>
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 
           {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="home" class="w-4 h-4"></i> Dashboard
        </a>
    </li>

    <!-- Các menu khác -->
    <li>
        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 
           {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="layers" class="w-4 h-4"></i> Danh mục
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subjects.index') }}"
           class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 
           {{ request()->routeIs('admin.subjects.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="book-open" class="w-4 h-4"></i> Môn học
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subscription_plans.index') }}"
           class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 
           {{ request()->routeIs('admin.subscription_plans.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="dollar-sign" class="w-4 h-4"></i> Gói nâng cấp
        </a>
    </li>
    <li x-data="{ open: {{ request()->routeIs('admin.exams.nangluc') || request()->routeIs('admin.exams.tuduy') ? 'true' : 'false' }} }" class="relative">
        <button @click="open = !open"
            class="flex items-center gap-2 px-2 py-2 rounded-md w-full transition hover:bg-blue-100 {{ request()->routeIs('admin.exams.nangluc') || request()->routeIs('admin.exams.tuduy') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="file-text" class="w-4 h-4"></i>
            <span>Bài thi</span>
            <svg :class="{'rotate-180': open}" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <ul x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-40" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-40" x-transition:leave-end="opacity-0 max-h-0" class="ml-6 mt-1 space-y-1 overflow-hidden" style="max-height:10rem">
            <li>
                <a href="{{ route('admin.exams.nangluc') }}" class="flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50 {{ request()->routeIs('admin.exams.nangluc') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                    <i data-feather="zap" class="w-4 h-4"></i> Đề thi Năng lực
                </a>
            </li>
            <li>
                <a href="{{ route('admin.exams.tuduy') }}" class="flex items-center gap-2 px-2 py-1 rounded hover:bg-blue-50 {{ request()->routeIs('admin.exams.tuduy') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                    <i data-feather="activity" class="w-4 h-4"></i> Đề thi Tư duy
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ route('admin.import.form') }}"
           class="flex items-center gap-2 px-2 py-2 rounded-md transition hover:bg-blue-100 
           {{ request()->routeIs('admin.import.*') ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
            <i data-feather="upload" class="w-4 h-4"></i> Import câu hỏi
        </a>
    </li>

    <!-- Đăng xuất -->
    <li>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-md hover:bg-red-100 text-red-600 transition">
                <i data-feather="log-out" class="w-4 h-4"></i> Đăng xuất
            </button>
        </form>
    </li>
</ul>
