@extends('layouts.frontend')

@section('title', 'Trang chủ')

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    .bg-pattern {
        background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMtMy4zMTQgMC02IDIuNjg2LTYgNnMyLjY4NiA2IDYgNiA2LTIuNjg2IDYtNi0yLjY4Ni02LTYtNnptMCAxMGMtMi4yMDkgMC00LTEuNzkxLTQtNHMxLjc5MS00IDQtNCA0IDEuNzkxIDQgNC0xLjc5MSA0LTQgNHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iLjEiLz48L2c+PC9zdmc+');
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" id="floating-elements"></div>

    <!-- Hero Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-6 inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full shadow-lg">
            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
            </svg>
            <span class="text-sm font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Nền tảng thi trắc nghiệm thông minh
            </span>
        </div>

        <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight">
            <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                Khám Phá
            </span>
            <br>
            <span class="text-slate-800">Tiềm Năng Của Bạn</span>
        </h1>

        <p class="text-xl md:text-2xl text-slate-600 mb-12 max-w-3xl mx-auto font-light">
            Đánh giá năng lực và phát triển tư duy với hệ thống kiểm tra thông minh, 
            <span class="font-semibold text-blue-600">hiện đại và chính xác</span>
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('exams.list') }}" class="hero-btn group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                <span class="relative z-10 flex items-center gap-2">
                    Bắt đầu ngay
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>

            <a href="#features" class="px-8 py-4 bg-white text-slate-700 rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-slate-200 hover:border-blue-300">
                Tìm hiểu thêm
            </a>
        </div>

        <!-- Stats -->
        <div class="mt-16 grid grid-cols-3 gap-8 max-w-3xl mx-auto">
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-4 shadow-lg border border-white">
                <div class="flex justify-center mb-2 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-800 mb-1"><span class="text-blue-600">100+</span></div>
                <div class="text-sm text-slate-600">Người dùng mỗi ngày </div>
            </div>

            <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-4 shadow-lg border border-white">
                <div class="flex justify-center mb-2 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-800 mb-1"><span class="text-blue-600">1000+</span></div>
                <div class="text-sm text-slate-600">Câu hỏi đa dạng</div>
            </div>

            <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-4 shadow-lg border border-white">
                <div class="flex justify-center mb-2 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-800 mb-1"><span class="text-blue-600">50K+</span></div>
                <div class="text-sm text-slate-600">Lượt thi thành công</div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-slate-400 rounded-full flex items-start justify-center p-2">
            <div class="w-1 h-3 bg-slate-400 rounded-full"></div>
        </div>
    </div>
</div>

