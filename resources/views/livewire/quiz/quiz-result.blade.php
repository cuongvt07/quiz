<div class="container mx-auto py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-blue-500 p-8 text-center">
                <h1 class="text-3xl font-bold text-white mb-2">Kết quả bài thi</h1>
                <h2 class="text-xl text-white opacity-90">{{ $category->name }}</h2>
            </div>
            
            <div class="p-8">
                <div class="flex flex-col items-center justify-center mb-10">
                    <div class="relative">
                        <svg class="w-40 h-40" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#e6e6e6" stroke-width="12" />
                            <circle 
                                cx="60" 
                                cy="60" 
                                r="54" 
                                fill="none" 
                                stroke="{{ $score >= 80 ? '#22c55e' : ($score >= 60 ? '#eab308' : '#ef4444') }}" 
                                stroke-width="12" 
                                stroke-dasharray="339.292" 
                                stroke-dashoffset="{{ 339.292 * (100 - $score) / 100 }}" 
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-4xl font-bold {{ $score >= 80 ? 'text-green-500' : ($score >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ number_format($score, 1) }}%
                            </span>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-600 text-center">
                        Bạn đã trả lời đúng <span class="font-bold text-indigo-600">{{ $correct }}</span> trên tổng số <span class="font-bold text-indigo-600">{{ $total }}</span> câu hỏi
                    </p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Nhận xét</h3>
                    
                    @if($score >= 80)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700">Xuất sắc! Bạn đã nắm vững các kiến thức trong bài thi này.</p>
                        </div>
                    @elseif($score >= 60)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700">Tốt! Bạn đã có kiến thức cơ bản nhưng cần xem lại một số phần.</p>
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700">Bạn cần ôn tập lại kiến thức trong bài thi này.</p>
                        </div>
                    @endif
                </div>
                
                <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4">
                    <a href="{{ route('quiz.show', $category->slug) }}" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition text-center">
                        Làm lại
                    </a>
                    <a href="{{ route('quizzes') }}" class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition text-center">
                        Quay lại danh sách bài thi
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-10 text-center">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Chia sẻ kết quả</h3>
            <div class="flex justify-center space-x-4">
                <button class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                    </svg>
                </button>
                <button class="p-3 bg-blue-800 text-white rounded-full hover:bg-blue-900 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                    </svg>
                </button>
                <button class="p-3 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
