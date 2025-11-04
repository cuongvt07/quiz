@extends('layouts.admin')

@section('content')
@php
$type = request()->routeIs('admin.exams.nangluc') ? 'nang_luc' : (request()->routeIs('admin.exams.tuduy') ? 'tu_duy' : 'all');
@endphp
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-xl font-bold flex items-center gap-2">
        <i data-feather="file-text"></i>
        @if($type === 'nang_luc')
            Đề thi Năng lực
        @elseif($type === 'tu_duy')
            Đề thi Tư duy
        @else
            Danh sách đề thi
        @endif
    </h1>
</div>
<div class="flex flex-wrap gap-2 items-center mb-2">
    <x-admin.filter />
    <a href="{{ route('admin.exams.export', array_filter(['type' => $type !== 'all' ? $type : null, 'search' => request('search')])) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center ml-2">
        <i data-feather="download" class="mr-2"></i> Xuất Excel
    </a>
</div>
@if(session('success'))
    <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-3 p-2 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
@endif
<x-admin.table :headers="['#', 'Tên đề thi', 'Môn học', 'Thời gian (phút)', 'Số câu hỏi', 'Hành động']">
    {{-- Form tạo mới --}}
    <tr class="border-b bg-gray-50">
        <form action="{{ route('admin.exams.store') }}" method="POST" class="contents">
            @csrf
            <td class="px-3 py-2 text-gray-400">#</td>
            <td class="px-3 py-2">
                <input type="text" name="title" class="w-full border rounded px-2 py-1 text-sm" placeholder="Tên đề thi" required value="{{ old('title') }}">
                @error('title')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <select name="subject_id" class="w-full border rounded px-2 py-1 text-sm" required>
                    <option value="">-- Môn học --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @if(old('subject_id')==$subject->id) selected @endif>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="number" name="duration_minutes" class="w-full border rounded px-2 py-1 text-sm" placeholder="Thời gian" min="1" required value="{{ old('duration_minutes') }}">
                @error('duration_minutes')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="number" name="total_questions" class="w-full border rounded px-2 py-1 text-sm" placeholder="Số câu hỏi" min="1" required value="{{ old('total_questions') }}">
                @error('total_questions')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Thêm mới">
                    <i data-feather="plus" style="width:20px;height:20px"></i>
                </button>
            </td>
        </form>
    </tr>
    {{-- Danh sách + sửa inline --}}
    @foreach ($exams as $exam)
        @if(request('edit') == $exam->id)
        <tr class="border-b bg-yellow-50">
            <form action="{{ route('admin.exams.update', $exam) }}" method="POST" class="contents">
                @csrf
                @method('PUT')
                <td class="px-3 py-2 text-gray-500">{{ ($exams->currentPage() - 1) * $exams->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2">
                    <input type="text" name="title" class="w-full border rounded px-2 py-1 text-sm" required value="{{ old('title', $exam->title) }}">
                    @error('title')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2">
                    <select name="subject_id" class="w-full border rounded px-2 py-1 text-sm" required>
                        <option value="">-- Môn học --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @if(old('subject_id', $exam->subject_id)==$subject->id) selected @endif>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="duration_minutes" class="w-full border rounded px-2 py-1 text-sm" min="1" required value="{{ old('duration_minutes', $exam->duration_minutes) }}">
                    @error('duration_minutes')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="total_questions" class="w-full border rounded px-2 py-1 text-sm" min="1" required value="{{ old('total_questions', $exam->total_questions) }}">
                    @error('total_questions')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2 flex gap-2 justify-end">
                    <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Lưu">
                        <i data-feather="check" style="width:20px;height:20px"></i>
                    </button>
                    <a href="{{ route('admin.exams.index', request()->except('edit')) }}" class="p-2 rounded hover:bg-gray-100 text-gray-500" title="Huỷ"><i data-feather="x" style="width:20px;height:20px"></i></a>
                </td>
            </form>
        </tr>
        @else
        <tr class="border-b hover:bg-gray-50">
            <td class="px-3 py-2 text-gray-500">{{ ($exams->currentPage() - 1) * $exams->perPage() + $loop->iteration }}</td>
            <td class="px-3 py-2 font-semibold flex items-center gap-2">
                <i data-feather="file-text" style="width:20px;height:20px"></i> {{ $exam->title }}
            </td>
            <td class="px-3 py-2">{{ $exam->subject->name ?? '-' }}</td>
            <td class="px-3 py-2">{{ $exam->duration_minutes }}</td>
            <td class="px-3 py-2">{{ $exam->total_questions }}</td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <a href="{{ route('admin.exam-attempts.exam-users', $exam) }}" class="p-2 rounded hover:bg-purple-100 text-purple-600" title="Xem lượt thi">
                    <i data-feather="users" style="width:20px;height:20px"></i>
                </a>
                <a href="{{ route('admin.exams.show', $exam) }}" class="p-2 rounded hover:bg-green-100 text-green-700" title="Chi tiết đề thi">
                    <i data-feather="info" style="width:20px;height:20px"></i>
                </a>
                <a href="{{ route('admin.exams.index', array_merge(request()->except('page'), ['edit' => $exam->id])) }}" class="p-2 rounded hover:bg-blue-100 text-blue-600" title="Sửa">
                    <i data-feather="edit-2" style="width:20px;height:20px"></i>
                </a>
                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá đề thi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded hover:bg-red-100 text-red-600" title="Xoá">
                        <i data-feather="trash-2" style="width:20px;height:20px"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endif
    @endforeach
</x-admin.table>
<div class="mt-4">{{ $exams->appends(request()->except('page'))->links() }}</div>
<script>
if(window.feather) feather.replace();
</script>
@endsection