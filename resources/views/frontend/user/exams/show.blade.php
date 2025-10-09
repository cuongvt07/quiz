@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-{{ $exam->isCompetency() ? 'blue' : 'purple' }}-600 to-{{ $exam->isCompetency() ? 'blue' : 'purple' }}-700 text-white p-8">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">{{ $exam->type_name }}</span>
                <span class="text-sm">{{ $exam->subject->name }}</span>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $exam->title }}</h1>
        </div>

        {{-- Content --}}
        <div class="p-8">
            {{-- Thông tin đề thi --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i data-lucide="clock" class="w-8 h-8 mx-auto text-blue-600 mb-2"></i>
                    <p class="text-2xl font-bold text-gray-800">{{ $exam->duration_minutes }}</p>
                    <p class="text-sm text-gray-600">Phút</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i data-lucide="file-text" class="w-8 h-8 mx-auto text-green-600 mb-2"></i>
                    <p class="text-2xl font-bold text-gray-800">{{ $exam->total_questions }}</p>
                    <p class="text-sm text-gray-600">Câu hỏi</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i data-lucide="ticket" class="w-8 h-8 mx-auto text-purple-600 mb-2"></i>
                    <p class="text-2xl font-bold text-gray-800">{{ Auth::user()->free_slots }}</p>
                    <p class="text-sm text-gray-600">Lượt còn lại</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i data-lucide="history" class="w-8 h-8 mx-auto text-orange-600 mb-2"></i>
                    <p class="text-2xl font-bold text-gray-800">{{ $attempts->count() }}</p>
                    <p class="text-sm text-gray-600">Lượt đã thi</p>
                </div>
            </div>

            {{-- Hướng dẫn --}}
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Hướng dẫn làm bài</h2>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2 mt-0.5"></i>
                        <span>Bài thi có <strong>{{ $exam->total_questions }} câu hỏi</strong>, thời gian làm bài <strong>{{ $exam->duration_minutes }} phút</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2 mt-0.5"></i>
                        <span>Mỗi lần bắt đầu thi sẽ trừ <strong>1 lượt thi</strong> của bạn</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2 mt-0.5"></i>
                        <span>Nhấn "Nộp bài" khi hoàn thành để xem kết quả</span>
                    </li>
                </ul>
            </div>

            {{-- Lịch sử thi --}}
            @if($attempts->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Lịch sử làm bài</h2>
                <div class="space-y-3">
                    @foreach($attempts->take(3) as $attempt)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Điểm: {{ $attempt->score }}/{{ $exam->total_questions }}</p>
                            <p class="text-sm text-gray-600">{{ $attempt->finished_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('user.exams.result', $attempt) }}" 
                           class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            Xem chi tiết
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Nút hành động --}}
            <div class="flex gap-4">
                <a href="{{ route('user.exams.index', ['type' => $exam->type]) }}" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                    Quay lại
                </a>
                
                @if($canAttempt)
                <form action="{{ route('user.exams.start', $exam) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i data-lucide="play" class="w-4 h-4 inline mr-2"></i>
                        Bắt đầu làm bài
                    </button>
                </form>
                @else
                <div class="flex-1 px-6 py-3 bg-gray-300 text-gray-500 rounded-lg text-center cursor-not-allowed">
                    <i data-lucide="lock" class="w-4 h-4 inline mr-2"></i>
                    Hết lượt thi
                </div>
                @endif
            </div>

            @if(!$canAttempt)
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-sm">
                    <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i>
                    Bạn đã hết lượt thi. Vui lòng <a href="{{ route('admin.subscription_plans.index') }}" class="text-blue-600 underline">nâng cấp gói</a> để tiếp tục.
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection
