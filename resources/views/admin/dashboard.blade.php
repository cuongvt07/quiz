@extends('layouts.admin')

@section('content')
@php
    // ====== Tiền xử lý dữ liệu cho biểu đồ ======
    $jsonFlags = JSON_UNESCAPED_UNICODE;

    // attemptsByMonth: [ ['month' => 'YYYY-MM', 'total' => n], ... ]
    $attemptPairs = collect($attemptsByMonth)->mapWithKeys(fn($row) => [$row->month => (int)$row->total]);

    // usersByMonth: [ ['month' => 'YYYY-MM', 'total' => n], ... ]
    $userPairs = collect($usersByMonth)->mapWithKeys(fn($row) => [$row->month => (int)$row->total]);

    // Hợp nhất tập tháng và sort tăng dần
    $allMonths = $attemptPairs->keys()->merge($userPairs->keys())->unique()->sort()->values();

    // Labels hiển thị dạng mm/YY (vd: 10/25)
    $labelsPretty = $allMonths->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('m/y'));

    // Dữ liệu map theo labels (thiếu thì 0)
    $attemptSeries = $allMonths->map(fn($m) => (int)($attemptPairs[$m] ?? 0));
    $userSeries    = $allMonths->map(fn($m) => (int)($userPairs[$m] ?? 0));

    // Top exams
    $topExamLabels = collect($topExams)->pluck('title')->values();
    $topExamCounts = collect($topExams)->pluck('attempts_count')->map(fn($v)=> (int)$v)->values();

    // Attempts theo type
    $byType = collect($attemptsByType)->keyBy('type');
    $typeNangLuc = (int)optional($byType->get('nang_luc'))->total ?? 0;
    $typeTuDuy   = (int)optional($byType->get('tu_duy'))->total ?? 0;

    // Top subscriptions
    $subLabels = collect($topSubscriptions)
        ->map(fn($s) => optional($s->subscriptionPlan)->name ?? ('Gói #' . $s->plan_id))
        ->values();
    $subTotals = collect($topSubscriptions)->pluck('total')->map(fn($v)=> (int)$v)->values();
@endphp

