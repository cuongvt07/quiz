@extends('layouts.frontend')

@section('title', 'Danh sách đề thi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $type == 'nang_luc' ? 'Đề thi Năng lực' : ($type == 'tu_duy' ? 'Đề thi Tư duy' : 'Tất cả đề thi') }}
        </h1>
        <p class="mt-2 text-gray-600">
            Chọn đề thi phù hợp với nhu cầu học tập của bạn
        </p>
    </div>

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar -->
        <div class="hidden lg:block lg:col-span-3">
            <nav class="space-y-6" aria-label="Sidebar">
                <!-- Type Filter -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Loại đề thi
                    </h3>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('exams.list') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ !$type ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="truncate">Tất cả</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 inline-block py-0.5 px-2 text-xs rounded-full">
                                {{ $subjects->flatMap->all()->sum('exams_count') }}
                            </span>
                        </a>
                        <a href="{{ route('exams.list', ['type' => 'nang_luc']) }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $type == 'nang_luc' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="truncate">Năng lực</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 inline-block py-0.5 px-2 text-xs rounded-full">
                                {{ $subjects->get('nang_luc', collect())->sum('exams_count') }}
                            </span>
                        </a>
                        <a href="{{ route('exams.list', ['type' => 'tu_duy']) }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $type == 'tu_duy' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="truncate">Tư duy</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 inline-block py-0.5 px-2 text-xs rounded-full">
                                {{ $subjects->get('tu_duy', collect())->sum('exams_count') }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Subject Filter -->
                @foreach($subjects as $examType => $typeSubjects)
                    @if($typeSubjects->count() > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                                {{ $examType == 'nang_luc' ? 'Môn học Năng lực' : 'Môn học Tư duy' }}
                            </h3>
                            <div class="mt-2 space-y-1">
                                @foreach($typeSubjects as $subject)
                                    <a href="{{ route('exams.list', ['type' => $examType, 'subject' => $subject->id]) }}"
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                        <span class="truncate">{{ $subject->name }}</span>
                                        <span class="ml-auto bg-gray-100 text-gray-600 inline-block py-0.5 px-2 text-xs rounded-full">
                                            {{ $subject->exams_count }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
        </div>

        <!-- Main Content -->
        <main class="lg:col-span-9">
            @if($exams->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy đề thi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Hiện chưa có đề thi nào trong danh mục này.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($exams as $exam)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium 
                                        {{ $exam->subject->type == 'nang_luc' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $exam->subject->name }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $exam->total_questions }} câu hỏi</span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $exam->title }}
                                </h3>

                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $exam->duration_minutes }} phút
                                </div>

                                <a href="{{ route('exams.show', $exam) }}"
                                   class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Bắt đầu thi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $exams->links() }}
                </div>
            @endif
        </main>
    </div>
</div>

<!-- Mobile Filter Dialog -->
<div x-data="{ open: false }" class="fixed bottom-0 inset-x-0 lg:hidden">
    <!-- Filter button -->
    <button type="button"
            @click="open = true"
            class="fixed z-20 bottom-4 left-1/2 transform -translate-x-1/2 w-48 px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700">
        Lọc đề thi
    </button>

    <!-- Dialog -->
    <div x-show="open"
         class="fixed inset-0 z-40 overflow-y-auto"
         aria-labelledby="modal-title"
         x-ref="dialog"
         aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="open = false"
                 aria-hidden="true">
            </div>

            <!-- Panel -->
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-t-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 max-w-lg w-full sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button"
                            @click="open = false"
                            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Lọc đề thi
                        </h3>
                        <div class="mt-4 space-y-6">
                            <!-- Mobile filters - same structure as sidebar but optimized for mobile -->
                            <!-- Type Filter -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                                    Loại đề thi
                                </h4>
                                <div class="mt-2 space-y-1">
                                    <a href="{{ route('exams.list') }}"
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ !$type ? 'bg-blue-50 text-blue-700' : 'text-gray-600' }}">
                                        Tất cả
                                    </a>
                                    <a href="{{ route('exams.list', ['type' => 'nang_luc']) }}"
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $type == 'nang_luc' ? 'bg-blue-50 text-blue-700' : 'text-gray-600' }}">
                                        Năng lực
                                    </a>
                                    <a href="{{ route('exams.list', ['type' => 'tu_duy']) }}"
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $type == 'tu_duy' ? 'bg-blue-50 text-blue-700' : 'text-gray-600' }}">
                                        Tư duy
                                    </a>
                                </div>
                            </div>

                            <!-- Subject Filters -->
                            @foreach($subjects as $examType => $typeSubjects)
                                @if($typeSubjects->count() > 0)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                                            {{ $examType == 'nang_luc' ? 'Môn học Năng lực' : 'Môn học Tư duy' }}
                                        </h4>
                                        <div class="mt-2 space-y-1">
                                            @foreach($typeSubjects as $subject)
                                                <a href="{{ route('exams.list', ['type' => $examType, 'subject' => $subject->id]) }}"
                                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700' : 'text-gray-600' }}">
                                                    {{ $subject->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Xử lý animation cho cards khi scroll
    function revealCards() {
        $('.exam-card').each(function() {
            if ($(this).offset().top < $(window).scrollTop() + $(window).height() - 100) {
                $(this).addClass('opacity-100').removeClass('opacity-0');
            }
        });
    }

    // Gọi hàm lần đầu và bind vào sự kiện scroll
    revealCards();
    $(window).scroll(revealCards);
});
</script>
@endpush