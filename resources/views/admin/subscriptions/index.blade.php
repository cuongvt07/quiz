@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i data-lucide="credit-card" class="w-5 h-5"></i> Thành viên đăng ký gói
    </h1>
    <a href="{{ route('admin.subscriptions.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center gap-2">
        <i data-lucide="plus" class="w-5 h-5"></i> Tạo mới
    </a>
</div>

@if(session('success'))
    <div class="mb-3 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-3 p-2 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
@endif

{{-- Alpine wrapper quản lý dòng đang mở --}}
<div x-data="{ openId: null }">
    <x-admin.table :headers="['Thành viên', 'Email', 'Trạng thái', 'Tổng lượt thi', 'Tổng gói', 'Chi tiết gói']">
        @php $grouped = $subscriptions->groupBy('user_id'); @endphp

        @foreach($grouped as $userId => $subs)
            @php
                $first = $subs->first();
                $activeCount = $subs->where('status', 'active')->count();
            @endphp

            {{-- Hàng chính --}}
            <tr class="border-b hover:bg-gray-50">
                <td class="px-3 py-2 font-medium text-gray-800">{{ $first->user->name }}</td>
                <td class="px-3 py-2 text-gray-700">{{ $first->user->email }}</td>
                <td class="px-3 py-2">
                    <span class="inline-block px-2 py-0.5 rounded-full text-sm font-medium
                        {{ $activeCount > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $activeCount > 0 ? $activeCount . ' gói hoạt động' : 'Không có gói hoạt động' }}
                    </span>
                </td>
                <td class="px-3 py-2 text-center">{{ $first->user->free_slots ?? 0 }} lượt</td>
                <td class="px-3 py-2 text-center">{{ $subs->count() }} gói</td>
                <td class="px-3 py-2 text-center">
                    <button 
                        @click="openId === {{ $userId }} ? openId = null : openId = {{ $userId }}" 
                        class="flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-800 transition">
                        <i data-lucide="chevron-down"
                           :class="openId === {{ $userId }} ? 'rotate-180 text-blue-700' : 'text-blue-600'"
                           class="w-4 h-4 transition-transform duration-200"></i>
                        <span x-text="openId === {{ $userId }} ? 'Thu gọn' : 'Mở rộng'"></span>
                    </button>
                </td>
            </tr>

            {{-- Hàng chi tiết mở rộng --}}
            <tr x-show="openId === {{ $userId }}"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="bg-gray-50 border-b">
                <td colspan="6" class="p-4">
                    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-gray-700 font-medium">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tên gói</th>
                                    <th class="px-3 py-2 text-center">Lượt thi</th>
                                    <th class="px-3 py-2 text-center">Thời gian</th>
                                    <th class="px-3 py-2 text-center">Trạng thái</th>
                                    <th class="px-3 py-2 text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($subs as $s)
                                    <tr>
                                        <td class="px-3 py-2">{{ $s->plan->name }}</td>
                                        <td class="px-3 py-2 text-center">{{ $s->plan->attempts }} lượt</td>
                                        <td class="px-3 py-2 text-center text-gray-600">
                                            {{ \Carbon\Carbon::parse($s->start_date)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($s->end_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $s->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $s->status === 'active' ? 'Đang hoạt động' : 'Hết hạn' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex justify-center gap-1">
                                                @if(\Carbon\Carbon::parse($s->end_date)->isPast())
                                                    <form action="{{ route('admin.subscriptions.renew', $s) }}" 
                                                          method="POST"
                                                          class="inline-block">
                                                        @csrf
                                                        <button type="submit"
                                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-full transition-colors group relative"
                                                                title=" Mua mới gói này">
                                                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                                            <span class="absolute hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 right-0 bottom-full mb-2 whitespace-nowrap">
                                                                    Mua mới: +{{ $s->plan->attempts }} lượt, +{{ $s->plan->duration_days }} ngày
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.subscriptions.destroy', $s) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Xóa gói này sẽ trừ {{ $s->plan->attempts }} lượt thi đã cộng. Bạn có chắc chắn?')"
                                                      class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-full transition-colors group relative"
                                                            title="Xóa gói này">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        <span class="absolute hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 right-0 bottom-full mb-2 whitespace-nowrap">
                                                            Xóa gói: -{{ $s->plan->attempts }} lượt thi
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>
</div>

<div class="mt-4 flex justify-end">{{ $subscriptions->withQueryString()->links() }}</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });
</script>
@endsection
