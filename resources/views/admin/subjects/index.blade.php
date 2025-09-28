@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2"><i data-feather="book"></i> Môn học</h1>

</div>
<x-admin.filter>
    {{-- Có thể thêm select, filter khác ở đây nếu cần --}}
</x-admin.filter>
@if(session('success'))
    <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-3 p-2 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
@endif
<x-admin.table :headers="['#', 'Tên môn học', 'Loại', 'Hành động']">
    {{-- Form tạo mới --}}
    <tr class="border-b bg-gray-50">
        <form action="{{ route('admin.subjects.store') }}" method="POST" class="contents">
            @csrf
            <td class="px-3 py-2 text-gray-400">#</td>
            <td class="px-3 py-2">
                <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" placeholder="Tên môn học" required value="{{ old('name') }}">
                @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <select name="type" class="w-full border rounded px-2 py-1 text-sm" required>
                    <option value="">-- Loại --</option>
                    <option value="nang_luc" {{ old('type')=='nang_luc'?'selected':'' }}>Năng lực</option>
                    <option value="tu_duy" {{ old('type')=='tu_duy'?'selected':'' }}>Tư duy</option>
                </select>
                @error('type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Thêm mới">
                    <i data-feather="plus" style="width:20px;height:20px"></i>
                </button>
            </td>
        </form>
    </tr>
    {{-- Danh sách + sửa inline --}}
    @foreach ($subjects as $subject)
        @if(request('edit') == $subject->id)
        <tr class="border-b bg-yellow-50">
            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="contents">
                @csrf
                @method('PUT')
                <td class="px-3 py-2 text-gray-500">{{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2">
                    <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" required value="{{ old('name', $subject->name) }}">
                    @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2">
                    <select name="type" class="w-full border rounded px-2 py-1 text-sm" required>
                        <option value="nang_luc" {{ old('type', $subject->type)=='nang_luc'?'selected':'' }}>Năng lực</option>
                        <option value="tu_duy" {{ old('type', $subject->type)=='tu_duy'?'selected':'' }}>Tư duy</option>
                    </select>
                    @error('type')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2 flex gap-2 justify-end">
                    <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Lưu">
                        <i data-feather="check" style="width:20px;height:20px"></i>
                    </button>
                    <a href="{{ route('admin.subjects.index', request()->except('edit')) }}" class="p-2 rounded hover:bg-gray-100 text-gray-500" title="Huỷ"><i data-feather="x" style="width:20px;height:20px"></i></a>
                </td>
            </form>
        </tr>
        @else
        <tr class="border-b hover:bg-gray-50">
            <td class="px-3 py-2 text-gray-500">{{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}</td>
            <td class="px-3 py-2 font-semibold flex items-center gap-2">
                <i data-feather="book" style="width:20px;height:20px"></i> {{ $subject->name }}
            </td>
            <td class="px-3 py-2">
                <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-xs text-gray-700">
                    {{ $subject->type == 'nang_luc' ? 'Năng lực' : 'Tư duy' }}
                </span>
            </td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <a href="{{ route('admin.subjects.index', array_merge(request()->except('page'), ['edit' => $subject->id])) }}" class="p-2 rounded hover:bg-blue-100 text-blue-600" title="Sửa">
                    <i data-feather="edit-2" style="width:20px;height:20px"></i>
                </a>
                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá môn học này?')">
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
<div class="mt-4 flex justify-end">{{ $subjects->withQueryString()->links() }}</div>
<script>
if(window.feather) feather.replace();
</script>
@endsection