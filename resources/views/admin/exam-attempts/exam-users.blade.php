@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-lucide="users" class="text-blue-600 w-7 h-7"></i>
                <span>Danh sách thí sinh đã thi</span>
            </h1>
            <p class="text-gray-600 mt-2">Đề thi: <strong>{{ $exam->title }}</strong></p>
            <p class="text-sm text-gray-500">Môn: {{ $exam->subject->name }} ({{ $exam->type_name }})</p>
        </div>
        <a href="{{ route('admin.exams.' . ($exam->isCompetency() ? 'nangluc' : 'tuduy')) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại danh sách đề thi
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thí sinh
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Số lần thi
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Điểm gần nhất
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ngày thi gần nhất
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" 
                                     src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $user->exam_attempts_count }} lần
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($user->examAttempts->isNotEmpty() && $user->examAttempts->first()->score !== null)
                            <span class="text-lg font-bold text-gray-900">
                                {{ $user->examAttempts->first()->score }}<span class="text-sm text-gray-500">/{{ $exam->total_questions }}</span>
                            </span>
                            <div class="text-xs text-gray-500">
                                ({{ round(($user->examAttempts->first()->score / $exam->total_questions) * 100, 1) }}%)
                            </div>
                        @else
                            <span class="text-gray-400">Chưa hoàn thành</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        @if($user->examAttempts->isNotEmpty())
                            {{ $user->examAttempts->first()->finished_at ? $user->examAttempts->first()->finished_at->format('d/m/Y H:i') : 'Đang thi' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('admin.exam-attempts.user-attempts', ['exam' => $exam, 'user' => $user]) }}"
                           class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900">
                            <i data-lucide="history" class="w-4 h-4"></i>
                            Xem lịch sử
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-2"></i>
                        <p>Chưa có thí sinh nào thi đề này</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
