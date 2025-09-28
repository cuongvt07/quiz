@extends('layouts.admin')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-xl font-bold flex items-center gap-2"><i data-feather="folder"></i> Danh mục câu hỏi</h1>
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
<x-admin.table :headers="['#', 'Tên danh mục', 'Mô tả', 'Số câu hỏi', 'Hành động']">
    {{-- Form tạo mới --}}
    <tr class="border-b bg-gray-50">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="contents">
            @csrf
            <td class="px-3 py-2 text-gray-400">#</td>
            <td class="px-3 py-2">
                <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" placeholder="Tên danh mục" required value="{{ old('name') }}">
                @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="text" name="description" class="w-full border rounded px-2 py-1 text-sm" placeholder="Mô tả" value="{{ old('description') }}" maxlength="100">
                @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2 text-center text-gray-400">-</td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Thêm mới">
                    <i data-feather="plus" style="width:20px;height:20px"></i>
                </button>
            </td>
        </form>
    </tr>
    {{-- Danh sách + sửa inline --}}
    @foreach ($categories as $category)
        @if(request('edit') == $category->id)
        <tr class="border-b bg-yellow-50">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="contents">
                @csrf
                @method('PUT')
                <td class="px-3 py-2 text-gray-500">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2">
                    <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" required value="{{ old('name', $category->name) }}">
                    @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="description" class="w-full border rounded px-2 py-1 text-sm" value="{{ old('description', $category->description) }}" maxlength="100">
                    @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </td>
                <td class="px-3 py-2 text-center text-gray-400">-</td>
                <td class="px-3 py-2 flex gap-2 justify-end">
                    <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Lưu">
                        <i data-feather="check" style="width:20px;height:20px"></i>
                    </button>
                    <a href="{{ route('admin.categories.index', request()->except('edit')) }}" class="p-2 rounded hover:bg-gray-100 text-gray-500" title="Huỷ"><i data-feather="x" style="width:20px;height:20px"></i></a>
                </td>
            </form>
        </tr>
        @else
        <tr class="border-b hover:bg-gray-50">
            <td class="px-3 py-2 text-gray-500">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
            <td class="px-3 py-2 font-semibold flex items-center gap-2">
                <i data-feather="folder" style="width:20px;height:20px"></i> {{ $category->name }}
            </td>
            <td class="px-3 py-2 text-gray-700 max-w-xs truncate" style="max-width: 220px">
                {{ \Illuminate\Support\Str::limit($category->description, 50) }}
            </td>
            <td class="px-3 py-2 text-center">
                <span class="inline-block px-2 py-0.5 rounded bg-gray-200 text-xs text-gray-700">{{ $category->questions_count ?? 0 }}</span>
            </td>
            <td class="px-3 py-2 flex gap-2 justify-end">
                <a href="{{ route('admin.categories.index', array_merge(request()->except('page'), ['edit' => $category->id])) }}" class="p-2 rounded hover:bg-blue-100 text-blue-600" title="Sửa">
                    <i data-feather="edit-2" style="width:20px;height:20px"></i>
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirmDelete({{ $category->questions_count ?? 0 }})">
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
<div class="mt-4">{{ $categories->withQueryString()->links() }}</div>
<script>
function confirmDelete(questionCount) {
    if (questionCount > 0) {
        return confirm('Danh mục này đang có câu hỏi liên quan. Bạn có chắc chắn muốn xoá?');
    }
    return confirm('Bạn có chắc chắn muốn xoá danh mục này?');
}
if(window.feather) feather.replace();
</script>
@endsection