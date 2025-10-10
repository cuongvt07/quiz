@extends('layouts.admin')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="help-circle"></i> Ngân hàng câu hỏi
    </h1>
</div>

<!-- Filters -->
<!-- Filters -->
<div class="mb-6 bg-white p-5 rounded-lg shadow-sm border border-gray-100">
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm câu hỏi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-feather="search" class="h-4 w-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        placeholder="Nhập nội dung câu hỏi..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Môn học</label>
                <select name="subject_id" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Question type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại câu hỏi</label>
                <select name="loai" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($questionTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('loai') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="is_active" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 justify-end md:justify-start">
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                    <i data-feather="filter" class="w-4 h-4"></i> Lọc
                </button>
                <a href="{{ route('admin.questions.list') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600 transition">
                    <i data-feather="x-circle" class="w-4 h-4"></i> Xóa lọc
                </a>
            </div>
        </div>

        <!-- Optional advanced filter section -->
        {{-- 
        <div id="advancedFilters" class="hidden pt-4 border-t mt-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Người tạo</label>
                    <input type="text" name="created_by" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tạo từ</label>
                    <input type="date" name="from_date" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="to_date" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>
        </div>
        --}}
    </form>
</div>


<!-- Questions Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Câu hỏi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Môn học</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Loại</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Đáp án</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($questions as $question)
                    <tr class="hover:bg-gray-50 transition cursor-pointer question-item" 
                        data-question-id="{{ $question->id }}">
                        <td class="px-4 py-3 text-sm text-gray-700 font-medium">
                            {{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">{{ $question->question }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $question->exams->first()->subject->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-indigo-700 font-medium">{{ $question->ten_loai }}</td>
                        <td class="px-4 py-3 text-center text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $question->questionChoices->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $question->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $question->is_active ? 'Hoạt động' : 'Đã ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="toggleAnswers({{ $question->id }})"
                                class="p-2 rounded-full hover:bg-gray-200 transition" title="Xem đáp án">
                                <i data-feather="chevron-down" class="w-5 h-5 text-gray-600 chevron-icon"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Hidden row: answers -->
                    <tr class="answer-details hidden bg-gray-50" data-question-id="{{ $question->id }}">
                        <td colspan="7" class="p-5">
                            <div class="space-y-3">
                                <h4 class="font-semibold text-gray-700 flex items-center gap-2">
                                    <i data-feather="check-circle" class="w-4 h-4"></i> Danh sách đáp án
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($question->questionChoices as $choice)
                                        <div class="flex items-start gap-3 p-3 rounded-lg border 
                                            {{ $choice->is_correct ? 'bg-green-50 border-green-300' : 'bg-white border-gray-200' }}">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                                    {{ $choice->is_correct ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                                    {{ chr(65 + $loop->index) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <p class="text-sm {{ $choice->is_correct ? 'text-green-900 font-semibold' : 'text-gray-700' }}">
                                                    {{ $choice->name }}
                                                </p>
                                                @if($choice->explanation)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <i data-feather="info" class="w-3 h-3 inline-block"></i>
                                                        {{ $choice->explanation }}
                                                    </p>
                                                @endif
                                                @if($choice->is_correct)
                                                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                        <i data-feather="check" class="w-3 h-3 mr-1"></i> Đáp án đúng
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-8 text-gray-500">
                            <i data-feather="inbox" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                            <p>Không tìm thấy câu hỏi nào</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questions->hasPages())
        <div class="px-6 py-4 border-t bg-white">
            {{ $questions->withQueryString()->links() }}
        </div>
    @endif
</div>

<!-- Styles -->
<style>
.chevron-icon { transition: transform 0.3s ease; }
.question-item.expanded .chevron-icon { transform: rotate(180deg); }

.answer-details {
    transition: all 0.3s ease;
}
.answer-details.show {
    display: table-row;
}
</style>

<!-- Scripts -->
<script>
function toggleAnswers(questionId) {
    const row = document.querySelector(`.question-item[data-question-id="${questionId}"]`);
    const details = document.querySelector(`.answer-details[data-question-id="${questionId}"]`);
    const chevron = row.querySelector('.chevron-icon');
    const expanded = row.classList.toggle('expanded');

    if (expanded) {
        details.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        localStorage.setItem(`q_${questionId}_expanded`, true);
    } else {
        details.classList.add('hidden');
        chevron.style.transform = '';
        localStorage.setItem(`q_${questionId}_expanded`, false);
    }

    if (window.feather) feather.replace();
}

function expandAll() {
    document.querySelectorAll('.question-item').forEach(row => {
        const id = row.dataset.questionId;
        row.classList.add('expanded');
        document.querySelector(`.answer-details[data-question-id="${id}"]`).classList.remove('hidden');
        localStorage.setItem(`q_${id}_expanded`, true);
    });
    if (window.feather) feather.replace();
}

function collapseAll() {
    document.querySelectorAll('.question-item').forEach(row => {
        const id = row.dataset.questionId;
        row.classList.remove('expanded');
        document.querySelector(`.answer-details[data-question-id="${id}"]`).classList.add('hidden');
        localStorage.setItem(`q_${id}_expanded`, false);
    });
    if (window.feather) feather.replace();
}

// Restore expanded state
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.question-item').forEach(row => {
        const id = row.dataset.questionId;
        const details = document.querySelector(`.answer-details[data-question-id="${id}"]`);
        if (localStorage.getItem(`q_${id}_expanded`) === 'true') {
            row.classList.add('expanded');
            details.classList.remove('hidden');
        }
    });
    if (window.feather) feather.replace();
});
</script>
@endsection
