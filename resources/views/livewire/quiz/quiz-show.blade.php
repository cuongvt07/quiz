<div class="container mx-auto py-8 px-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-500 p-6">
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $category->name }}</h1>
            <div class="flex justify-between items-center mt-4">
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-white">
                    <span class="text-sm">Câu hỏi:</span>
                    <span class="font-bold">{{ $currentIndex }} / {{ $questions->count() }}</span>
                </div>
                <div 
                    x-data="{
                        timeLeft: {{ $timeLeft }},
                        formatTime() {
                            let minutes = Math.floor(this.timeLeft / 60);
                            let seconds = this.timeLeft % 60;
                            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        }
                    }"
                    x-init="setInterval(() => {
                        if(timeLeft > 0) {
                            timeLeft--;
                            if(timeLeft === 0) {
                                $wire.emit('timeUp');
                            }
                        }
                    }, 1000)"
                    class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-white flex items-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="formatTime()">30:00</span>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex flex-wrap mb-6">
                @foreach($questions as $index => $q)
                    <button 
                        wire:click="$set('currentQuestion', {{ $index }})" 
                        class="w-10 h-10 flex items-center justify-center m-1 rounded-full {{ $index === $currentQuestion ? 'bg-indigo-600 text-white' : (isset($answers[$q->id]) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700') }} hover:bg-indigo-500 hover:text-white transition duration-200"
                    >
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
            
            <div x-data="{ showQuestion: true }" 
                 x-on:question-change.window="showQuestion = false; setTimeout(() => { showQuestion = true }, 300)">
                <div x-show="showQuestion" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-4"
                     class="mb-8">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">{{ $currentIndex }}. {{ $question->question }}</h2>
                    
                    <div class="space-y-4">
                        @if(isset($question->questionChoices))
                            @foreach($question->questionChoices as $choice)
                                <label class="flex items-start p-4 {{ $answers[$question->id] == $choice->id ? 'bg-indigo-50 border-indigo-500' : 'bg-gray-50 border-gray-300' }} border rounded-lg cursor-pointer transition hover:bg-indigo-50">
                                    <input 
                                        type="radio" 
                                        name="question_{{ $question->id }}" 
                                        value="{{ $choice->id }}" 
                                        wire:model="selectedAnswer"
                                        wire:click="selectAnswer({{ $question->id }}, {{ $choice->id }})"
                                        class="mt-1 h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                    >
                                    <span class="ml-3 text-gray-700">{{ $choice->name }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <button 
                wire:click="previousQuestion" 
                class="{{ $currentQuestion === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200' }} px-4 py-2 bg-gray-100 text-gray-700 rounded-lg transition flex items-center"
                {{ $currentQuestion === 0 ? 'disabled' : '' }}
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Câu trước
            </button>
            
            @if($currentQuestion < $questions->count() - 1)
                <button 
                    wire:click="nextQuestion" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center"
                >
                    Câu tiếp
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button 
                    wire:click="finishQuiz" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center"
                >
                    Nộp bài
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('questionChanged', function() {
                window.dispatchEvent(new CustomEvent('question-change'));
            });
        });
    </script>
</div>