<!-- Rankings Section -->
<div class="py-24 relative bg-gradient-to-b from-slate-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
                Bảng Vinh Danh
            </h2>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                Những thành tích xuất sắc trong cộng đồng
            </p>
        </div>

    <div class="grid md:grid-cols-2 justify-center gap-16">
            <!-- Top Attempts (Highest Scores) -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-yellow-400 via-yellow-500 to-orange-500">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white">Thí Sinh Xuất Sắc</h3>
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                            </svg>
                        </div>
                        
                        <!-- Filters -->
                        <form id="ranking-section" method="GET" class="grid grid-cols-2 gap-2">
                            <div class="relative">
                                <select name="exam_id" class="w-full pl-3 pr-10 py-2 text-sm bg-white text-black rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="">Tất cả đề thi</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ $selectedExam == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <select name="month" class="w-full pl-3 pr-10 py-2 text-sm bg-white text-black rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @foreach($months as $key => $month)
                                        <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>
                                            Tháng {{ $key }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="col-span-2 py-2 px-4 bg-yellow-400 hover:bg-yellow-500 text-black text-sm font-semibold rounded-lg border border-yellow-500 transition-colors">
                                Lọc kết quả
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="p-6 pb-12">
                    @if($topAttempts->isEmpty())
                        <div class="text-center text-gray-500 py-12">
                            Chưa có dữ liệu cho thời gian này
                        </div>
                    @else
                        <div class="relative min-h-[380px] flex items-end justify-center">
                            <!-- Podium Platform Background -->
                            <div class="absolute bottom-0 left-0 right-0 flex items-end justify-center gap-2 px-4">
                                @if(isset($topAttempts[1]))
                                <div class="w-1/3 h-24 bg-gradient-to-t from-yellow-300 via-yellow-200 to-yellow-100 rounded-t-2xl border-t-4 border-yellow-400 shadow-lg"></div>
                                @endif
                                @if(isset($topAttempts[0]))
                                <div class="w-1/3 h-32 bg-gradient-to-t from-yellow-600 via-yellow-500 to-orange-400 rounded-t-2xl border-t-4 border-yellow-700 shadow-xl"></div>
                                @endif
                                @if(isset($topAttempts[2]))
                                <div class="w-1/3 h-20 bg-gradient-to-t from-orange-400 via-orange-300 to-orange-200 rounded-t-2xl border-t-4 border-orange-500 shadow-lg"></div>
                                @endif
                            </div>
                            
                            <!-- Winners on Podium -->
                            <div class="relative w-full flex items-end justify-center gap-2 px-4 pb-2">
                                <!-- Top 2 (Left) -->
                                @if(isset($topAttempts[1]))
                                <div class="w-1/3 flex flex-col items-center mb-24 transform transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                                    <div class="relative mb-3">
                                        <div class="w-20 h-20 bg-gradient-to-br from-yellow-200 to-yellow-400 rounded-full flex items-center justify-center border-4 border-white shadow-xl">
                                            <span class="text-2xl font-bold text-yellow-700">2</span>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                            <span class="text-2xl">🥈</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ Str::limit($topAttempts[1]->user->name, 12) }}</h4>
                                        <p class="text-xl font-extrabold text-yellow-600 mb-0.5">{{ $topAttempts[1]->score }}</p>
                                        <p class="text-xs text-gray-500">điểm</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Top 1 (Center) -->
                                @if(isset($topAttempts[0]))
                                <div class="w-1/3 flex flex-col items-center mb-32 transform transition-all duration-300 hover:scale-110 hover:-translate-y-3">
                                    <div class="relative mb-4">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                                            <span class="text-4xl drop-shadow-lg">👑</span>
                                        </div>
                                        <div class="w-24 h-24 bg-gradient-to-br from-yellow-400 via-yellow-500 to-orange-500 rounded-full flex items-center justify-center border-4 border-white shadow-2xl relative">
                                            <span class="text-3xl font-extrabold text-white">1</span>
                                            <div class="absolute -top-1 -left-1 w-3 h-3 bg-white rounded-full animate-ping"></div>
                                            <div class="absolute -bottom-1 -right-1 w-2 h-2 bg-white rounded-full animate-ping" style="animation-delay: 0.5s"></div>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl">
                                            <span class="text-3xl">🏆</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-extrabold text-gray-900 text-base mb-1">{{ Str::limit($topAttempts[0]->user->name, 12) }}</h4>
                                        <p class="text-2xl font-black text-transparent bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text mb-1">{{ $topAttempts[0]->score }}</p>
                                        <p class="text-xs text-gray-600 font-semibold">điểm</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Top 3 (Right) -->
                                @if(isset($topAttempts[2]))
                                <div class="w-1/3 flex flex-col items-center mb-20 transform transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                                    <div class="relative mb-3">
                                        <div class="w-20 h-20 bg-gradient-to-br from-orange-200 to-orange-400 rounded-full flex items-center justify-center border-4 border-white shadow-xl">
                                            <span class="text-2xl font-bold text-orange-700">3</span>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                            <span class="text-2xl">🥉</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ Str::limit($topAttempts[2]->user->name, 12) }}</h4>
                                        <p class="text-xl font-extrabold text-orange-600 mb-0.5">{{ $topAttempts[2]->score }}</p>
                                        <p class="text-xs text-gray-500">điểm</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Users (Most Exam Attempts) -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-blue-400 via-blue-500 to-indigo-500 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold">Người Thi Nhiều Nhất</h3>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-blue-100">Top 3 người dùng tham gia nhiều kỳ thi nhất</p>
                </div>
                
                <div class="p-6 pb-12">
                    @if($topUsers->isEmpty())
                        <div class="text-center text-gray-500 py-12">
                            Chưa có dữ liệu
                        </div>
                    @else
                        <div class="relative min-h-[380px] flex items-end justify-center">
                            <!-- Podium Platform Background -->
                            <div class="absolute bottom-0 left-0 right-0 flex items-end justify-center gap-2 px-4">
                                @if(isset($topUsers[1]))
                                <div class="w-1/3 h-24 bg-gradient-to-t from-blue-300 via-blue-200 to-blue-100 rounded-t-2xl border-t-4 border-blue-400 shadow-lg"></div>
                                @endif
                                @if(isset($topUsers[0]))
                                <div class="w-1/3 h-32 bg-gradient-to-t from-blue-600 via-blue-500 to-indigo-400 rounded-t-2xl border-t-4 border-blue-700 shadow-xl"></div>
                                @endif
                                @if(isset($topUsers[2]))
                                <div class="w-1/3 h-20 bg-gradient-to-t from-indigo-400 via-indigo-300 to-indigo-200 rounded-t-2xl border-t-4 border-indigo-500 shadow-lg"></div>
                                @endif
                            </div>
                            
                            <!-- Winners on Podium -->
                            <div class="relative w-full flex items-end justify-center gap-2 px-4 pb-2">
                                <!-- Top 2 (Left) -->
                                @if(isset($topUsers[1]))
                                <div class="w-1/3 flex flex-col items-center mb-24 transform transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                                    <div class="relative mb-3">
                                        <div class="w-20 h-20 bg-gradient-to-br from-blue-200 to-blue-400 rounded-full flex items-center justify-center border-4 border-white shadow-xl">
                                            <span class="text-2xl font-bold text-blue-700">2</span>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                            <span class="text-2xl">🥈</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ Str::limit($topUsers[1]->name, 12) }}</h4>
                                        <p class="text-xl font-extrabold text-blue-600 mb-0.5">{{ $topUsers[1]->exam_attempts_count }}</p>
                                        <p class="text-xs text-gray-500">lượt thi</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Top 1 (Center) -->
                                @if(isset($topUsers[0]))
                                <div class="w-1/3 flex flex-col items-center mb-32 transform transition-all duration-300 hover:scale-110 hover:-translate-y-3">
                                    <div class="relative mb-4">
                                        <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                                            <span class="text-4xl drop-shadow-lg">👑</span>
                                        </div>
                                        <div class="w-24 h-24 bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-500 rounded-full flex items-center justify-center border-4 border-white shadow-2xl relative">
                                            <span class="text-3xl font-extrabold text-white">1</span>
                                            <div class="absolute -top-1 -left-1 w-3 h-3 bg-white rounded-full animate-ping"></div>
                                            <div class="absolute -bottom-1 -right-1 w-2 h-2 bg-white rounded-full animate-ping" style="animation-delay: 0.5s"></div>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl">
                                            <span class="text-3xl">🏆</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-extrabold text-gray-900 text-base mb-1">{{ Str::limit($topUsers[0]->name, 12) }}</h4>
                                        <p class="text-2xl font-black text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text mb-1">{{ $topUsers[0]->exam_attempts_count }}</p>
                                        <p class="text-xs text-gray-600 font-semibold">lượt thi</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Top 3 (Right) -->
                                @if(isset($topUsers[2]))
                                <div class="w-1/3 flex flex-col items-center mb-20 transform transition-all duration-300 hover:scale-105 hover:-translate-y-2">
                                    <div class="relative mb-3">
                                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-200 to-indigo-400 rounded-full flex items-center justify-center border-4 border-white shadow-xl">
                                            <span class="text-2xl font-bold text-indigo-700">3</span>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                            <span class="text-2xl">🥉</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ Str::limit($topUsers[2]->name, 12) }}</h4>
                                        <p class="text-xl font-extrabold text-indigo-600 mb-0.5">{{ $topUsers[2]->exam_attempts_count }}</p>
                                        <p class="text-xs text-gray-500">lượt thi</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="py-24 relative bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
                Tại sao chọn chúng tôi?
            </h2>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                Trải nghiệm học tập và kiểm tra với công nghệ tiên tiến
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Feature 1 -->
            <div class="feature-card group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-500 opacity-0 group-hover:opacity-5 rounded-3xl transition-opacity"></div>
                
                <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white mb-6 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-3">
                    Bài kiểm tra chính xác
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    Đánh giá năng lực và tư duy của bạn với độ chính xác cao
                </p>

                <div class="mt-4 w-12 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transform origin-left transition-transform duration-300 scale-x-0 group-hover:scale-x-100"></div>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 opacity-0 group-hover:opacity-5 rounded-3xl transition-opacity"></div>
                
                <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 text-white mb-6 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-3">
                    Kết quả tức thì
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    Nhận phản hồi ngay lập tức sau khi hoàn thành bài thi
                </p>

                <div class="mt-4 w-12 h-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transform origin-left transition-transform duration-300 scale-x-0 group-hover:scale-x-100"></div>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-500 opacity-0 group-hover:opacity-5 rounded-3xl transition-opacity"></div>
                
                <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-white mb-6 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-3">
                    Theo dõi tiến độ
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    Xem sự phát triển của bạn qua từng lần làm bài
                </p>

                <div class="mt-4 w-12 h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full transform origin-left transition-transform duration-300 scale-x-0 group-hover:scale-x-100"></div>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-emerald-500 opacity-0 group-hover:opacity-5 rounded-3xl transition-opacity"></div>
                
                <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-500 text-white mb-6 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-800 mb-3">
                    Thách thức bản thân
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    Vượt qua giới hạn và đạt điểm số cao nhất
                </p>

                <div class="mt-4 w-12 h-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transform origin-left transition-transform duration-300 scale-x-0 group-hover:scale-x-100"></div>
            </div>
        </div>
    </div>
