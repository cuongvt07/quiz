<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 min-h-screen" x-data>
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow flex items-center justify-between px-4 h-14">
            <span class="text-lg font-bold text-blue-600">Quiz Admin</span>
            <div class="flex items-center gap-3">
                <span class="text-gray-700 text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" class="w-7 h-7 rounded-full" alt="avatar">
            </div>
        </header>
        <div class="flex flex-1 bg-gray-50">
            <aside class="w-60 bg-white shadow-md p-2 flex-shrink-0 hidden md:block">
                @include('components.admin.sidebar')
            </aside>
            <main class="flex-1 px-4 py-4 w-full">
                @yield('content')
            </main>
        </div>
    </div>
    <script>feather.replace()</script>
    @stack('scripts')
</body>
</html>