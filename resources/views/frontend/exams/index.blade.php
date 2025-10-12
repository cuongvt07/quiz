@extends('layouts.frontend')

@section('title', 'Danh sách đề thi')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{ $type == 'nang_luc' ? 'Đề thi Năng lực' : ($type == 'tu_duy' ? 'Đề thi Tư duy' : 'Tất cả đề thi') }}
            </h1>
            <p class="text-gray-600">
                Chọn đề thi phù hợp với nhu cầu học tập của bạn
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- SIDEBAR TRÁI - Danh mục & Bộ lọc -->
            <aside class="w-full lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Danh mục đề thi
                    </h2>

                    <!-- Loại đề thi -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Loại đề thi
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('exams.list') }}" 
                               class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-all {{ !$type ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>Tất cả</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ !$type ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $subjects->flatMap->all()->sum('exams_count') }}
                                </span>
                            </a>
                            <a href="{{ route('exams.list', ['type' => 'nang_luc']) }}"
                               class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-all {{ $type == 'nang_luc' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>Năng lực</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $type == 'nang_luc' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $subjects->get('nang_luc', collect())->sum('exams_count') }}
                                </span>
                            </a>
                            <a href="{{ route('exams.list', ['type' => 'tu_duy']) }}"
                               class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-all {{ $type == 'tu_duy' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>Tư duy</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $type == 'tu_duy' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $subjects->get('tu_duy', collect())->sum('exams_count') }}
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Môn học Năng lực -->
                    @if($subjects->has('nang_luc') && $subjects->get('nang_luc')->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Môn học Năng lực
                        </h3>
                        <div class="space-y-1">
                            @foreach($subjects->get('nang_luc') as $subject)
                                <a href="{{ route('exams.list', ['type' => 'nang_luc', 'subject' => $subject->id]) }}"
                                   class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-all {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <span class="truncate">{{ $subject->name }}</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full ml-2 {{ request('subject') == $subject->id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $subject->exams_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Môn học Tư duy -->
                    @if($subjects->has('tu_duy') && $subjects->get('tu_duy')->count() > 0)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Môn học Tư duy
                        </h3>
                        <div class="space-y-1">
                            @foreach($subjects->get('tu_duy') as $subject)
                                <a href="{{ route('exams.list', ['type' => 'tu_duy', 'subject' => $subject->id]) }}"
                                   class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-all {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <span class="truncate">{{ $subject->name }}</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full ml-2 {{ request('subject') == $subject->id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $subject->exams_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </aside>

            <!-- NỘI DUNG PHẢI - Danh sách đề thi -->
            <main class="flex-1 min-w-0">
                @if($exams->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Không tìm thấy đề thi</h3>
                        <p class="text-gray-600">
                            Hiện chưa có đề thi nào trong danh mục này.
                        </p>
                    </div>
                @else
                    <!-- Grid các đề thi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($exams as $exam)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-200 transition-all duration-200 overflow-hidden group">
                                <div class="p-6">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                            {{ $exam->subject->type == 'nang_luc' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                            {{ $exam->subject->name }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-500">
                                            {{ $exam->total_questions }} câu
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors min-h-[3.5rem]">
                                        {{ $exam->title }}
                                    </h3>

                                    <!-- Duration -->
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $exam->duration_minutes }} phút</span>
                                    </div>

                                    <!-- Button -->
                                    @if($exam->total_questions == 0)
                                        <button type="button" disabled
                                                class="block w-full text-center px-4 py-2.5 bg-gray-400 text-white text-sm font-semibold rounded-lg cursor-not-allowed">
                                            Đề thi đang update
                                        </button>
                                    @else
                                        <a href="{{ route('exams.show', $exam) }}"
                                           class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                            Bắt đầu thi
                                        </a>
                                    @endif
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
</div>

<!-- Mobile Filter Button (chỉ hiện trên mobile) -->
<div class="lg:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40">
    <button type="button"
            onclick="document.getElementById('mobileFilter').classList.remove('hidden')"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full shadow-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        Lọc đề thi
    </button>
</div>

<!-- Mobile Filter Modal -->
<div id="mobileFilter" class="hidden fixed inset-0 z-50 overflow-y-auto lg:hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('mobileFilter').classList.add('hidden')"></div>
    
    <!-- Modal Panel -->
    <div class="relative min-h-screen flex items-end">
        <div class="relative bg-white rounded-t-2xl w-full max-h-[85vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Lọc đề thi
                </h3>
                <button type="button"
                        onclick="document.getElementById('mobileFilter').classList.add('hidden')"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <!-- Loại đề thi -->
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Loại đề thi
                    </h4>
                    <div class="space-y-2">
                        <a href="{{ route('exams.list') }}" 
                           class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium {{ !$type ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }}">
                            <span>Tất cả</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                {{ $subjects->flatMap->all()->sum('exams_count') }}
                            </span>
                        </a>
                        <a href="{{ route('exams.list', ['type' => 'nang_luc']) }}"
                           class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium {{ $type == 'nang_luc' ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }}">
                            <span>Năng lực</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                {{ $subjects->get('nang_luc', collect())->sum('exams_count') }}
                            </span>
                        </a>
                        <a href="{{ route('exams.list', ['type' => 'tu_duy']) }}"
                           class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium {{ $type == 'tu_duy' ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }}">
                            <span>Tư duy</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                {{ $subjects->get('tu_duy', collect())->sum('exams_count') }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Môn học Năng lực -->
                @if($subjects->has('nang_luc') && $subjects->get('nang_luc')->count() > 0)
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Môn học Năng lực
                    </h4>
                    <div class="space-y-2">
                        @foreach($subjects->get('nang_luc') as $subject)
                            <a href="{{ route('exams.list', ['type' => 'nang_luc', 'subject' => $subject->id]) }}"
                               class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }}">
                                <span>{{ $subject->name }}</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    {{ $subject->exams_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Môn học Tư duy -->
                @if($subjects->has('tu_duy') && $subjects->get('tu_duy')->count() > 0)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Môn học Tư duy
                    </h4>
                    <div class="space-y-2">
                        @foreach($subjects->get('tu_duy') as $subject)
                            <a href="{{ route('exams.list', ['type' => 'tu_duy', 'subject' => $subject->id]) }}"
                               class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium {{ request('subject') == $subject->id ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }}">
                                <span>{{ $subject->name }}</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    {{ $subject->exams_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer Button -->
            <div class="p-6 border-t border-gray-200">
                <button type="button"
                        onclick="document.getElementById('mobileFilter').classList.add('hidden')"
                        class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                    Áp dụng
                </button>
            </div>
        </div>
    </div>
</div>
@endsection