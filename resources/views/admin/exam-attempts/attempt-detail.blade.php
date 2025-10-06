@extends('layouts.admin')

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
        <a href="{{ route('admin.exam-attempts.user-attempts', ['exam' => $attempt->exam, 'user' => $attempt->user]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại lịch sử
        </a>
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Điểm số</p>
                <p class="text-4xl font-bold text-blue-600">{{ $attempt->score }}</p>
                <p class="text-sm text-gray-500 mt-1">/{{ $attempt->exam->total_questions }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ $attempt->score_percentage }}%</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-green-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Đúng</p>
                <p class="text-3xl font-bold text-green-600">{{ $attempt->correct_count }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <i data-lucide="x-circle" class="w-8 h-8 mx-auto text-red-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Sai</p>
                <p class="text-3xl font-bold text-red-600">{{ $attempt->wrong_count }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <i data-lucide="clock" class="w-8 h-8 mx-auto text-purple-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Thời gian</p>
                <p class="text-2xl font-bold text-purple-600">{{ $attempt->duration_in_minutes ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">phút</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <i data-lucide="calendar" class="w-8 h-8 mx-auto text-orange-600 mb-2"></i>
                <p class="text-sm text-gray-600 mb-1">Ngày thi</p>
                <p class="text-lg font-bold text-orange-600">{{ $attempt->finished_at->format('d/m/Y') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $attempt->finished_at->format('H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
            <p class="text-sm text-blue-700 mb-1">Đề thi</p>
            <p class="text-lg font-semibold text-blue-900">{{ $attempt->exam->title }}</p>
            <p class="text-xs text-blue-600 mt-1">{{ $attempt->exam->subject->name }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
            <p class="text-sm text-purple-700 mb-1">Thí sinh</p>
            <p class="text-lg font-semibold text-purple-900">{{ $attempt->user->name }}</p>
            <p class="text-xs text-purple-600 mt-1">{{ $attempt->user->email }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
            <p class="text-sm text-green-700 mb-1">Loại thi</p>
            <p class="text-lg font-semibold text-green-900">{{ $attempt->type_name }}</p>
            <p class="text-xs text-green-600 mt-1">{{ $attempt->exam->total_questions }} câu hỏi</p>
        </div>
    </div>

    {{-- Questions and Answers --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i data-lucide="list" class="w-5 h-5"></i>
            Chi tiết câu trả lời
        </h2>

        <div class="space-y-6">
            @foreach($detailedResults as $index => $result)
            <div class="border border-gray-200 rounded-lg p-5 {{ $result['is_correct'] ? 'bg-green-50 border-green-200' : ($result['user_answer'] ? 'bg-red-50 border-red-200' : 'bg-gray-50') }}">
                {{-- Question Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-gray-700 shadow-sm">
                                Câu {{ $index + 1 }}
                            </span>
                            @if($result['is_correct'])
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    Đúng
                                </span>
                            @elseif($result['user_answer'])
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="x-circle" class="w-3 h-3"></i>
                                    Sai
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium flex items-center gap-1">
                                    <i data-lucide="minus-circle" class="w-3 h-3"></i>
                                    Chưa trả lời
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-800 font-medium text-lg">{{ $result['question']->question_text }}</p>
                    </div>
                </div>

                {{-- Choices --}}
                <div class="space-y-3 ml-4">
                    @foreach($result['question']->choices as $choice)
                    <div class="flex items-start gap-3 p-3 rounded-lg transition
                        {{ $choice->id === $result['correct_choice']?->id ? 'bg-green-100 border-2 border-green-400' : '' }}
                        {{ $result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'] ? 'bg-red-100 border-2 border-red-400' : '' }}
                        {{ $choice->id !== $result['correct_choice']?->id && (!$result['user_answer'] || $choice->id !== $result['user_answer']->id) ? 'bg-white border border-gray-200' : '' }}">
                        
                        {{-- Choice Letter --}}
                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full font-semibold text-sm
                            {{ $choice->id === $result['correct_choice']?->id ? 'bg-green-500 text-white' : '' }}
                            {{ $result['user_answer'] && $choice->id === $result['user_answer']->id && !$result['is_correct'] ? 'bg-red-500 text-white' : '' }}
                            {{ $choice->id !== $result['correct_choice']?->id && (!$result['user_answer'] || $choice->id !== $result['user_answer']->id) ? 'bg-gray-300 text-gray-700' : '' }}">
                            {{ chr(65 + $loop->index) }}
                        </span>

                        {{-- Choice Text --}}
                        <div class="flex-1">
                            <p class="text-gray-800">{{ $choice->choice_text }}</p>
                        </div>

                        {{-- Icons --}}
                        <div class="flex-shrink-0 flex items-center gap-2">
                            @if($choice->id === $result['correct_choice']?->id)
                                <span class="text-green-600 flex items-center gap-1 text-sm font-medium">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Đáp án đúng
                                </span>
                            @endif
                            @if($result['user_answer'] && $choice->id === $result['user_answer']->id)
                                <span class="text-blue-600 flex items-center gap-1 text-sm font-medium">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    Bạn chọn
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-4">
        <a href="{{ route('admin.exam-attempts.user-attempts', ['exam' => $attempt->exam, 'user' => $attempt->user]) }}"
           class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
            Quay lại lịch sử
        </a>
        
        <form action="{{ route('admin.exam-attempts.destroy', $attempt) }}" 
              method="POST" 
              class="flex-1"
              onsubmit="return confirm('Xóa lượt thi này sẽ hoàn lại 1 lượt cho user. Bạn có chắc chắn?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                Xóa lượt thi này
            </button>
        </form>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
