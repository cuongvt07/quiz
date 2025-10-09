@extends('layouts.frontend')

@section('title', 'Danh sách bài thi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Danh sách bài thi</h1>
            <p class="mt-2 text-sm text-gray-700">Chọn chủ đề bài thi bạn muốn thực hành.</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($categories as $category)
            <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $category->name }}
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>{{ $category->description }}</p>
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $category->questions_count }} câu hỏi
                        </span>
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('quiz.show', $category->slug) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Bắt đầu làm bài
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection