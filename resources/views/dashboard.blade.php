<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Chào mừng đến với hệ thống trắc nghiệm</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 rounded-lg shadow-md p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Bài trắc nghiệm</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="mb-6 opacity-90">Làm các bài trắc nghiệm theo danh mục</p>
                        <a href="{{ route('quizzes') }}" class="inline-block px-4 py-2 bg-white text-indigo-600 rounded-lg font-medium hover:bg-gray-100 transition">
                            Bắt đầu làm bài
                        </a>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg shadow-md p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Kết quả của bạn</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <p class="mb-6 opacity-90">Xem lịch sử và kết quả bài thi của bạn</p>
                        <a href="#" class="inline-block px-4 py-2 bg-white text-green-600 rounded-lg font-medium hover:bg-gray-100 transition">
                            Xem kết quả
                        </a>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg shadow-md p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Hồ sơ của bạn</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <p class="mb-6 opacity-90">Xem và cập nhật thông tin cá nhân của bạn</p>
                        <a href="{{ route('profile.show') }}" class="inline-block px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-gray-100 transition">
                            Cập nhật hồ sơ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