</div>

<!-- Exam Types Section -->
<div class="py-24 bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 rounded-full mb-4">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                </svg>
                <span class="text-sm font-semibold text-blue-600">Chọn loại bài thi</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
                Hai Hướng Phát Triển
            </h2>
            <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                Chọn lộ trình phù hợp để phát triển bản thân
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Năng Lực -->
            <div class="exam-card group relative bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02]">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-500 opacity-5 group-hover:opacity-10 transition-opacity"></div>
                
                <div class="relative p-8 md:p-10">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-500 text-white mb-4 shadow-lg">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800 mb-2">
                                Năng Lực
                            </h3>
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">
                                Competency Assessment
                            </p>
                        </div>
                    </div>

                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Kiểm tra khả năng xử lý thông tin, tư duy logic và giải quyết vấn đề phức tạp
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">500+</div>
                            <div class="text-xs text-slate-500">Câu hỏi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">Nhiều dạng</div>
                            <div class="text-xs text-slate-500">Bài thi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">Đa dạng</div>
                            <div class="text-xs text-slate-500">Mức độ</div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500"></div>
                            <span class="text-slate-700">Phân tích logic</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500"></div>
                            <span class="text-slate-700">Xử lý số liệu</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-blue-600 to-cyan-500"></div>
                            <span class="text-slate-700">Tư duy phản biện</span>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <a href="{{ route('exams.list', ['type' => 'nangluc']) }}" class="block w-full py-4 rounded-2xl font-semibold text-white bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 text-center group/btn">
                        <span class="inline-flex items-center gap-2">
                            Bắt đầu làm bài
                            <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-white/50 to-transparent rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-white/50 to-transparent rounded-full blur-2xl"></div>
            </div>

            <!-- Tư Duy -->
            <div class="exam-card group relative bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02]">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-pink-500 to-rose-500 opacity-5 group-hover:opacity-10 transition-opacity"></div>
                
                <div class="relative p-8 md:p-10">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="inline-flex p-4 rounded-2xl bg-gradient-to-br from-purple-600 via-pink-500 to-rose-500 text-white mb-4 shadow-lg">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-slate-800 mb-2">
                                Tư Duy
                            </h3>
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">
                                Cognitive Test
                            </p>
                        </div>
                    </div>

                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Phát triển khả năng suy luận, phân tích và ra quyết định thông minh
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">500+</div>
                            <div class="text-xs text-slate-500">Câu hỏi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">Nhiều dạng</div>
                            <div class="text-xs text-slate-500">Bài thi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800 mb-1">Đa dạng</div>
                            <div class="text-xs text-slate-500">Mức độ</div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-purple-600 to-rose-500"></div>
                            <span class="text-slate-700">Suy luận logic</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-purple-600 to-rose-500"></div>
                            <span class="text-slate-700">Nhận thức mẫu hình</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-gradient-to-r from-purple-600 to-rose-500"></div>
                            <span class="text-slate-700">Tư duy sáng tạo</span>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <a href="{{ route('exams.list', ['type' => 'tuduy']) }}" class="block w-full py-4 rounded-2xl font-semibold text-white bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 text-center group/btn">
                        <span class="inline-flex items-center gap-2">
                            Bắt đầu làm bài
                            <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-white/50 to-transparent rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-white/50 to-transparent rounded-full blur-2xl"></div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600"></div>
    <div class="absolute inset-0 bg-pattern opacity-20"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Sẵn sàng thử thách bản thân?
        </h2>
        <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">
            Tham gia cùng hàng nghìn người dùng đang phát triển kỹ năng mỗi ngày
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 rounded-2xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                Đăng ký tài khoản ngay <span>( Nhận ngay 2 lượt thi miễn phí )</span>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. HIỆU ỨNG FLOATING ELEMENTS ===
    const floatingEls = document.querySelectorAll('[data-float]');
    floatingEls.forEach(el => {
        const range = parseInt(el.getAttribute('data-float')) || 10;
        const duration = 3000 + Math.random() * 2000;
        const direction = Math.random() > 0.5 ? 1 : -1;

        el.animate([
            { transform: `translateY(0px)` },
            { transform: `translateY(${direction * range}px)` },
            { transform: `translateY(0px)` }
        ], {
            duration: duration,
            iterations: Infinity,
            easing: 'ease-in-out'
        });
    });

    // === 2. HIỆU ỨNG PARALLAX SCROLL ===
    const parallaxEls = document.querySelectorAll('[data-parallax]');
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        parallaxEls.forEach(el => {
            const speed = parseFloat(el.getAttribute('data-parallax')) || 0.3;
            el.style.transform = `translateY(${scrollY * speed}px)`;
        });
    });

    // === 3. HIỆU ỨNG ĐẾM SỐ COUNTER ===
    const counters = document.querySelectorAll('[data-count]');
    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count');
                const current = +counter.innerText.replace(/,/g, '');
                const increment = Math.ceil(target / 60);
                if (current < target) {
                    counter.innerText = (current + increment).toLocaleString();
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        });
    };

    // Kích hoạt khi phần tử visible
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(counter => counterObserver.observe(counter));

    // === 4. FORM LỌC BẢNG XẾP HẠNG ===
    const form = document.getElementById('ranking-section');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = new URLSearchParams(new FormData(form)).toString();
            // Reload lại trang với anchor đến bảng xếp hạng
            window.location.href = `?${query}#ranking-section`;
        });
    }

    // === 5. TỰ ĐỘNG CUỘN XUỐNG PHẦN BẢNG XẾP HẠNG ===
    const scrollToRanking = () => {
        const section = document.getElementById('ranking-section');
        if (section) {
            console.log('Scrolling to:', section);
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            console.warn('Không tìm thấy #ranking-section, thử lại...');
            // Nếu chưa render, thử lại sau 500ms
            setTimeout(scrollToRanking, 500);
        }
    };

    const shouldScroll =
        window.location.hash === '#ranking-section' ||
        window.location.search.includes('exam_id') ||
        window.location.search.includes('month');

    if (shouldScroll) {
        console.log('Scrolling to ranking section...');
        // Chờ toàn bộ trang render xong rồi mới cuộn
        window.addEventListener('load', function() {
            setTimeout(scrollToRanking, 500);
        });
    }
});
</script>
@endpush