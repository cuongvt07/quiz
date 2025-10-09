@extends('layouts.app')

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