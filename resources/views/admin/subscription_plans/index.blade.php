@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-feather="dollar-sign"></i> Gói nâng cấp thành viên
    </h1>
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

<x-admin.table :headers="['#', 'Tên gói', 'Giá (VNĐ)', 'Số ngày', 'Số lượt thi', 'Mô tả', 'Hành động']">
    {{-- Form tạo mới --}}
    <tr class="border-b bg-gray-50">
        <form action="{{ route('admin.subscription_plans.store') }}" method="POST" class="contents">
            @csrf
            <td class="px-3 py-2 text-gray-400">#</td>
            <td class="px-3 py-2">
                <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" placeholder="Tên gói" required value="{{ old('name') }}">
                @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="number" name="price" class="w-full border rounded px-2 py-1 text-sm" placeholder="Giá" min="0" step="0.01" required value="{{ old('price') }}">
                @error('price')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="number" name="duration_days" class="w-full border rounded px-2 py-1 text-sm" placeholder="Số ngày" min="1" required value="{{ old('duration_days') }}">
                @error('duration_days')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="number" name="attempts" class="w-full border rounded px-2 py-1 text-sm" placeholder="Số lượt thi" min="0" required value="{{ old('attempts', 0) }}">
                @error('attempts')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2">
                <input type="text" name="description" class="w-full border rounded px-2 py-1 text-sm" placeholder="Mô tả" value="{{ old('description') }}" maxlength="100">
                @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            </td>
            <td class="px-3 py-2 flex gap-2 justify-center">
                <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Thêm mới">
                    <i data-feather="plus" style="width:20px;height:20px"></i>
                </button>
            </td>
        </form>
    </tr>

    {{-- Danh sách + sửa inline --}}
    @foreach ($plans as $plan)
        @if(request('edit') == $plan->id)
            <tr class="border-b bg-yellow-50">
                <form action="{{ route('admin.subscription_plans.update', $plan) }}" method="POST" class="contents">
                    @csrf
                    @method('PUT')
                    <td class="px-3 py-2 text-gray-500">{{ ($plans->currentPage() - 1) * $plans->perPage() + $loop->iteration }}</td>
                    <td class="px-3 py-2">
                        <input type="text" name="name" class="w-full border rounded px-2 py-1 text-sm" required value="{{ old('name', $plan->name) }}">
                        @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" name="price" class="w-full border rounded px-2 py-1 text-sm" min="0" step="0.01" required value="{{ old('price', $plan->price) }}">
                        @error('price')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" name="duration_days" class="w-full border rounded px-2 py-1 text-sm" min="1" required value="{{ old('duration_days', $plan->duration_days) }}">
                        @error('duration_days')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" name="attempts" class="w-full border rounded px-2 py-1 text-sm" min="0" required value="{{ old('attempts', $plan->attempts) }}">
                        @error('attempts')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" name="description" class="w-full border rounded px-2 py-1 text-sm" value="{{ old('description', $plan->description) }}" maxlength="100">
                        @error('description')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </td>
                    <td class="px-3 py-2 flex gap-2 justify-center">
                        <button type="submit" class="p-2 rounded bg-blue-600 text-white hover:bg-blue-700" title="Lưu">
                            <i data-feather="check" style="width:20px;height:20px"></i>
                        </button>
                        <a href="{{ route('admin.subscription_plans.index', request()->except('edit')) }}" class="p-2 rounded hover:bg-gray-100 text-gray-500" title="Huỷ">
                            <i data-feather="x" style="width:20px;height:20px"></i>
                        </a>
                    </td>
                </form>
            </tr>
        @else
            <tr class="border-b hover:bg-gray-50">
                <td class="px-3 py-2 text-gray-500">{{ ($plans->currentPage() - 1) * $plans->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 font-semibold flex items-center gap-2">
                    <i data-feather="dollar-sign" style="width:20px;height:20px"></i> {{ $plan->name }}
                </td>
                <td class="px-3 py-2">{{ number_format($plan->price, 0, ',', '.') }}</td>
                <td class="px-3 py-2">{{ $plan->duration_days }}</td>
                <td class="px-3 py-2">{{ $plan->attempts }} lượt</td>
                <td class="px-3 py-2 text-gray-700 max-w-xs truncate" style="max-width: 220px">
                    {{ \Illuminate\Support\Str::limit($plan->description, 50) }}
                </td>
                <td class="px-3 py-2 flex gap-2 justify-center">
                    <a href="{{ route('admin.subscription_plans.index', array_merge(request()->except('page'), ['edit' => $plan->id])) }}" class="p-2 rounded hover:bg-blue-100 text-blue-600" title="Sửa">
                        <i data-feather="edit-2" style="width:20px;height:20px"></i>
                    </a>
                    <form action="{{ route('admin.subscription_plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá gói này?')">
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

<div class="mt-4 flex justify-end">{{ $plans->withQueryString()->links() }}</div>

<script>
if (window.feather) feather.replace();
</script>
@endsection
