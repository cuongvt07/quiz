@extends('layouts.frontend')

@section('title', 'Lịch sử thi')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                    <i data-lucide="history" class="text-blue-600 w-7 h-7"></i>
                    <span>Lịch sử thi</span>
                </h1>
                @if(request()->has('exam_id'))
                    <p class="text-gray-600 mt-2">{{ $attempts->first()->exam->title ?? '' }}</p>
                @endif
            </div>
            <a href="/user/profile#attempts" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại hồ sơ
            </a>
        </div>

        @if($attempts->isEmpty())
            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có lượt thi nào</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Bạn chưa hoàn thành bài thi nào.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('exams.list') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Làm bài thi ngay
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white shadow-sm rounded-lg">
                <ul class="divide-y divide-gray-200">
                    @foreach($attempts as $attempt)
                        <li class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-4">
                                        {{-- Loại đề thi --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $attempt->exam->subject->type === 'nang_luc' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $attempt->exam->subject->type === 'nang_luc' ? 'Năng lực' : 'Tư duy' }}
                                        </span>

                                        {{-- Tên môn --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $attempt->exam->subject->name }}
                                        </span>
                                    </div>

                                    <h2 class="mt-2 text-lg font-medium text-gray-900">
                                        {{ $attempt->exam->title }}
                                    </h2>

                                    <div class="mt-2 flex items-center text-sm text-gray-500 gap-4">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $attempt->created_at->format('d/m/Y H:i') }}
                                        </span>

                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $attempt->duration_minutes ?? 0 }} phút
                                        </span>

                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $attempt->score ?? 0 }}/{{ $attempt->exam->total_questions }} câu đúng
                                        </span>

                                        <span class="font-medium text-blue-600">
                                            {{ number_format($attempt->score, 1) }} điểm
                                        </span>
                                    </div>
                                </div>

                                <div class="ml-4">
                                    <a href="{{ route('exam-history.show', $attempt) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $attempts->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection