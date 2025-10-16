@extends('layouts.frontend')

@section('title', $exam->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span class="sr-only">Trang chủ</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <a href="{{ route('exams.list', ['type' => $exam->subject->type]) }}" 
                       class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ $exam->subject->type == 'nang_luc' ? 'Đề thi Năng lực' : 'Đề thi Tư duy' }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $exam->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <!-- Exam Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            {{ $exam->subject->type == 'nang_luc' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ $exam->subject->name }}
                        </span>
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $exam->duration_minutes }} phút
                            </span>
                            <span class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $exam->total_questions }} câu hỏi
                            </span>
                        </div>
                    </div>
                    <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                </div>

                <!-- Instructions -->
                <div class="px-6 py-4 bg-blue-50">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Hướng dẫn làm bài</h2>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bài thi gồm {{ $exam->total_questions }} câu hỏi, thời gian {{ $exam->duration_minutes }} phút
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Mỗi câu hỏi chỉ có một đáp án đúng
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bạn có thể điều hướng giữa các câu hỏi trong quá trình làm bài
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hệ thống sẽ lưu câu trả lời và tự động nộp bài khi hết thời gian
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 mt-8 lg:mt-0">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden sticky top-4">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin bài thi</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tổng lượt thi cả đề</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $exam->attempts_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thời gian làm bài</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $exam->duration_minutes }} phút</dd>
                        </div>
                    </dl>

                    @auth
                        @if($exam->total_questions == 0)
                            <div class="mt-6">
                                <button type="button" disabled
                                        class="w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed">
                                    Đề thi đang update
                                </button>
                            </div>
                        @elseif(auth()->user()->free_slots > 0)
                            <form action="{{ route('user.exams.start', $exam) }}" method="POST" class="mt-6">
                                @csrf
                                <button type="submit"
                                        class="w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Bắt đầu thi
                                </button>
                            </form>
                        @else
                            <div class="mt-6 rounded-md bg-yellow-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            Hết lượt thi
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>
                                                Bạn đã hết lượt thi. Vui lòng nâng cấp gói để có thêm lượt thi.
                                            </p>
                                        </div>
                                        <div class="mt-4">
                                            <div class="-mx-2 -my-1.5 flex">
                                                <a href="{{ route('admin.subscription_plans.index') }}" 
                                                   class="bg-yellow-50 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                                    Nâng cấp gói
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($userAttempts->isNotEmpty())
                            <div class="mt-8 border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử làm bài</h3>
                                <div class="space-y-4">
                                    @foreach($userAttempts as $attempt)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Lần {{ $loop->iteration }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $attempt->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $attempt->score ?? 0 }} điểm
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $attempt->correct_count ?? 0 }}/{{ $exam->total_questions }} câu đúng
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="mt-6 rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Yêu cầu đăng nhập
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>
                                            Bạn cần đăng nhập để bắt đầu làm bài thi này.
                                        </p>
                                    </div>
                                    <div class="mt-4">
                                        <div class="-mx-2 -my-1.5 flex">
                                            <a href="{{ route('login') }}" 
                                               class="bg-yellow-50 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                                Đăng nhập
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection