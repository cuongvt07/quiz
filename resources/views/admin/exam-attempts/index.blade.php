@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-feather="clock" class="text-blue-600 w-7 h-7"></i>
                <span>Quản lý lượt thi</span>
            </h1>
            <p class="text-gray-600 mt-2">Tổng quan tất cả lượt thi trong hệ thống</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-4 mb-6">
        <a href="{{ route('admin.exam-attempts.index') }}"
           class="px-6 py-3 rounded-lg font-medium transition {{ !$type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i data-feather="list" class="w-4 h-4 inline mr-2"></i>
            Tất cả
        </a>
        <a href="{{ route('admin.exam-attempts.index', ['type' => 'nangluc']) }}"
           class="px-6 py-3 rounded-lg font-medium transition {{ $type === 'nangluc' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i data-feather="zap" class="w-4 h-4 inline mr-2"></i>
            Năng lực
        </a>
        <a href="{{ route('admin.exam-attempts.index', ['type' => 'tuduy']) }}"
           class="px-6 py-3 rounded-lg font-medium transition {{ $type === 'tuduy' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i data-feather="activity" class="w-4 h-4 inline mr-2"></i>
            Tư duy
        </a>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Tổng lượt thi --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng lượt thi</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_attempts']) }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="text-emerald-600">+{{ number_format($stats['today_total']) }}</span> hôm nay
                    </p>
                </div>
                <i data-feather="users" class="w-10 h-10 text-blue-600 opacity-20"></i>
            </div>
        </div>

        {{-- Thi Năng lực --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Thi Năng lực</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['competency_attempts']) }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="text-emerald-600">+{{ number_format($stats['today_competency']) }}</span> hôm nay
                    </p>
                </div>
                <i data-feather="zap" class="w-10 h-10 text-blue-600 opacity-20"></i>
            </div>
        </div>

        {{-- Thi Tư duy --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Thi Tư duy</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['cognitive_attempts']) }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="text-emerald-600">+{{ number_format($stats['today_cognitive']) }}</span> hôm nay
                    </p>
                </div>
                <i data-feather="activity" class="w-10 h-10 text-purple-600 opacity-20"></i>
            </div>
        </div>

        {{-- Lượt thi trong ngày --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Lượt thi hôm nay</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ number_format($stats['today_total']) }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="text-blue-600">{{ number_format($stats['today_competency']) }}</span> Năng lực,
                        <span class="text-purple-600">{{ number_format($stats['today_cognitive']) }}</span> Tư duy
                    </p>
                </div>
                <i data-feather="calendar" class="w-10 h-10 text-emerald-600 opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thí sinh
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Đề thi
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Loại đề 
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Điểm số
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thời gian
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attempts as $attempt)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $attempt->user->profile_photo_url }}" 
                                     alt="{{ $attempt->user->name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $attempt->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $attempt->exam->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $attempt->exam->isCompetency() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $attempt->exam?->subject?->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">{{ $attempt->score }}</span>
                            <span class="text-sm text-gray-500">/{{ $attempt->exam->total_questions }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            ({{ $attempt->score_percentage }}%)
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        @php
                            $finishedAt = is_object($attempt->finished_at) ? $attempt->finished_at : \Carbon\Carbon::parse($attempt->finished_at);
                        @endphp
                        <div class="text-gray-900">{{ $finishedAt->format('d/m/Y') }}</div>
                        <div class="text-gray-500">{{ $finishedAt->format('H:i') }}</div>
                        @if($attempt->duration_in_minutes)
                            <div class="text-xs text-gray-400 mt-1">
                                ({{ $attempt->duration_in_minutes }} phút)
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.exam-attempts.attempt-detail', $attempt) }}"
                               class="text-blue-600 hover:text-blue-900"
                               title="Xem chi tiết">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.exam-attempts.user-attempts', ['exam' => $attempt->exam, 'user' => $attempt->user]) }}"
                               class="text-purple-600 hover:text-purple-900"
                               title="Xem lịch sử">
                                <i data-feather="history" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.exam-attempts.destroy', $attempt) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Xóa lượt thi này sẽ hoàn lại 1 lượt cho user. Bạn có chắc chắn?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Xóa">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i data-feather="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-2"></i>
                        <p>Chưa có lượt thi nào</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($attempts->hasPages())
    <div class="mt-6">
        {{ $attempts->appends(['type' => $type])->links() }}
    </div>
    @endif
</div>

<script>
if(window.feather) feather.replace();
</script>
@endsection