<div class="min-h-screen bg-gray-100 p-6 font-sans"> <!-- Nền xám nhạt hiện đại -->
    <div class="max-w-[1600px] mx-auto space-y-8">

        {{-- Header + Filter --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-black mb-2"> <!-- Text đen cho tiêu đề -->
                    Thống kê hệ thống
                </h1>
                <p class="text-gray-600 text-sm font-medium flex items-center gap-2"> <!-- Text xám phụ -->
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Theo dõi hệ thống thời gian thực
                </p>
            </div>

            <form class="flex items-center gap-3 bg-white p-3 rounded-2xl shadow-lg border border-gray-200" method="GET">
                <div class="flex items-center gap-3 text-sm">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-black" fill="black" viewBox="0 0 24 24"> <!-- Icon đen -->
                            <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm6 3a5 5 0 015 5c0 .7-.15 1.37-.42 2l2.2 2.2-1.42 1.42-2.2-2.2a5 5 0 01-6.16 0l-2.2 2.2-1.42-1.42 2.2-2.2A5 5 0 017 12a5 5 0 015-5zm0 2a3 3 0 100 6 3 3 0 000-6z"/>
                        </svg>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                               class="pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                    <div class="w-3 h-px bg-gray-300"></div>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-black" fill="black" viewBox="0 0 24 24"> <!-- Icon đen -->
                            <path d="M6 2h12a2 2 0 012 2v16a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v16h12V4H6zm6 3a5 5 0 015 5c0 .7-.15 1.37-.42 2l2.2 2.2-1.42 1.42-2.2-2.2a5 5 0 01-6.16 0l-2.2 2.2-1.42-1.42 2.2-2.2A5 5 0 017 12a5 5 0 015-5zm0 2a3 3 0 100 6 3 3 0 000-6z"/>
                        </svg>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                               class="pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                </div>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="white" viewBox="0 0 24 24"> <!-- Icon trắng trong nút -->
                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                    </svg>
                    Lọc Dữ Liệu
                </button>
            </form>
        </div>

        {{-- 4 Stats Card --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $cards = [
                    [
                        'icon'=>'users',
                        'label'=>'Tổng Người Dùng',
                        'value'=>$stats['users']??0,
                        'gradient'=>'from-purple-500 to-purple-600',
                        'glow'=>'blue',
                        'pattern'=>'users'
                    ],
                    [
                        'icon'=>'file-text',
                        'label'=>'Tổng Bài Thi',
                        'value'=>$stats['exams']??0,
                        'gradient'=>'from-purple-500 to-purple-600',
                        'glow'=>'green',
                        'pattern'=>'exams'
                    ],
                    [
                        'icon'=>'help-circle',
                        'label'=>'Tổng Câu Hỏi',
                        'value'=>$stats['questions']??0,
                        'gradient'=>'from-purple-500 to-purple-600',
                        'glow'=>'orange',
                        'pattern'=>'questions'
                    ],
                    [
                        'icon'=>'activity',
                        'label'=>'Lượt Thi',
                        'value'=>$stats['attempts']??0,
                        'gradient'=>'from-purple-500 to-purple-600',
                        'glow'=>'purple',
                        'pattern'=>'attempts'
                    ],
                ];
            @endphp

            @foreach($cards as $c)
            <div class="group relative">
                <div class="relative bg-white p-6 rounded-2xl shadow-lg border border-gray-200 transition-all duration-500 hover:scale-[1.02] hover:-translate-y-1 overflow-hidden">
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-14 h-14 flex items-center justify-center rounded-2xl bg-gradient-to-br {{ $c['gradient'] }} shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="white" data-feather="{{ $c['icon'] }}"></svg> <!-- Icon trắng trong gradient -->
                                </div>
                                <div class="text-sm font-semibold text-black uppercase tracking-wider">{{ $c['label'] }}</div> <!-- Text đen -->
                            </div>

                            <div class="text-4xl font-black text-black counter" data-count="{{ $c['value'] }}">0</div> <!-- Text đen -->
                        </div>

                        <div class="hidden lg:block w-20 h-16 opacity-30">
                            <svg viewBox="0 0 80 64" class="w-full h-full">
                                <path d="M0 32 Q 20 16, 40 24 T 80 16" stroke="currentColor" stroke-width="2" fill="none" class="text-{{ $c['glow'] }}-500"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Revenue Reports Tablist --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-8 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="white" data-feather="dollar-sign"></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">Báo Cáo Doanh Thu</h2>
                        <p class="text-xs text-gray-600 font-medium">Thống kê doanh thu từ đăng ký gói</p>
                    </div>
                </div>
            </div>

            <div class="p-8" x-data="{ activeTab: 'daily' }">
                {{-- Tab Navigation --}}
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl mb-6">
                    <button @click="activeTab = 'daily'" 
                            :class="{'bg-white shadow-lg text-blue-600': activeTab === 'daily'}"
                            class="flex-1 py-2 px-4 text-sm font-semibold rounded-lg transition-all duration-200">
                        Hôm Nay
                    </button>
                    <button @click="activeTab = 'weekly'"
                            :class="{'bg-white shadow-lg text-blue-600': activeTab === 'weekly'}"
                            class="flex-1 py-2 px-4 text-sm font-semibold rounded-lg transition-all duration-200">
                        Tuần Này
                    </button>
                    <button @click="activeTab = 'monthly'"
                            :class="{'bg-white shadow-lg text-blue-600': activeTab === 'monthly'}"
                            class="flex-1 py-2 px-4 text-sm font-semibold rounded-lg transition-all duration-200">
                        Tháng Này
                    </button>
                    <button @click="activeTab = 'yearly'"
                            :class="{'bg-white shadow-lg text-blue-600': activeTab === 'yearly'}"
                            class="flex-1 py-2 px-4 text-sm font-semibold rounded-lg transition-all duration-200">
                        Năm Nay
                    </button>
                </div>

                {{-- Tab Content --}}
                <div>
                    {{-- Daily Report --}}
                    <div x-show="activeTab === 'daily'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Doanh Thu Hôm Nay</h3>
                                    <span class="text-xs font-medium {{ $revenueStats['today']['growth'] >= 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full">
                                        {{ $revenueStats['today']['growth'] >= 0 ? '+' : '' }}{{ number_format($revenueStats['today']['growth'], 1) }}%
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ number_format($revenueStats['today']['revenue'], 0, ',', '.') }} ₫</div>
                                <div class="mt-2 text-xs text-gray-500">So với hôm qua</div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Đăng Ký Mới</h3>
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                        @php
                                            $subs = is_string($revenueStats['today']['subscriptions'])
                                                ? json_decode($revenueStats['today']['subscriptions'], true)
                                                : $revenueStats['today']['subscriptions'];
                                        @endphp

                                        +{{ !empty($subs) ? number_format($subs[0]['price'], 0, ',', '.') . ' ₫' : '0 ₫' }}
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ !empty($subs) ? number_format($subs[0]['price'], 0, ',', '.') . ' ₫' : '0 ₫' }}</div>
                                <div class="mt-2 text-xs text-gray-500">Gói đăng ký mới hôm nay</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Chi Tiết Đăng Ký Hôm Nay</h3>
                                <div class="h-[300px]">
                                    <canvas id="dailySubscriptionsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Weekly Report --}}
                    <div x-show="activeTab === 'weekly'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Doanh Thu Tuần Này</h3>
                                    <span class="text-xs font-medium {{ $revenueStats['weekly']['growth'] >= 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full">
                                        {{ $revenueStats['weekly']['growth'] >= 0 ? '+' : '' }}{{ number_format($revenueStats['weekly']['growth'], 1) }}%
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ number_format($revenueStats['weekly']['revenue'], 0, ',', '.') }} ₫</div>
                                <div class="mt-2 text-xs text-gray-500">So với tuần trước</div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Tổng Đăng Ký</h3>
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                        +{{ $revenueStats['weekly']['subscriptions'] }}
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ $revenueStats['weekly']['subscriptions'] }}</div>
                                <div class="mt-2 text-xs text-gray-500">Gói đăng ký tuần này</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Phân Tích Theo Ngày</h3>
                                <div class="h-[300px]">
                                    <canvas id="weeklySubscriptionsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Monthly Report --}}
                    <div x-show="activeTab === 'monthly'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Doanh Thu Tháng</h3>
                                    <span class="text-xs font-medium {{ $revenueStats['monthly']['growth'] >= 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full">
                                        {{ $revenueStats['monthly']['growth'] >= 0 ? '+' : '' }}{{ number_format($revenueStats['monthly']['growth'], 1) }}%
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ number_format($revenueStats['monthly']['revenue'], 0, ',', '.') }} ₫</div>
                                <div class="mt-2 text-xs text-gray-500">So với tháng trước</div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Lượt Đăng Ký</h3>
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                        +{{ $revenueStats['monthly']['subscriptions'] }}
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ $revenueStats['monthly']['subscriptions'] }}</div>
                                <div class="mt-2 text-xs text-gray-500">Gói đăng ký tháng này</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Xu Hướng Tháng</h3>
                                <div class="h-[300px]">
                                    <canvas id="monthlySubscriptionsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Yearly Report --}}
                    <div x-show="activeTab === 'yearly'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Doanh Thu Năm</h3>
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                        {{ number_format($revenueStats['yearly']['revenue'], 0, ',', '.') }} ₫
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ number_format($revenueStats['yearly']['revenue'], 0, ',', '.') }} ₫</div>
                                <div class="mt-2 text-xs text-gray-500">Tổng doanh thu năm {{ date('Y') }}</div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-600">Tổng Đăng Ký</h3>
                                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                        +{{ $revenueStats['yearly']['subscriptions'] }}
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-black">{{ number_format($revenueStats['yearly']['subscriptions']) }}</div>
                                <div class="mt-2 text-xs text-gray-500">Gói đăng ký năm {{ date('Y') }}</div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Phân Tích Theo Quý</h3>
                                <div class="h-[300px]">
                                    <canvas id="yearlySubscriptionsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Monthly Stats --}}
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="white" data-feather="trending-up"></svg> <!-- Icon trắng -->
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-black">Xu Hướng Hàng Tháng</h2> <!-- Text đen -->
                            <p class="text-xs text-gray-600 font-medium">Phân tích tăng trưởng</p> <!-- Text xám phụ -->
                        </div>
                    </div>
                    <div class="flex gap-4 text-xs font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-black">Lượt Thi</span> <!-- Text đen -->
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-black">Người Dùng Mới</span> <!-- Text đen -->
                        </div>
                    </div>
                </div>
                <div class="h-80"><canvas id="monthlyStatsChart"></canvas></div>
            </div>

            {{-- Top Exams --}}
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="white" data-feather="award"></svg> <!-- Icon trắng -->
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">Top 5 Bài Thi</h2> <!-- Text đen -->
                        <p class="text-xs text-gray-600 font-medium">Bài thi phổ biến nhất</p> <!-- Text xám phụ -->
                    </div>
                </div>
                <div class="h-80"><canvas id="topExamsChart"></canvas></div>
            </div>

            {{-- Exam Types --}}
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="white" data-feather="pie-chart"></svg> <!-- Icon trắng -->
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">Phân Loại Bài Thi</h2> <!-- Text đen -->
                        <p class="text-xs text-gray-600 font-medium">Theo loại bài thi</p> <!-- Text xám phụ -->
                    </div>
                </div>
                <div class="h-80"><canvas id="examTypesChart"></canvas></div>
            </div>

            {{-- Top Subscriptions --}}
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="white" data-feather="package"></svg> <!-- Icon trắng -->
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">Gói Đăng Ký</h2> <!-- Text đen -->
                        <p class="text-xs text-gray-600 font-medium">Gói phổ biến</p> <!-- Text xám phụ -->
                    </div>
                </div>
                <div class="h-80"><canvas id="topSubscriptionsChart"></canvas></div>
            </div>
        </div>

        {{-- Top Users --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 p-8 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="white" data-feather="users"></svg> <!-- Icon trắng -->
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">Người Dùng Năng Động Nhất</h2> <!-- Text đen -->
                        <p class="text-xs text-gray-600 font-medium">Những người tham gia tích cực</p> <!-- Text xám phụ -->
                    </div>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    @forelse($topUsers as $index => $user)
                        <div class="group relative">
                            @if($index < 3)
                            <div class="absolute -top-2 -right-2 z-10 w-8 h-8 flex items-center justify-center rounded-full 
                                {{ $index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600' : ($index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500' : 'bg-gradient-to-br from-green-500 to-amber-700') }} 
                                shadow-lg text-white text-xs font-black">
                                #{{ $index + 1 }}
                            </div>
                            @endif

                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200 hover:border-blue-300 hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                                <div class="relative inline-block mb-4">
                                    <div class="w-20 h-20 rounded-full overflow-hidden ring-4 ring-white shadow-lg">
                                        <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="white" data-feather="check"></svg>
                                    </div>
                                </div>
                                
                                <h3 class="font-bold text-black mb-1 truncate">{{ $user->name }}</h3>
                                
                                <div class="flex items-center justify-center gap-1 text-sm font-semibold text-blue-600 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" data-feather="activity"></svg>
                                    <span>{{ $user->exam_attempts_count }}</span>
                                </div>

                                @if($user->subscriptions->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-semibold rounded-full">
                                        <svg class="w-3 h-3 text-white" fill="white" data-feather="award"></svg>
                                        {{ optional($user->subscriptions->first()->subscriptionPlan)->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-black" fill="black" data-feather="inbox"></svg>
                            </div>
                            <p class="text-gray-600 font-medium">Không có dữ liệu</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"
        integrity="sha512-lwqX3m3TS5GvMEs6apYwfrzqD4YzR8QOXK7u0CNsv54WccBFlS2Tk8U5ZxkFfT7MQrLKUO6dtrJxHGr7A7sjUQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    feather.replace();
});

// Counter animation
document.querySelectorAll('.counter').forEach(function(el) {
    const count = Number(el.getAttribute('data-count') || 0);
    let n = 0;
    const frames = 80;
    const step = Math.max(1, Math.ceil(count / frames));
    const itv = setInterval(() => {
        n += step;
        if (n >= count) { el.textContent = count.toLocaleString('vi-VN'); clearInterval(itv); }
        else { el.textContent = n.toLocaleString('vi-VN'); }
    }, 16);
});

// Chart data
const revenueData = @json($revenueStats);
const labelsMonthly   = @json($labelsPretty);
const seriesAttempts  = @json($attemptSeries);
const seriesUsers     = @json($userSeries);
const topExamLabels   = @json($topExamLabels);
const topExamCounts   = @json($topExamCounts);
const donutData       = @json([$typeNangLuc, $typeTuDuy]);
const subLabels       = @json($subLabels);
const subTotals       = @json($subTotals);

// Chart.js configuration
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
Chart.defaults.plugins.legend.position = 'bottom';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 15;
Chart.defaults.plugins.legend.labels.font = {
    size: 12,
    weight: '600'
};

// Revenue Charts
const dailySubscriptionsChart = new Chart(document.getElementById('dailySubscriptionsChart'), {
    type: 'line',
    data: {
        labels: Array.from({length: 24}, (_, i) => `${i.toString().padStart(2, '0')}:00`),
        datasets: [{
            label: 'Doanh Thu',
            data: revenueData.today.hourly_data,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            }
        }
    }
});

const weeklySubscriptionsChart = new Chart(document.getElementById('weeklySubscriptionsChart'), {
    type: 'bar',
    data: {
        labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
        datasets: [{
            label: 'Doanh Thu',
            data: Object.values(revenueData.weekly.daily_data),
            backgroundColor: '#8B5CF6',
            borderRadius: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            }
        }
    }
});

const monthlySubscriptionsChart = new Chart(document.getElementById('monthlySubscriptionsChart'), {
    type: 'line',
    data: {
        labels: Array.from({length: 30}, (_, i) => i + 1),
        datasets: [{
            label: 'Doanh Thu',
            data: Object.values(revenueData.monthly.daily_data),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            },
            x: {
                ticks: {
                    callback: function(value) {
                        return 'Ngày ' + (value + 1);
                    }
                }
            }
        }
    }
});

const yearlySubscriptionsChart = new Chart(document.getElementById('yearlySubscriptionsChart'), {
    type: 'bar',
    data: {
        labels: ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'],
        datasets: [{
            label: 'Doanh Thu',
            data: Object.values(revenueData.yearly.quarterly_data),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ],
            borderRadius: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            }
        }
    }
});

// Monthly Stats Chart
new Chart(document.getElementById('monthlyStatsChart'), {
    type: 'line',
    data: {
        labels: labelsMonthly,
        datasets: [
            {
                label: 'Lượt Thi',
                data: seriesAttempts,
                borderColor: '#3B82F6', // Xanh dương công nghệ
                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBorderWidth: 3
            },
            {
                label: 'Người Dùng Mới',
                data: seriesUsers,
                borderColor: '#8B5CF6', // Tím công nghệ
                backgroundColor: 'rgba(139, 92, 246, 0.08)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#8B5CF6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBorderWidth: 3
            }
        ]
    },
    options: {
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0, font: { size: 11, weight: '600' }, color: '#000000' }, // Text đen
                grid: { color: 'rgba(0, 0, 0, 0.04)' }
            },
            x: {
                ticks: { font: { size: 11, weight: '600' }, color: '#000000' }, // Text đen
                grid: { display: false }
            }
        },
        plugins: {
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 }
            }
        }
    }
});

