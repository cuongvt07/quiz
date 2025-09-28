@extends('layouts.admin')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-blue-600 flex items-center"><i data-feather="arrow-left"></i> Quay lại</a>
        <h1 class="text-2xl font-bold flex items-center gap-2"><i data-feather="file-text"></i> Chi tiết đề thi {{ $exam->title }}</h1>
    </div>
    <div class="flex gap-2">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center" id="btnImportQuestions">
            <i data-feather="upload" class="mr-2"></i> Import câu hỏi
        </button>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center" id="btnAddQuestion">
            <i data-feather="plus" class="mr-2"></i> Thêm câu hỏi
        </button>
    </div>
</div>
<div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded shadow p-4">
        <div class="mb-2 text-lg font-semibold">Thông tin đề thi</div>
        <div><b>Tên đề:</b> {{ $exam->title }}</div>
        <div><b>Môn học:</b> {{ $exam->subject->name ?? '-' }}</div>
        <div><b>Thời gian:</b> {{ $exam->duration_minutes }} phút</div>
        <div><b>Số câu hỏi tối đa:</b> {{ $exam->total_questions }}</div>
        <div><b>Đã thêm:</b> <span id="currentQuestionCount">{{ $exam->questions->count() }}</span> / {{ $exam->total_questions }}</div>
    </div>
</div>
<div class="mb-4">
    <h2 class="text-lg font-bold mb-2 flex items-center gap-2"><i data-feather="list"></i> Danh sách câu hỏi</h2>
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nội dung câu hỏi</th>
                    <th class="px-4 py-2">Số đáp án</th>
                    <th class="px-4 py-2">Đáp án đúng</th>
                    <th class="px-4 py-2">Trạng thái</th>
                    <th class="px-4 py-2 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exam->questions as $i => $question)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-500">{{ $i+1 }}</td>
                    <td class="px-4 py-2">{{ $question->question }}</td>
                    <td class="px-4 py-2 text-center">{{ $question->questionChoices->count() }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $correct = $question->questionChoices->firstWhere('is_correct', 1); @endphp
                        {{ $correct ? $correct->name : '-' }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($question->is_active)
                            <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-700 text-xs">Hiện</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded bg-gray-200 text-gray-500 text-xs">Ẩn</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 flex gap-2 justify-end">
                        <button class="p-2 rounded hover:bg-blue-100 text-blue-600 btnEditQuestion" data-id="{{ $question->id }}" title="Sửa"><i data-feather="edit-2" style="width:20px;height:20px"></i></button>
                        <button class="p-2 rounded hover:bg-red-100 text-red-600 btnDeleteQuestion" data-id="{{ $question->id }}" title="Xoá"><i data-feather="trash-2" style="width:20px;height:20px"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- Modal thêm/sửa câu hỏi, modal import sẽ bổ sung sau --}}
<script>
if(window.feather) feather.replace();
</script>
@endsection
