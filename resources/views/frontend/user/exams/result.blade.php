@extends('layouts.frontend')

@section('title', 'Kết quả bài thi')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Kết quả bài thi -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                <h1 class="text-xl font-bold text-blue-900">
                    {{ $attempt->exam->title }}
                </h1>
                <p class="text-sm text-blue-600 mt-1">
                    {{ $attempt->exam->subject->name }} - {{ $attempt->exam->subject->type == 'nang_luc' ? 'Năng lực' : 'Tư duy' }}
                </p>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="p-6">
                <!-- Score Circle -->
                <div class="flex justify-center mb-8">
                    <div class="relative">
                        <svg class="w-32 h-32">
                            <circle
                                class="text-gray-200"
                                stroke-width="10"
                                stroke="currentColor"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                            />
                            <circle
                                class="text-blue-600"
                                stroke-width="10"
                                stroke="currentColor"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                                style="stroke-dasharray: {{ 2 * pi() * 56 }}; stroke-dashoffset: {{ 2 * pi() * 56 * (1 - ($attempt->correct_count / $attempt->exam->total_questions)) }};"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span class="text-2xl font-bold text-blue-600">
                                    {{ $attempt->correct_count }}
                                </span>
                                <span class="text-sm text-gray-600">/{{ $attempt->exam->total_questions }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <!-- Số câu đúng -->
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <span class="text-2xl font-bold text-green-600">{{ $attempt->correct_count }}</span>
                        <p class="text-sm text-green-700 mt-1">Câu đúng</p>
                    </div>

                    <!-- Số câu sai -->
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <span class="text-2xl font-bold text-red-600">{{ $attempt->wrong_count }}</span>
                        <p class="text-sm text-red-700 mt-1">Câu sai</p>
                    </div>
                </div>

                <!-- Thời gian làm bài -->
                <div class="space-y-3 text-sm text-gray-600 mb-8">
                    <div class="flex justify-between items-center">
                        <span>Thời gian bắt đầu:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($attempt->started_at)->format('H:i:s - d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Thời gian kết thúc:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($attempt->finished_at)->format('H:i:s - d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Tổng thời gian làm bài:</span>
                        <span class="font-medium">{{ $attempt->duration_in_minutes }} phút</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Loại lượt thi:</span>
                        <span class="font-medium {{ $attempt->used_free_slot ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $attempt->used_free_slot ? 'Lượt miễn phí' : 'Lượt trả phí' }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('exam-history.show', $attempt) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Xem chi tiết bài làm
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('exams.list') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>

        @if($attempt->used_free_slot && $attempt->user->free_attempts_left <= 0)
            <!-- Promotion Card -->
            <div class="mt-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <h2 class="text-lg font-semibold mb-2">Bạn đã hết lượt thi miễn phí!</h2>
                <p class="mb-4">Nâng cấp tài khoản ngay để:</p>
                <ul class="list-disc list-inside mb-6 space-y-1">
                    <li>Không giới hạn số lần làm đề thi</li>
                    <li>Xem đáp án chi tiết và giải thích</li>
                    <li>Truy cập tất cả các đề thi premium</li>
                </ul>
                <a href="{{ route('subscriptions.index') }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-white rounded-lg text-sm font-medium hover:bg-white hover:text-blue-600 transition-colors">
                    Xem các gói nâng cấp
                    <svg class="ml-2 -mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-lucide="file-text" class="text-blue-600 w-7 h-7"></i>
                <span>Chi tiết lần thi</span>
            </h1>
            <p class="text-gray-600 mt-2">
                <strong>{{ $attempt->user->name }}</strong> - {{ $attempt->exam->title }}
            </p>
        </div>
        <a href="{{ route('exam-history.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại lịch sử
        </a>
    </div>

    {{-- Tổng quan --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
            <p class="text-sm text-gray-600 mb-2">Điểm số</p>
            <p class="text-4xl font-bold text-blue-600">{{ $attempt->score }}</p>
            <p class="text-sm text-gray-500 mt-1">/{{ $attempt->exam->total_questions }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ $attempt->score_percentage }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
            <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-green-600 mb-2"></i>
            <p class="text-sm text-gray-600 mb-1">Đúng</p>
            <p class="text-3xl font-bold text-green-600">{{ $attempt->correct_count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
            <i data-lucide="x-circle" class="w-8 h-8 mx-auto text-red-600 mb-2"></i>
            <p class="text-sm text-gray-600 mb-1">Sai</p>
            <p class="text-3xl font-bold text-red-600">{{ $attempt->wrong_count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
            <i data-lucide="clock" class="w-8 h-8 mx-auto text-purple-600 mb-2"></i>
            <p class="text-sm text-gray-600 mb-1">Thời gian</p>
            <p class="text-2xl font-bold text-purple-600">{{ $attempt->duration_in_minutes ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">phút</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
            <i data-lucide="calendar" class="w-8 h-8 mx-auto text-orange-600 mb-2"></i>
            <p class="text-sm text-gray-600 mb-1">Ngày thi</p>
            <p class="text-lg font-bold text-orange-600">{{ \Carbon\Carbon::parse($attempt->finished_at)->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($attempt->finished_at)->format('H:i') }}</p>
        </div>
    </div>

    {{-- Danh sách câu hỏi --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i data-lucide="list" class="w-5 h-5"></i>
            Chi tiết câu trả lời
        </h2>

        <div class="space-y-6">
            @foreach($detailedResults as $index => $result)
                <div class="border border-gray-200 rounded-lg p-5 
                    {{ $result['is_correct'] ? 'bg-green-50 border-green-200' : 
                        ($result['user_answer'] ? 'bg-red-50 border-red-200' : 'bg-gray-50') }}">
                    
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-gray-700 shadow-sm">
                                    Câu {{ $index + 1 }}
                                </span>
                                @if($result['is_correct'])
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                                        ✅ Đúng
                                    </span>
                                @elseif($result['user_answer'])
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium flex items-center gap-1">
                                        ❌ Sai
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium flex items-center gap-1">
                                        ⏸️ Chưa trả lời
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-800 font-medium text-lg">{{ $result['question']->question }}</p>
                        </div>
                    </div>

                    {{-- Danh sách đáp án --}}
                    <div class="space-y-3 ml-4">
                        @foreach($result['question']->questionChoices as $choice)
                            <div class="flex flex-col p-3 rounded-lg transition
                                {{ $choice->id === $result['correct_choice']?->id ? 'bg-green-100 border-2 border-green-400' : '' }}
                                {{ $result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'] ? 'bg-red-100 border-2 border-red-400' : '' }}
                                {{ $choice->id !== $result['correct_choice']?->id && (!$result['user_answer'] || $choice->id !== $result['user_answer']->id) ? 'bg-white border border-gray-200' : '' }}">
                                
                                <div class="flex items-start gap-3">
                                    {{-- Ký hiệu A/B/C --}}
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full font-semibold text-sm
                                        {{ $choice->id === $result['correct_choice']?->id ? 'bg-green-500 text-white' : '' }}
                                        {{ $result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'] ? 'bg-red-500 text-white' : '' }}
                                        {{ $choice->id !== $result['correct_choice']?->id && (!$result['user_answer'] || $choice->id !== $result['user_answer']->id) ? 'bg-gray-300 text-gray-700' : '' }}">
                                        {{ chr(65 + $loop->index) }}
                                    </span>

                                    {{-- Nội dung đáp án --}}
                                    <div class="flex-1">
                                        <p class="text-gray-800">{{ $choice->name }}</p>
                                    </div>

                                    {{-- Đánh dấu --}}
                                    <div class="flex-shrink-0 flex items-center gap-2">
                                        @if($choice->id === $result['correct_choice']?->id)
                                            <span class="text-green-600 text-sm font-medium">Đáp án đúng</span>
                                        @endif
                                        @if($result['user_answer'] && $choice->id === $result['user_answer']->id)
                                            <span class="text-blue-600 text-sm font-medium">Bạn chọn</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Giải thích --}}
                                @if(!empty($choice->explanation))
                                    <div class="mt-2 ml-10 text-sm text-gray-600 italic">
                                        📝 Giải thích: {{ $choice->explanation }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
@endsection