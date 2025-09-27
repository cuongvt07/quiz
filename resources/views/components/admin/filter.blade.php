<form method="GET" action="" class="mb-4 flex flex-wrap gap-2 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm..." class="border rounded px-2 py-1 text-sm" />
    {{ $slot }}
    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Lọc</button>
    @if(request()->has('q') && request('q'))
        <a href="?" class="ml-2 text-gray-500 text-xs underline">Xoá lọc</a>
    @endif
</form>