// Top Exams Chart
new Chart(document.getElementById('topExamsChart'), {
    type: 'bar',
    data: {
        // Đảm bảo luôn có 5 cột bằng cách thêm nhãn trống
        labels: [...topExamLabels, ...Array(Math.max(0, 5 - topExamLabels.length)).fill('')],
        datasets: [{
            label: 'Lượt Thi',
            // Thêm giá trị null cho các cột trống
            data: [...topExamCounts, ...Array(Math.max(0, 5 - topExamCounts.length)).fill(null)],
            backgroundColor: [
                'rgba(59, 130, 246, 0.9)',   // Xanh dương
                'rgba(16, 185, 129, 0.9)',   // Xanh lá
                'rgba(245, 158, 11, 0.9)',   // Cam
                'rgba(139, 92, 246, 0.9)',   // Tím
                'rgba(236, 72, 153, 0.9)'    // Hồng
            ],
            hoverBackgroundColor: [
                'rgb(29, 78, 216)',      // Xanh dương đậm
                'rgb(6, 95, 70)',        // Xanh lá đậm
                'rgb(180, 83, 9)',       // Cam đậm
                'rgb(109, 40, 217)',     // Tím đậm
                'rgb(190, 24, 93)'       // Hồng đậm
            ],
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: 0.5,
            categoryPercentage: 0.7
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    precision: 0, 
                    font: { size: 11, weight: '600' }, 
                    color: '#000000',
                    maxTicksLimit: 8
                },
                grid: { 
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: false
                }
            },
            x: {
                min: 0,
                max: 4, // Luôn hiển thị đủ không gian cho 5 cột
                ticks: { 
                    font: { size: 11, weight: '600' }, 
                    color: '#000000',
                    maxRotation: 45,
                    minRotation: 45,
                    callback: function(value) {
                        // Chỉ hiển thị nhãn cho các cột có dữ liệu
                        return topExamLabels[value] || '';
                    }
                },
                grid: { display: false }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return `${context.parsed.y} lượt thi`;
                    }
                }
            }
        }
    }
});

