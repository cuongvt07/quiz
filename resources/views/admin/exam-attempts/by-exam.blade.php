@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-lucide="file-text" class="text-blue-600 w-7 h-7"></i>
                <span>Quản lý theo đề thi</span>
            </h1>
            <p class="text-gray-600 mt-2">Xem lịch sử thi theo từng đề thi</p>
        </div>
    </div>

    {{-- Loại đề thi tabs --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4">
        <div class="flex gap-4 border-b border-gray-200">
            <button onclick="switchTab('all')" 
                    class="px-4 py-2 font-medium text-sm border-b-2 transition-all tab-button active-tab" 
                    data-tab="all">
                Tất cả đề thi
            </button>
            <button onclick="switchTab('nang_luc')" 
                    class="px-4 py-2 font-medium text-sm border-b-2 transition-all tab-button" 
                    data-tab="nang_luc">
                Đề thi Năng lực
            </button>
            <button onclick="switchTab('tu_duy')" 
                    class="px-4 py-2 font-medium text-sm border-b-2 transition-all tab-button" 
                    data-tab="tu_duy">
                Đề thi Tư duy
            </button>
        </div>

        {{-- Bảng danh sách đề thi --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tên đề thi
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Môn học
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số thí sinh
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tổng lượt thi
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Điểm trung bình
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="exam-list">
                    @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50" data-type="{{ $exam->type }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($exam->type === 'nang_luc')
                                    <i data-lucide="zap" class="w-5 h-5 text-yellow-500 mr-2"></i>
                                @else
                                    <i data-lucide="activity" class="w-5 h-5 text-blue-500 mr-2"></i>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $exam->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $exam->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-900">{{ $exam->subject->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $exam->total_users }} thí sinh
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-900">{{ $exam->total_attempts }} lượt</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($exam->avg_score !== null)
                                <span class="text-sm font-medium text-gray-900">
                                    {{ number_format($exam->avg_score, 1) }}/{{ $exam->total_questions }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    ({{ number_format(($exam->avg_score / $exam->total_questions) * 100, 1) }}%)
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Chưa có dữ liệu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.exam-attempts.exam-users', $exam) }}" 
                               class="inline-flex items-center px-3 py-1 border border-blue-100 rounded-lg text-sm text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                            <p>Chưa có dữ liệu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function switchTab(type) {
    // Update active tab
    document.querySelectorAll('.tab-button').forEach(btn => {
        if (btn.dataset.tab === type) {
            btn.classList.add('active-tab', 'border-blue-500', 'text-blue-600');
            btn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        } else {
            btn.classList.remove('active-tab', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }
    });

    // Filter exam rows
    const rows = document.querySelectorAll('#exam-list tr[data-type]');
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Initial setup
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.tab-button.active-tab').classList.add('border-blue-500', 'text-blue-600');
});
</script>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
@endsection