@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            Danh sách đề thi {{ $type === \App\Models\Subject::TYPE_COMPETENCY ? 'Năng lực' : 'Tư duy' }}
        </h1>
        
        {{-- Tab chọn loại thi --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('user.exams.index', ['type' => \App\Models\Subject::TYPE_COMPETENCY]) }}"
               class="px-6 py-3 rounded-lg font-medium transition {{ $type === \App\Models\Subject::TYPE_COMPETENCY ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                <i data-lucide="trophy" class="w-5 h-5 inline mr-2"></i>
                Thi Năng lực
            </a>
            <a href="{{ route('user.exams.index', ['type' => \App\Models\Subject::TYPE_COGNITIVE]) }}"
               class="px-6 py-3 rounded-lg font-medium transition {{ $type === \App\Models\Subject::TYPE_COGNITIVE ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                <i data-lucide="brain" class="w-5 h-5 inline mr-2"></i>
                Thi Tư duy
            </a>
        </div>

        {{-- Thông tin lượt thi --}}
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="ticket" class="w-6 h-6 text-blue-600"></i>
                    <div>
                        <p class="text-sm text-gray-600">Số lượt thi còn lại</p>
                        <p class="text-2xl font-bold text-blue-600">{{ Auth::user()->free_slots }} lượt</p>
                    </div>
                </div>
                <a href="{{ route('admin.subscription_plans.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1"></i>
                    Nâng cấp gói
                </a>
            </div>
        </div>
    </div>

    {{-- Danh sách môn học --}}
    @if($subjects->isNotEmpty())
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Môn học</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($subjects as $subject)
            <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">
                {{ $subject->name }} ({{ $subject->exams_count }} đề)
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Danh sách đề thi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <span class="px-3 py-1 bg-{{ $exam->isCompetency() ? 'blue' : 'purple' }}-100 text-{{ $exam->isCompetency() ? 'blue' : 'purple' }}-700 text-xs font-medium rounded-full">
                        {{ $exam->type_name }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $exam->subject->name }}</span>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ $exam->title }}</h3>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                        {{ $exam->duration_minutes }} phút
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        {{ $exam->total_questions }} câu hỏi
                    </div>
                </div>
                
                <a href="{{ route('user.exams.show', $exam) }}" 
                   class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Xem chi tiết
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
            <p class="text-gray-500">Chưa có đề thi nào</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $exams->appends(['type' => $type])->links() }}
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