// Exam Types Chart
console.log('Donut Data:', donutData); // Để debug

const examTypesChart = new Chart(document.getElementById('examTypesChart'), {
    type: 'doughnut',
    data: {
        labels: ['Năng Lực', 'Tư Duy'],
        datasets: [{
            data: [3, 3], // Gán trực tiếp giá trị để test
            backgroundColor: [
                '#3B82F6', // Xanh dương
                '#F59E0B'  // Cam
            ],
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 8,
            hoverBorderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        radius: '90%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: { size: 13, weight: '600' },
                    color: '#000000',
                    usePointStyle: true,
                    generateLabels: (chart) => {
                        const data = chart.data;
                        return data.labels.map((label, i) => ({
                            text: `${label} (${data.datasets[0].data[i]})`,
                            fillStyle: data.datasets[0].backgroundColor[i],
                            strokeStyle: data.datasets[0].backgroundColor[i],
                            pointStyle: 'circle',
                        }));
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.formattedValue;
                        return `${label}: ${value} lượt thi`;
                    }
                }
            }
        }
    }
});

// Top Subscriptions Chart
new Chart(document.getElementById('topSubscriptionsChart'), {
    type: 'bar',
    data: {
        labels: [...subLabels, ...Array(Math.max(0, 5 - subLabels.length)).fill('')],
        datasets: [{
            label: 'Gói Đăng Ký',
            data: [...subTotals, ...Array(Math.max(0, 5 - subTotals.length)).fill(null)],
            backgroundColor: [
                'rgba(236, 72, 153, 0.9)',   // Hồng
                'rgba(139, 92, 246, 0.9)',   // Tím
                'rgba(59, 130, 246, 0.9)',   // Xanh dương
                'rgba(16, 185, 129, 0.9)',   // Xanh lá
                'rgba(245, 158, 11, 0.9)'    // Cam
            ],
            hoverBackgroundColor: [
                'rgb(190, 24, 93)',      // Hồng đậm
                'rgb(109, 40, 217)',     // Tím đậm
                'rgb(29, 78, 216)',      // Xanh dương đậm
                'rgb(6, 95, 70)',        // Xanh lá đậm
                'rgb(180, 83, 9)'        // Cam đậm
            ],
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: 0.5,
            categoryPercentage: 0.7
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { 
                    precision: 0, 
                    font: { size: 11, weight: '600' }, 
                    color: '#000000',
                    maxTicksLimit: 8
                },
                grid: { 
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: false
                }
            },
            x: {
                min: 0,
                max: 4, // Luôn hiển thị đủ không gian cho 5 cột
                ticks: { 
                    font: { size: 11, weight: '600' }, 
                    color: '#000000',
                    maxRotation: 45,
                    minRotation: 45,
                    callback: function(value) {
                        return subLabels[value] || '';
                    }
                },
                grid: { display: false }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return `${context.parsed.y} lượt đăng ký`;
                    }
                }
            }
        }
    }
});
</script>
@endpush