@extends('layouts.frontend')

@section('title', 'Trang chủ')

@push('styles')
<style>
    .gradient-overlay {
        background: linear-gradient(rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9));
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative bg-gray-900 h-[600px]">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 gradient-overlay"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex flex-col justify-center h-full text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white mb-6">
                Kiểm tra năng lực và tư duy
                <span class="block text-blue-200">cùng Quiz Online</span>
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Hệ thống thi trắc nghiệm thông minh giúp bạn đánh giá và phát triển khả năng của mình một cách hiệu quả.
            </p>
            <div>
                <a href="{{ route('exams.list') }}" 
                   class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-full text-blue-700 bg-white hover:bg-blue-50 transition duration-300">
                    Bắt đầu luyện tập
                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Tại sao chọn Quiz Online?
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Chúng tôi cung cấp nền tảng học tập và kiểm tra kiến thức hiện đại, tiện lợi
            </p>
        </div>

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white p-6 rounded-lg shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Đa dạng bài thi</h3>
                        <p class="mt-2 text-gray-600">
                            Nhiều dạng đề thi khác nhau giúp bạn rèn luyện toàn diện các kỹ năng.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white p-6 rounded-lg shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Tiết kiệm thời gian</h3>
                        <p class="mt-2 text-gray-600">
                            Làm bài và nhận kết quả ngay lập tức, giúp bạn học tập hiệu quả hơn.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white p-6 rounded-lg shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Theo dõi tiến độ</h3>
                        <p class="mt-2 text-gray-600">
                            Dễ dàng xem lại lịch sử làm bài và đánh giá sự tiến bộ của bản thân.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exam Types Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Khám phá các loại đề thi
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Chọn loại đề thi phù hợp với mục tiêu học tập của bạn
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Năng lực -->
            <div class="relative rounded-2xl overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-700 opacity-90 group-hover:opacity-95 transition-opacity"></div>
                <img src="{{ asset('images/competency.jpg') }}" alt="Năng lực" class="w-full h-80 object-cover">
                <div class="absolute inset-0 flex flex-col justify-center px-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Đề thi Năng lực</h3>
                    <p class="mb-6 text-blue-100">Kiểm tra khả năng tư duy logic, xử lý thông tin và giải quyết vấn đề.</p>
                    <a href="{{ route('exams.list', ['type' => 'nangluc']) }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-white text-sm font-medium rounded-full text-white hover:bg-white hover:text-blue-600 transition-colors w-fit">
                        Xem chi tiết
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Tư duy -->
            <div class="relative rounded-2xl overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-indigo-700 opacity-90 group-hover:opacity-95 transition-opacity"></div>
                <img src="{{ asset('images/cognitive.jpg') }}" alt="Tư duy" class="w-full h-80 object-cover">
                <div class="absolute inset-0 flex flex-col justify-center px-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Đề thi Tư duy</h3>
                    <p class="mb-6 text-indigo-100">Phát triển khả năng suy luận, phân tích và đưa ra quyết định.</p>
                    <a href="{{ route('exams.list', ['type' => 'tuduy']) }}"
                       class="inline-flex items-center px-6 py-3 border-2 border-white text-sm font-medium rounded-full text-white hover:bg-white hover:text-indigo-600 transition-colors w-fit">
                        Xem chi tiết
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-blue-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Sẵn sàng để bắt đầu?</span>
            <span class="block text-blue-200">Tạo tài khoản miễn phí ngay hôm nay.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                    Đăng ký ngay
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('exams.list') }}"
                   class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Xem đề thi
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animations on scroll
    function revealOnScroll() {
        var reveals = document.querySelectorAll('.group');
        
        reveals.forEach((reveal) => {
            var windowHeight = window.innerHeight;
            var elementTop = reveal.getBoundingClientRect().top;
            var elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                reveal.classList.add('opacity-100');
                reveal.classList.remove('opacity-0');
            }
        });
    }

    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll();
});
</script>
@endpush