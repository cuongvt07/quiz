@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-lucide="clock" class="text-purple-600 w-7 h-7"></i>
                <span>Lịch sử thi của {{ $user->name }}</span>
            </h1>
            <p class="text-gray-600 mt-2">Đề thi: <strong>{{ $exam->title }}</strong></p>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.exam-attempts.exam-users', $exam) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Quay lại
        </a>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng số lần thi</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_attempts'] }}</p>
                </div>
                <i data-lucide="list" class="w-10 h-10 text-blue-600 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Đã hoàn thành</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed_attempts'] }}</p>
                </div>
                <i data-lucide="check-circle" class="w-10 h-10 text-green-600 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Điểm trung bình</p>
                    <p class="text-3xl font-bold text-purple-600">{{ round($stats['average_score'] ?? 0, 1) }}</p>
                </div>
                <i data-lucide="trending-up" class="w-10 h-10 text-purple-600 opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Điểm cao nhất</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['best_score'] ?? 0 }}</p>
                </div>
                <i data-lucide="award" class="w-10 h-10 text-orange-600 opacity-20"></i>
            </div>
        </div>
    </div>

    {{-- Attempts List --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Lần thi
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Trạng thái
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Điểm số
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Số câu đúng/sai
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
                @forelse($attempts as $index => $attempt)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium">
                                Lần {{ $attempts->total() - $attempts->firstItem() - $index + 1 }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($attempt->isCompleted())
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Hoàn thành
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                Đang thi
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($attempt->isCompleted())
                            <div>
                                <span class="text-2xl font-bold text-gray-900">{{ $attempt->score }}</span>
                                <span class="text-sm text-gray-500">/{{ $exam->total_questions }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                ({{ $attempt->score_percentage }}%)
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($attempt->isCompleted())
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-green-600 font-medium">
                                    <i data-lucide="check" class="w-4 h-4 inline"></i>
                                    {{ $attempt->correct_count }}
                                </span>
                                <span class="text-red-600 font-medium">
                                    <i data-lucide="x" class="w-4 h-4 inline"></i>
                                    {{ $attempt->wrong_count }}
                                </span>
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <div class="text-gray-900">{{ $attempt->created_at->format('d/m/Y') }}</div>
                        <div class="text-gray-500">{{ $attempt->created_at->format('H:i') }}</div>
                        @if($attempt->isCompleted() && $attempt->duration_in_minutes)
                            <div class="text-xs text-gray-400 mt-1">
                                ({{ $attempt->duration_in_minutes }} phút)
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                            @if($attempt->isCompleted())
                                <a href="{{ route('admin.exam-attempts.attempt-detail', $attempt) }}"
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            @endif
                            <form action="{{ route('admin.exam-attempts.destroy', $attempt) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Xóa lượt thi này sẽ hoàn lại 1 lượt cho user. Bạn có chắc chắn?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Xóa">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-2"></i>
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
        {{ $attempts->links() }}
    </div>
    @endif
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
