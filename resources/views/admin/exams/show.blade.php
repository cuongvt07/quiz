@extends('layouts.admin')

@section('content')
<form id="questionsForm" method="POST" action="{{ route('admin.exams.questions.batch-update', $exam) }}">    
    @csrf
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-blue-600 flex items-center">
                <i data-feather="arrow-left"></i> Quay lại
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <i data-feather="file-text"></i> Chi tiết đề thi {{ $exam->title }}
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.exams.questions.template') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 flex items-center">
                <i data-feather="download" class="mr-2"></i> Tải file mẫu
            </a>
            <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center" id="btnImportQuestions">
                <i data-feather="upload" class="mr-2"></i> Import câu hỏi
            </button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                <i data-feather="save" class="mr-2"></i> Lưu tất cả
            </button>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded shadow p-4">
            <div class="mb-2 text-lg font-semibold flex justify-between items-center">
                <span>Thông tin đề thi</span>
            </div>
            <div class="mb-2"><b>Tên đề:</b> {{ $exam->title }}</div>
            <div class="mb-2"><b>Môn học:</b> {{ $exam->subject->name ?? '-' }}</div>
            <div class="mb-2">
                <b>Thời gian:</b>
                <input type="number" name="duration_minutes" value="{{ $exam->duration_minutes }}" 
                    class="w-20 px-2 py-1 border rounded" min="1"> phút
            </div>
            <div class="mb-2 flex items-center gap-2">
                <b>Số câu hỏi tối đa:</b> 
                <span id="totalQuestionsDisplay" class="font-semibold">{{ $exam->total_questions }}</span>
                <input type="hidden" name="total_questions" id="totalQuestionsInput" value="{{ $exam->total_questions }}">
                <button type="button" onclick="decrementQuestionCount()" 
                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 flex items-center disabled:opacity-50" 
                    id="decrementBtn">
                    <i data-feather="minus" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="incrementQuestionCount()" 
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                    <i data-feather="plus" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-3 p-3 bg-green-100 text-green-700 rounded flex items-center gap-2">
            <i data-feather="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-3 p-3 bg-red-100 text-red-700 rounded flex items-center gap-2">
            <i data-feather="alert-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        <h2 class="text-lg font-bold mb-2 flex items-center gap-2">
            <i data-feather="list"></i> Danh sách câu hỏi
            <span class="text-sm font-normal text-gray-500">(Nhập trực tiếp vào ô, click "Quản lý đáp án" để thêm đáp án)</span>
        </h2>
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200" id="questionsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung câu hỏi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại câu hỏi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số đáp án</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Đáp án đúng</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="questionsBody">
                    @for($i = 0; $i < $exam->total_questions; $i++)
                        @php
                            $question = $exam->questions[$i] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ !$question ? 'bg-gray-50' : '' }}" data-question-index="{{ $i }}">
                            <td class="px-4 py-3 text-gray-500 text-center font-medium">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="questions[{{ $i }}][id]" value="{{ $question?->id ?? '' }}">
                                <input type="text" 
                                    name="questions[{{ $i }}][question]" 
                                    value="{{ $question?->question ?? '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                    placeholder="Nhập nội dung câu hỏi {{ $i + 1 }}...">
                            </td>
                            <td class="px-4 py-3">
                                <select name="questions[{{ $i }}][loai]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <option value="">-- Chọn loại --</option>
                                    @foreach($questionTypes as $value => $label)
                                        <option value="{{ $value }}" 
                                            {{ ($question?->loai ?? null) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Container ẩn để JS thêm/xóa hidden inputs cho answers (nested array) -->
                            <td class="px-4 py-3 hidden" data-answers-container="{{ $i }}">
                                @if($question && $question->questionChoices->count() > 0)
                                    @foreach($question->questionChoices as $j => $choice)
                                        <input type="hidden" 
                                            name="questions[{{ $i }}][answers][{{ $j }}][name]" 
                                            value="{{ e($choice->name) }}">
                                        <input type="hidden" 
                                            name="questions[{{ $i }}][answers][{{ $j }}][explanation]" 
                                            value="{{ e($choice->explanation) }}">
                                        <input type="hidden" 
                                            name="questions[{{ $i }}][answers][{{ $j }}][is_correct]" 
                                            value="{{ $choice->is_correct ? 1 : 0 }}">
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-sm font-medium {{ $question && $question->questionChoices->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}" data-answer-count="{{ $question?->questionChoices->count() ?? 0 }}">
                                    {{ $question?->questionChoices->count() ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $correctAnswer = $question?->questionChoices->where('is_correct', 1)->first();
                                @endphp
                                <span class="{{ $correctAnswer ? 'font-medium text-green-600' : 'text-gray-400' }}" data-correct-answer="{{ $correctAnswer?->name ?? '-' }}">
                                    {{ $correctAnswer?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="questions[{{ $i }}][is_active]" value="0">
                                    <input type="checkbox" 
                                        name="questions[{{ $i }}][is_active]" 
                                        value="1"
                                        {{ ($question?->is_active ?? 1) == 1 ? 'checked' : '' }}
                                        class="toggle-checkbox sr-only">
                                    <div class="toggle-bg w-11 h-6 bg-gray-200 rounded-full transition-colors duration-300"></div>
                                    <div class="toggle-dot absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300"></div>
                                </label>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                    onclick="openAnswerModal({{ $i }}, {{ $question?->id ?? 'null' }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i data-feather="list" class="w-4 h-4"></i>
                                    Quản lý đáp án
                                </button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</form>

{{-- Modal for importing questions --}}
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-lg mx-4">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Import câu hỏi</h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-500 hover:text-gray-700">
                <i data-feather="x"></i>
            </button>
        </div>
        
        <form id="importForm" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">File Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls" class="w-full px-3 py-2 border rounded" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Chế độ import</label>
                <select name="import_type" class="w-full px-3 py-2 border rounded" required>
                    <option value="append">Thêm mới (Giữ dữ liệu cũ)</option>
                    <option value="replace">Thay thế (Xóa dữ liệu cũ)</option>
                </select>
            </div>
        </form>

        <div class="p-4 border-t flex justify-end gap-2">
            <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Hủy
            </button>
            <button type="button" onclick="submitImport()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Import
            </button>
        </div>
    </div>
</div>

{{-- Modal for managing answers --}}
<div id="answerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold">
                Quản lý đáp án - <span id="modalQuestionTitle">Câu hỏi #</span>
            </h3>
            <button type="button" onclick="closeAnswerModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <label class="block mb-2 font-medium text-gray-700">Loại câu hỏi:</label>
                <select id="questionType" onchange="changeQuestionType()" class="w-full px-3 py-2 border rounded-lg">
                    <option value="multiple">Trắc nghiệm (4 đáp án)</option>
                    <option value="text">Điền đáp án (1 đáp án)</option>
                </select>
            </div>

            <div id="answersContainer" class="space-y-3">
                <!-- Answers will be rendered here -->
            </div>

            <div class="mt-4" id="addAnswerBtn">
                <button type="button" onclick="addAnswer()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    Thêm đáp án
                </button>
            </div>
        </div>

        <div class="p-4 border-t flex justify-end gap-2 sticky bottom-0 bg-white">
            <button type="button" onclick="closeAnswerModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                Đóng
            </button>
            <button type="button" onclick="saveAnswers()" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <i data-feather="save" class="w-4 h-4"></i>
                Lưu đáp án
            </button>
        </div>
    </div>
</div>

<script>
let currentQuestionIndex = null;
let currentQuestionId = null;
let answers = [];
let tempAnswers = {}; // { index: [answers array] }
const initialTotalQuestions = {{ $exam->total_questions }}; // Số câu hỏi ban đầu

// Toggle switch functionality
function initToggleSwitches() {
    document.querySelectorAll('.toggle-checkbox').forEach(checkbox => {
        const label = checkbox.closest('label');
        const bg = label.querySelector('.toggle-bg');
        const dot = label.querySelector('.toggle-dot');
        
        function updateToggle() {
            if (checkbox.checked) {
                bg.classList.remove('bg-gray-200');
                bg.classList.add('bg-blue-600');
                dot.style.transform = 'translateX(20px)';
            } else {
                bg.classList.remove('bg-blue-600');
                bg.classList.add('bg-gray-200');
                dot.style.transform = 'translateX(0)';
            }
        }
        
        updateToggle(); // Initial state
        
        checkbox.addEventListener('change', updateToggle);
    });
}

// Decrement total questions (only if > initial)
function decrementQuestionCount() {
    const input = document.getElementById('totalQuestionsInput');
    const display = document.getElementById('totalQuestionsDisplay');
    const decrementBtn = document.getElementById('decrementBtn');
    let current = parseInt(input.value);
    
    if (current <= initialTotalQuestions) {
        showNotification('Không thể giảm dưới số câu hỏi ban đầu', 'error');
        return;
    }
    
    current -= 1;
    input.value = current;
    display.textContent = current;
    updateQuestionRows(current);
    
    // Update button state
    decrementBtn.disabled = current <= initialTotalQuestions;
}

// Increment total questions
function incrementQuestionCount() {
    const input = document.getElementById('totalQuestionsInput');
    const display = document.getElementById('totalQuestionsDisplay');
    const decrementBtn = document.getElementById('decrementBtn');
    let current = parseInt(input.value);
    current += 1;
    input.value = current;
    display.textContent = current;
    updateQuestionRows(current);
    
    // Enable decrement if now > initial
    decrementBtn.disabled = false;
}

// Update question rows when total_questions changes
function updateQuestionRows(newTotal) {
    const currentTotal = document.querySelectorAll('#questionsBody tr').length;
    const tbody = document.getElementById('questionsBody');
    
    if (newTotal > currentTotal) {
        for (let i = currentTotal; i < newTotal; i++) {
            const row = createQuestionRow(i);
            tbody.appendChild(row);
        }
        initToggleSwitches(); // Re-init toggles for new rows
    } else if (newTotal < currentTotal) {
        for (let i = currentTotal - 1; i >= newTotal; i--) {
            tbody.removeChild(tbody.lastChild);
        }
    }
    
    if (window.feather) feather.replace();
}

function createQuestionRow(index) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50 bg-gray-50';
    row.setAttribute('data-question-index', index);
    row.innerHTML = `
        <td class="px-4 py-3 text-gray-500 text-center font-medium">${index + 1}</td>
        <td class="px-4 py-3">
            <input type="hidden" name="questions[${index}][id]" value="">
            <input type="text" 
                name="questions[${index}][question]" 
                value=""
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                placeholder="Nhập nội dung câu hỏi ${index + 1}...">
        </td>
        <td class="px-4 py-3">
            <select name="questions[${index}][category_id]" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <option value="">-- Chọn danh mục --</option>
                ${Array.from(document.querySelector('select[name^="questions"][name$="[category_id]"]')?.options || [])
                    .map(opt => `<option value="${opt.value}">${opt.text}</option>`).join('')}
            </select>
        </td>
        <!-- Container ẩn cho hidden answers -->
        <td class="px-4 py-3 hidden" data-answers-container="${index}"></td>
        <td class="px-4 py-3 text-center">
            <span class="px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600" data-answer-count="0">0</span>
        </td>
        <td class="px-4 py-3 text-center">
            <span class="text-gray-400" data-correct-answer="-">-</span>
        </td>
        <td class="px-4 py-3 text-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="questions[${index}][is_active]" value="0">
                <input type="checkbox" name="questions[${index}][is_active]" value="1" checked class="toggle-checkbox sr-only">
                <div class="toggle-bg w-11 h-6 bg-gray-200 rounded-full transition-colors duration-300"></div>
                <div class="toggle-dot absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300"></div>
            </label>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" 
                onclick="openAnswerModal(${index}, null)"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="list" class="w-4 h-4"></i>
                Quản lý đáp án
            </button>
        </td>
    `;
    return row;
}

// Load answers từ hidden inputs (parse thành array)
function loadAnswersFromHidden(index) {
    const container = document.querySelector(`td[data-answers-container="${index}"]`);
    if (!container) return [];
    
    const inputs = container.querySelectorAll('input[type="hidden"]');
    const answers = [];
    let currentAnswer = null;

    inputs.forEach(input => {
        const name = input.name;
        if (name.includes('[name]')) {
            currentAnswer = { name: input.value, explanation: '', is_correct: false };
            if (currentAnswer.name.trim()) {
                answers.push(currentAnswer);
            }
        } else if (name.includes('[explanation]') && currentAnswer) {
            currentAnswer.explanation = input.value;
        } else if (name.includes('[is_correct]') && currentAnswer) {
            currentAnswer.is_correct = input.value === '1';
        }
    });

    return answers;
}

// Clear old hidden inputs và add new ones từ array
function updateHiddenAnswers(index, answersArray) {
    const container = document.querySelector(`td[data-answers-container="${index}"]`);
    if (!container) return;

    // Clear old
    container.innerHTML = '';

    // Add new hidden inputs từ array
    answersArray.forEach((answer, j) => {
        if (answer.name.trim()) {
            const escapedName = answer.name.replace(/"/g, '&quot;');
            const escapedExplanation = (answer.explanation || '').replace(/"/g, '&quot;');
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="questions[${index}][answers][${j}][name]" value="${escapedName}">
                <input type="hidden" name="questions[${index}][answers][${j}][explanation]" value="${escapedExplanation}">
                <input type="hidden" name="questions[${index}][answers][${j}][is_correct]" value="${answer.is_correct ? 1 : 0}">
            `);
        }
    });
}

async function openAnswerModal(index, questionId) {
    currentQuestionIndex = index;
    currentQuestionId = questionId;
    
    document.getElementById('modalQuestionTitle').textContent = `Câu hỏi #${index + 1}`;
    
    // Load answers từ hidden inputs
    answers = loadAnswersFromHidden(index);
    if (answers.length === 0) {
        document.getElementById('questionType').value = 'multiple';
        answers = Array(4).fill().map(() => ({ name: '', explanation: '', is_correct: false }));
    } else {
        const type = answers.length > 1 ? 'multiple' : 'text';
        document.getElementById('questionType').value = type;
    }
    
    // Lưu tạm vào temp cho edit
    tempAnswers[index] = [...answers];
    
    renderAnswers();
    document.getElementById('answerModal').classList.remove('hidden');
    
    if (window.feather) feather.replace();
}

function closeAnswerModal() {
    document.getElementById('answerModal').classList.add('hidden');
    currentQuestionIndex = null;
    currentQuestionId = null;
    if (tempAnswers[currentQuestionIndex]) {
        delete tempAnswers[currentQuestionIndex];
    }
    answers = [];
}

function changeQuestionType() {
    const type = document.getElementById('questionType').value;
    
    if (type === 'text') {
        tempAnswers[currentQuestionIndex] = [{ name: '', explanation: '', is_correct: true }];
    } else {
        // Ensure exactly 4 answers for multiple choice
        let current = tempAnswers[currentQuestionIndex] || answers;
        while (current.length < 4) {
            current.push({ name: '', explanation: '', is_correct: false });
        }
        while (current.length > 4) {
            current.pop();
        }
        // Reset correct answer to none initially
        current.forEach(a => a.is_correct = false);
        tempAnswers[currentQuestionIndex] = current;
    }
    
    renderAnswers();
}

function renderAnswers() {
    const container = document.getElementById('answersContainer');
    const type = document.getElementById('questionType').value;
    const addBtn = document.getElementById('addAnswerBtn');
    
    // Sử dụng temp nếu có, fallback answers
    const currentAnswers = tempAnswers[currentQuestionIndex] || answers;
    
    container.innerHTML = '';
    
    if (type === 'text') {
        // Render single text answer
        const div = document.createElement('div');
        div.className = 'border rounded-lg p-4 bg-gray-50';
        const answer = currentAnswers[0] || { name: '', explanation: '', is_correct: true };
        div.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 pt-2">
                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                        <i data-feather="check" class="w-3 h-3 text-white"></i>
                    </div>
                </div>
                
                <div class="flex-grow">
                    <input type="text" 
                        value="${answer.name || ''}"
                        onchange="updateAnswerName(0, this.value)"
                        class="w-full px-3 py-2 border rounded-lg mb-2"
                        placeholder="Đáp án đúng">
                    <textarea 
                        onchange="updateAnswerExplanation(0, this.value)"
                        class="w-full px-3 py-2 border rounded-lg text-sm"
                        rows="2"
                        placeholder="Giải thích (không bắt buộc)">${answer.explanation || ''}</textarea>
                </div>
            </div>
        `;
        container.appendChild(div);
    } else {
        // Always render in 2x2 grid for multiple choice, ensuring 4 answers
        const gridDiv = document.createElement('div');
        gridDiv.className = 'grid grid-cols-2 gap-4';
        
        // Letters for A B C D
        const letters = ['A', 'B', 'C', 'D'];
        
        // Top row: A and B (indices 0,1)
        const topRow = document.createElement('div');
        topRow.className = 'space-y-2';
        [0, 1].forEach(idx => {
            const letter = letters[idx];
            const answer = currentAnswers[idx] || { name: '', explanation: '', is_correct: false };
            const div = document.createElement('div');
            div.className = 'border rounded-lg p-3 bg-white';
            div.innerHTML = `
                <div class="flex items-start gap-2">
                    <span class="font-bold text-lg text-gray-600 flex-shrink-0">${letter}</span>
                    <input type="radio" 
                        name="correct_answer" 
                        ${answer.is_correct ? 'checked' : ''}
                        onchange="setCorrectAnswer(${idx})"
                        class="mt-1 text-blue-600 flex-shrink-0">
                    <div class="flex-grow min-w-0">
                        <input type="text" 
                            value="${answer.name || ''}"
                            onchange="updateAnswerName(${idx}, this.value)"
                            class="w-full px-3 py-2 border rounded-lg mb-1"
                            placeholder="Đáp án ${letter}">
                        <textarea 
                            onchange="updateAnswerExplanation(${idx}, this.value)"
                            class="w-full px-3 py-2 border rounded-lg text-sm"
                            rows="2"
                            placeholder="Giải thích (không bắt buộc)">${answer.explanation || ''}</textarea>
                    </div>
                    <button type="button" onclick="removeAnswer(${idx})" 
                        class="flex-shrink-0 p-1 text-red-600 hover:bg-red-50 rounded ml-1"
                        style="display: ${currentAnswers.length <= 2 ? 'none' : 'block'};">
                        <i data-feather="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            topRow.appendChild(div);
        });
        gridDiv.appendChild(topRow);
        
        // Bottom row: C and D (indices 2,3)
        const bottomRow = document.createElement('div');
        bottomRow.className = 'space-y-2';
        [2, 3].forEach(idx => {
            const letter = letters[idx];
            const answer = currentAnswers[idx] || { name: '', explanation: '', is_correct: false };
            const div = document.createElement('div');
            div.className = 'border rounded-lg p-3 bg-white';
            div.innerHTML = `
                <div class="flex items-start gap-2">
                    <span class="font-bold text-lg text-gray-600 flex-shrink-0">${letter}</span>
                    <input type="radio" 
                        name="correct_answer" 
                        ${answer.is_correct ? 'checked' : ''}
                        onchange="setCorrectAnswer(${idx})"
                        class="mt-1 text-blue-600 flex-shrink-0">
                    <div class="flex-grow min-w-0">
                        <input type="text" 
                            value="${answer.name || ''}"
                            onchange="updateAnswerName(${idx}, this.value)"
                            class="w-full px-3 py-2 border rounded-lg mb-1"
                            placeholder="Đáp án ${letter}">
                        <textarea 
                            onchange="updateAnswerExplanation(${idx}, this.value)"
                            class="w-full px-3 py-2 border rounded-lg text-sm"
                            rows="2"
                            placeholder="Giải thích (không bắt buộc)">${answer.explanation || ''}</textarea>
                    </div>
                    <button type="button" onclick="removeAnswer(${idx})" 
                        class="flex-shrink-0 p-1 text-red-600 hover:bg-red-50 rounded ml-1"
                        style="display: ${currentAnswers.length <= 2 ? 'none' : 'block'};">
                        <i data-feather="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            bottomRow.appendChild(div);
        });
        gridDiv.appendChild(bottomRow);
        
        container.appendChild(gridDiv);
    }
    
    // Hide add button for multiple choice when 4 answers, always hide for text
    addBtn.style.display = (type === 'multiple' && currentAnswers.length < 4) ? 'block' : 'none';
    
    if (window.feather) feather.replace();
}

function updateAnswerName(index, value) {
    if (!tempAnswers[currentQuestionIndex]) tempAnswers[currentQuestionIndex] = answers;
    if (!tempAnswers[currentQuestionIndex][index]) {
        tempAnswers[currentQuestionIndex][index] = { name: '', explanation: '', is_correct: false };
    }
    tempAnswers[currentQuestionIndex][index].name = value;
}

function updateAnswerExplanation(index, value) {
    if (!tempAnswers[currentQuestionIndex]) tempAnswers[currentQuestionIndex] = answers;
    if (!tempAnswers[currentQuestionIndex][index]) {
        tempAnswers[currentQuestionIndex][index] = { name: '', explanation: '', is_correct: false };
    }
    tempAnswers[currentQuestionIndex][index].explanation = value;
}

function setCorrectAnswer(index) {
    if (!tempAnswers[currentQuestionIndex]) tempAnswers[currentQuestionIndex] = answers;
    tempAnswers[currentQuestionIndex].forEach((answer, i) => {
        answer.is_correct = i === index;
    });
}

function addAnswer() {
    if (!tempAnswers[currentQuestionIndex]) tempAnswers[currentQuestionIndex] = answers;
    if (tempAnswers[currentQuestionIndex].length < 4) {
        tempAnswers[currentQuestionIndex].push({ name: '', explanation: '', is_correct: false });
        renderAnswers();
    }
}

function removeAnswer(index) {
    if (!tempAnswers[currentQuestionIndex]) tempAnswers[currentQuestionIndex] = answers;
    if (tempAnswers[currentQuestionIndex].length > 2) { // Minimum 2 for multiple choice
        tempAnswers[currentQuestionIndex].splice(index, 1);
        renderAnswers();
    }
}

function saveAnswers() {
    if (!tempAnswers[currentQuestionIndex]) {
        showNotification('Không có thay đổi đáp án', 'info');
        closeAnswerModal();
        return;
    }

    const validAnswers = tempAnswers[currentQuestionIndex].filter(a => a && a.name.trim());
    
    if (validAnswers.length === 0) {
        alert('Vui lòng nhập ít nhất một đáp án');
        return;
    }
    
    const type = document.getElementById('questionType').value;
    if (type === 'multiple' && !validAnswers.some(a => a.is_correct)) {
        alert('Vui lòng chọn đáp án đúng');
        return;
    }
    
    // Update hidden inputs từ array
    updateHiddenAnswers(currentQuestionIndex, validAnswers);
    
    // Update UI row
    const row = document.querySelector(`tr[data-question-index="${currentQuestionIndex}"]`);
    if (row) {
        const countSpan = row.querySelector('[data-answer-count]');
        countSpan.textContent = validAnswers.length;
        countSpan.className = validAnswers.length > 0 
            ? 'px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800' 
            : 'px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600';
        
        const correct = validAnswers.find(a => a.is_correct);
        const correctSpan = row.querySelector('[data-correct-answer]');
        correctSpan.textContent = correct ? correct.name : '-';
        correctSpan.className = correct ? 'font-medium text-green-600' : 'text-gray-400';
    }
    
    // Clear temp
    delete tempAnswers[currentQuestionIndex];
    
    closeAnswerModal();
    showNotification('Đã lưu đáp án thành công', 'success');
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Global functions for modal handling
window.openAnswerModal = openAnswerModal;
window.closeAnswerModal = closeAnswerModal;
window.changeQuestionType = changeQuestionType;
window.updateAnswerName = updateAnswerName;
window.updateAnswerExplanation = updateAnswerExplanation;
window.setCorrectAnswer = setCorrectAnswer;
window.addAnswer = addAnswer;
window.removeAnswer = removeAnswer;
window.saveAnswers = saveAnswers;
window.incrementQuestionCount = incrementQuestionCount;
window.decrementQuestionCount = decrementQuestionCount;

// Handle form submission
document.getElementById('questionsForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-feather="loader" class="mr-2 animate-spin"></i> Đang lưu...';
    
    try {
        const formData = new FormData(this);
        
        // Debug: Log form data
        console.log('Submitting form data:', Object.fromEntries(formData));
        
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Failed to save questions');
        }
        
        showNotification(data.message || 'Đã lưu thành công', 'success');
        
        // Update current count after batch save
        // if (data.created_count) {
        //     const currentCount = parseInt(document.getElementById('currentQuestionCount').textContent);
        //     document.getElementById('currentQuestionCount').textContent = currentCount + data.created_count;
        // }
        
        // Redirect to show page or reload
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification(error.message || 'Không thể lưu thay đổi', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (window.feather) feather.replace();
    }
});

// Import modal functions
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importForm').reset();
}

async function submitImport() {
    const form = document.getElementById('importForm');
    const formData = new FormData(form);
    const submitBtn = document.querySelector('#importModal button[onclick="submitImport()"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader" class="animate-spin mr-2"></i> Đang import...';
        if (window.feather) feather.replace();

        const response = await fetch('{{ route("admin.exams.questions.import", $exam) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Có lỗi xảy ra khi import');
        }

        showNotification(data.message, 'success');
        closeImportModal();
        
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (error) {
        showNotification(error.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (window.feather) feather.replace();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (window.feather) feather.replace();
    initToggleSwitches();
    
    // Bind import button click event
    const importBtn = document.getElementById('btnImportQuestions');
    if (importBtn) {
        importBtn.addEventListener('click', openImportModal);
    }
    
    // Initial state for decrement button
    const decrementBtn = document.getElementById('decrementBtn');
    if (decrementBtn) {
        decrementBtn.disabled = {{ $exam->total_questions }} <= initialTotalQuestions;
    }
});
</script>
@endsection