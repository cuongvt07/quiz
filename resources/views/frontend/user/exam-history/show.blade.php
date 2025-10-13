@extends('layouts.frontend')

@section('title', 'Chi tiết lượt thi')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                    <i data-lucide="file-text" class="text-blue-600 w-7 h-7"></i>
                    <span>Chi tiết lần thi</span>
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ $attempt->exam->title }}
                </p>
            </div>
            <a href="{{ route('exam-history.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại lịch sử
            </a>
        </div>

        {{-- Tổng quan --}}
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Tổng số câu</p>
                <p class="text-4xl font-bold text-gray-700">{{ $stats['total_questions'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Điểm số</p>
                <p class="text-4xl font-bold text-blue-600">{{ $attempt->score }}</p>
                <p class="text-sm text-gray-500 mt-1">/{{ $stats['total_questions'] }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ number_format(($attempt->score / $stats['total_questions']) * 100, 1) }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-green-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Đúng</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['correct_count'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <i data-lucide="x-circle" class="w-8 h-8 mx-auto text-red-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Sai</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['wrong_count'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <i data-lucide="minus-circle" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Chưa trả lời</p>
                <p class="text-3xl font-bold text-gray-400">{{ $stats['unanswered_count'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-center">
                <i data-lucide="clock" class="w-8 h-8 mx-auto text-purple-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Thời gian</p>
                <p class="text-2xl font-bold text-purple-600">{{ $attempt->duration_minutes ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">phút</p>
            </div>
        </div>

        {{-- Danh sách câu hỏi --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5"></i>
                    Chi tiết câu trả lời
                </h2>
                <div class="flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-gray-600">Đúng</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-gray-600">Sai</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                        <span class="text-gray-600">Chưa trả lời</span>
                    </span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($detailedResults as $index => $result)
                    <div class="py-4">
                        {{-- Question Row --}}
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium
                                {{ $result['is_correct'] ? 'bg-green-500 text-white' : 
                                   ($result['user_answer'] ? 'bg-red-500 text-white' : 'bg-gray-300 text-gray-700') }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 mb-2">{{ $result['question']->question }}</p>
                                
                                {{-- Answers Summary --}}
                                <div class="flex flex-wrap gap-3 text-sm">
                                    @foreach($result['question']->choices as $choice)
                                        <div class="flex items-center gap-1 px-2 py-1 rounded
                                            {{ $choice->id === $result['correct_choice']?->id ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'] ? 'bg-red-100 text-red-700' : '' }}">
                                            <span class="font-medium">{{ chr(65 + $loop->index) }}.</span>
                                            <span>{{ $choice->name }}</span>
                                            @if($choice->id === $result['correct_choice']?->id)
                                                <span class="text-green-600 ml-1">✓</span>
                                            @endif
                                            @if($result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'])
                                                <span class="text-red-600 ml-1">✗</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Explanation if exists --}}
                                @if(!empty($result['question']->choices->first()->explanation))
                                    <p class="mt-2 text-sm text-gray-600 italic">
                                        <span class="font-medium">Giải thích:</span>
                                        {{ $result['question']->choices->first()->explanation }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection