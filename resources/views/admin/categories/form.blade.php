<form method="POST" action="{{ $action }}" class="space-y-3">
    @csrf
    @if(isset($method) && $method === 'PUT')
        @method('PUT')
    @endif
    <div>
        <label class="block text-sm font-medium mb-1">Tên danh mục</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" class="border rounded px-2 py-1 w-full" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Mô tả</label>
        <textarea name="description" class="border rounded px-2 py-1 w-full" rows="2">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Lưu</button>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-1 rounded border">Huỷ</a>
    </div>
</form>
