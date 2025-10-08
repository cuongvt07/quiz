@extends('layouts.frontend')

@section('title', 'Kết quả bài thi - ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Kết quả bài thi: {{ $category->name }}</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Điểm số của bạn: <span class="font-medium">{{ session('score', 0) }}/{{ $category->questions->count() }}</span>
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                @foreach($category->questions as $index => $question)
                    <div class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Câu {{ $index + 1 }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-4">
                                <p>{{ $question->content }}</p>
                                <div class="space-y-2">
                                    @foreach($question->choices as $choice)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($choice->is_correct)
                                                    <span class="h-5 w-5 text-green-500">
                                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700">{{ $choice->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
            <a href="{{ route('quizzes') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Quay lại danh sách
            </a>
        </div>
    </div>
</div>
@endsection