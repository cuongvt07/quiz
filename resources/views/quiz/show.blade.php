@extends('layouts.frontend')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('quiz.result', $category->slug) }}" method="POST">
            @csrf
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $category->name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $category->description }}</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                @foreach($category->questions as $index => $question)
                    <div class="mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-900">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-lg font-medium text-gray-900">{{ $question->content }}</p>
                                <div class="mt-4 space-y-4">
                                    @foreach($question->choices as $choice)
                                        <div class="flex items-center">
                                            <input type="radio" 
                                                   id="question-{{ $question->id }}-choice-{{ $choice->id }}"
                                                   name="answers[{{ $question->id }}]"
                                                   value="{{ $choice->id }}"
                                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <label for="question-{{ $question->id }}-choice-{{ $choice->id }}"
                                                   class="ml-3 block text-gray-700">
                                                {{ $choice->content }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="bg-gray-50 px-4 py-4 sm:px-6">
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Nộp bài
                </button>
            </div>
        </form>
    </div>
</div>
@endsection