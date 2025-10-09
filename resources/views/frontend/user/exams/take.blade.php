@extends('layouts.frontend')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <form id="exam-form" action="{{ route('user.exams.submit', $attempt) }}" method="POST">
            @csrf
            <div class="flex gap-6">
                {{-- Nội dung đề thi (9 cột) --}}
                <div class="w-9/12 space-y-6">
                    {{-- Header --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $attempt->exam->title }}</h1>
                        <p class="text-gray-600 mt-2">{{ $attempt->exam->subject->name }}</p>
                    </div>

                    {{-- Danh sách câu hỏi --}}
                    <div class="space-y-6">
                        @foreach($attempt->exam->questions as $index => $question)
                            <div class="bg-white rounded-xl shadow-sm p-6" id="question-{{ $index + 1 }}">
                                {{-- Tiêu đề câu hỏi --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-semibold text-gray-700">
                                        Câu {{ $index + 1 }}
                                    </span>
                                </div>
                                <p class="text-gray-800 text-lg font-medium mb-6">{{ $question->question }}</p>

                                {{-- Các lựa chọn --}}
                                <div class="space-y-3 ml-4">
                                    @if($question->questionChoices->count() == 1)
                                        {{-- Câu điền --}}
                                        <div class="rounded-lg border border-gray-200">
                                            <input type="text" 
                                                name="answers[{{ $question->id }}]" 
                                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Nhập câu trả lời của bạn"
                                                data-question="{{ $index + 1 }}"
                                                data-question-id="{{ $question->id }}"
                                                value="{{ $attempt->answers->where('question_id', $question->id)->first()?->answer ?? '' }}"
                                                oninput="saveAnswer({{ $question->id }}, this.value, true)">
                                        </div>
                                    @else
                                        {{-- Câu chọn --}}
                                        @foreach($question->questionChoices as $choice)
                                            <label class="flex items-center p-4 rounded-lg border border-gray-200 hover:border-blue-400 cursor-pointer transition-all group">
                                                <input type="radio" 
                                                    name="answers[{{ $question->id }}]" 
                                                    value="{{ $choice->id }}"
                                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                    data-question="{{ $index + 1 }}"
                                                    data-question-id="{{ $question->id }}"
                                                    {{ $attempt->answers->where('question_id', $question->id)->where('choice_id', $choice->id)->count() ? 'checked' : '' }}
                                                    onchange="saveAnswer({{ $question->id }}, {{ $choice->id }}, false)">
                                                <div class="ml-4 flex items-center justify-between flex-1">
                                                    <span class="font-medium text-gray-700 group-hover:text-blue-600">
                                                        {{ chr(65 + $loop->index) }}. {{ $choice->name }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sidebar thông tin (3 cột) --}}
                <div class="w-3/12">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                        {{-- Thông tin cơ bản --}}
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Thông tin bài thi</h3>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Thời gian:</span> 
                                    {{ $attempt->exam->duration_minutes }} phút
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Số câu hỏi:</span> 
                                    {{ $attempt->exam->total_questions }} câu
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Tổng điểm:</span> 
                                    {{ $attempt->exam->total_questions }} điểm
                                </p>
                            </div>
                        </div>

                        {{-- Đồng hồ đếm ngược --}}
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-1">Thời gian còn lại</p>
                                    <div class="text-3xl font-bold text-blue-600" 
                                        x-data="timer('{{ $endAt }}')" 
                                        x-init="startTimer">
                                        <span x-text="String(minutes).padStart(2, '0')">00</span>:<span x-text="String(seconds).padStart(2, '0')">00</span>
                                    </div>
                            </div>
                        </div>

                        {{-- Danh sách câu hỏi --}}
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Danh sách câu hỏi</h4>
                            <div class="grid grid-cols-5 gap-2">
                                @foreach($attempt->exam->questions as $index => $question)
                                    <a href="#question-{{ $index + 1 }}"
                                        class="question-number w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition
                                               {{ $attempt->answers->where('question_id', $question->id)->count() ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        data-question="{{ $index + 1 }}">
                                        {{ $index + 1 }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Nút nộp bài --}}
                        <button type="button" onclick="confirmSubmit()" 
                            class="w-full py-3 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                            Nộp bài
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ================== INIT ON PAGE LOAD ==================
document.addEventListener('DOMContentLoaded', function() {
    updateAllQuestionColors();
});

function updateAllQuestionColors() {
    const allInputs = document.querySelectorAll('input[data-question-id]');
    const processedQuestions = new Set();
    
    allInputs.forEach(input => {
        const questionId = input.dataset.questionId;
        const questionNumber = input.dataset.question;
        
        if (processedQuestions.has(questionNumber)) {
            return;
        }
        processedQuestions.add(questionNumber);
        
        const questionButton = document.querySelector(`.question-number[data-question="${questionNumber}"]`);
        if (!questionButton) return;
        
        let isAnswered = false;
        
        if (input.type === 'text') {
            isAnswered = input.value.trim() !== '';
        } else if (input.type === 'radio') {
            const radioInputs = document.querySelectorAll(`input[type="radio"][data-question-id="${questionId}"]`);
            isAnswered = Array.from(radioInputs).some(radio => radio.checked);
        }
        
        if (isAnswered) {
            questionButton.classList.remove('bg-gray-100', 'text-gray-600');
            questionButton.classList.add('bg-blue-500', 'text-white');
        } else {
            questionButton.classList.remove('bg-blue-500', 'text-white');
            questionButton.classList.add('bg-gray-100', 'text-gray-600');
        }
    });
}

// ================== TIMER ==================
function timer(endAtIso) {
    return {
        minutes: 0,
        seconds: 0,
        isRunning: false,
        endTime: new Date(endAtIso),

        startTimer() {
            this.isRunning = true;

            const updateTimer = () => {
                const now = new Date();
                const timeDiff = this.endTime - now;

                if (timeDiff <= 0) {
                    this.minutes = 0;
                    this.seconds = 0;
                    this.isRunning = false;
                    confirmSubmit(true);
                    return;
                }

                this.minutes = Math.floor(timeDiff / 60000);
                this.seconds = Math.floor((timeDiff % 60000) / 1000);
            };

            updateTimer();
            const interval = setInterval(() => {
                if (!this.isRunning) clearInterval(interval);
                updateTimer();
            }, 1000);
        }
    };
}

// ================== UPDATE QUESTION COLOR ==================
function updateQuestionColor(questionId, questionNumber) {
    const questionButton = document.querySelector(`.question-number[data-question="${questionNumber}"]`);
    if (!questionButton) return;
    
    const input = document.querySelector(`input[data-question-id="${questionId}"]`);
    if (!input) return;
    
    let isAnswered = false;
    
    if (input.type === 'text') {
        isAnswered = input.value.trim() !== '';
    } else if (input.type === 'radio') {
        const radioInputs = document.querySelectorAll(`input[type="radio"][data-question-id="${questionId}"]`);
        isAnswered = Array.from(radioInputs).some(radio => radio.checked);
    }
    
    if (isAnswered) {
        questionButton.classList.remove('bg-gray-100', 'text-gray-600');
        questionButton.classList.add('bg-blue-500', 'text-white');
    } else {
        questionButton.classList.remove('bg-blue-500', 'text-white');
        questionButton.classList.add('bg-gray-100', 'text-gray-600');
    }
}

// ================== SAVE ANSWER ==================
function saveAnswer(questionId, value, isTextAnswer = false) {
    // Cập nhật màu ngay lập tức (optimistic update)
    const input = document.querySelector(`input[data-question-id="${questionId}"]`);
    if (input) {
        const questionNumber = input.dataset.question;
        updateQuestionColor(questionId, questionNumber);
    }
    
    let data = {
        question_id: questionId
    };

    if (isTextAnswer) {
        data.answer_text = value;
    } else {
        data.choice_id = value;
    }

    fetch('{{ route("user.exams.save-answer", $attempt) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new TypeError("Server didn't return JSON response");
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            console.error('Failed to save answer:', data.message);
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// ================== COUNT ANSWERED QUESTIONS ==================
function countAnsweredQuestions() {
    let count = 0;
    
    const answeredRadios = new Set();
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        answeredRadios.add(radio.dataset.questionId);
    });
    count += answeredRadios.size;
    
    document.querySelectorAll('input[type="text"][data-question-id]').forEach(input => {
        if (input.value.trim() !== '') {
            count++;
        }
    });
    
    return count;
}

// ================== CONFIRM SUBMIT ==================
function confirmSubmit(isTimeout = false) {
    const totalQuestions = @json($attempt->exam->total_questions);
    const answeredQuestions = countAnsweredQuestions();

    if (isTimeout) {
        Swal.fire({
            title: 'Hết thời gian làm bài',
            text: 'Hệ thống sẽ tự động nộp bài của bạn.',
            icon: 'info',
            showConfirmButton: false,
            timer: 2500
        }).then(() => {
            submitExam();
        });
        return;
    }

    Swal.fire({
        title: 'Xác nhận nộp bài',
        html: `Bạn đã trả lời <b>${answeredQuestions}</b>/<b>${totalQuestions}</b> câu hỏi.<br>Bạn chắc chắn muốn nộp bài?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Nộp bài',
        cancelButtonText: 'Kiểm tra lại'
    }).then((result) => {
        if (result.isConfirmed) {
            submitExam();
        }
    });
}

// ================== SUBMIT FORM ==================
function submitExam() {
    const form = document.getElementById('exam-form');
    if (form) form.submit();
}
</script>
@endpush