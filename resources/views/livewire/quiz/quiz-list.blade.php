<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-10 text-indigo-700">Chọn bài trắc nghiệm</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                <div class="h-48 bg-gradient-to-r from-indigo-500 to-blue-500 flex items-center justify-center p-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $category->name }}</h2>
                    <p class="text-gray-600 mb-4">
                        {{ $category->question->count() }} câu hỏi
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Thời gian: 30 phút</span>
                        <a href="{{ route('quiz.show', $category->slug) }}" 
                           class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Bắt đầu
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
