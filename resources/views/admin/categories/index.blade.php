@extends('layouts.admin')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Danh mục câu hỏi</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">+ Thêm mới</a>
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
    @foreach ($categories as $category)
        <tr class="border-b hover:bg-gray-50">
            <td class="px-3 py-2 text-gray-500">{{ $category->id }}</td>
            <td class="px-3 py-2 font-semibold flex items-center gap-2">
                <i data-feather="folder"></i> {{ $category->name }}
            </td>
            <td class="px-3 py-2 text-gray-700">{{ $category->description }}</td>
            <td class="px-3 py-2">
                <span class="inline-block px-2 py-0.5 rounded bg-gray-200 text-xs text-gray-700">{{ $category->questions_count ?? 0 }}</span>
            </td>
            <td class="px-3 py-2 flex gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:underline">Sửa</a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirmDelete({{ $category->questions_count ?? 0 }})">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Xoá</button>
                </form>
            </td>
        </tr>
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