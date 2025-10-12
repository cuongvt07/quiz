@extends('layouts.frontend')

@section('title', 'Thông tin người dùng')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-10 flex flex-col md:flex-row gap-8">

        {{-- Cột bên trái: Thông tin cơ bản --}}
        <div class="w-full md:w-1/3 border-r border-gray-200 pr-6">
            <div class="flex flex-col items-center text-center">
                <img src="{{ $user->profile_photo_url }}" alt="Avatar"
                    class="w-24 h-24 rounded-full object-cover mb-4 shadow">
                <div class="text-xl font-semibold text-gray-900">{{ $user->name }}</div>
                <div class="text-gray-500 text-sm mb-2">{{ $user->email }}</div>

                <div class="w-full mt-6 space-y-4 text-left">
                    <div>
                        <div class="text-sm text-gray-500">Lượt thi còn lại</div>
                        <div class="text-lg font-bold text-blue-600">
                            {{ $user->free_slots ?? 'Không giới hạn' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Ngày tạo tài khoản</div>
                        <div class="text-lg font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                <a href="{{ route('logout') }}"
                   class="mt-8 w-full text-center px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition">
                    Đăng xuất
                </a>
            </div>
        </div>

        {{-- Cột bên phải: Tabs nội dung --}}
        <div class="w-full md:w-2/3" x-data="{ tab: localStorage.getItem('userTab') || 'info' }" x-init="$watch('tab', t => localStorage.setItem('userTab', t))">

            {{-- Tab Header --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button class="py-2 px-4 text-sm font-semibold"
                        :class="tab === 'info' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                        @click="tab = 'info'">
                    Thông tin người dùng
                </button>
                <button class="py-2 px-4 text-sm font-semibold"
                        :class="tab === 'attempts' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                        @click="tab = 'attempts'">
                    Lịch sử lượt thi
                </button>
                <button class="py-2 px-4 text-sm font-semibold"
                        :class="tab === 'stats' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                        @click="tab = 'stats'">
                    Thống kê kết quả
                </button>
                <button class="py-2 px-4 text-sm font-semibold"
                        :class="tab === 'subscription' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                        @click="tab = 'subscription'">
                    Gói đăng kí
                </button>
            </div>

            {{-- Tab 1: Thông tin người dùng --}}
            <div x-show="tab === 'info'" class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Họ và tên</div>
                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Email</div>
                    <div class="font-medium text-gray-900">{{ $user->email }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Ngày tạo tài khoản</div>
                    <div class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            {{-- Tab 2: Lịch sử lượt thi --}}
            <div x-show="tab === 'attempts'" x-cloak>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử thi theo từng đề</h2>
                <div class="space-y-6">
                    @forelse($attemptsByExam as $examId => $examAttempts)
                        @php $exam = $examAttempts->first()->exam; @endphp
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="font-semibold text-blue-700 mb-2">{{ $exam->title ?? 'Đề đã xóa' }}</div>
                            <div class="space-y-2">
                                @foreach($examAttempts as $attempt)
                                    <div class="flex justify-between items-center bg-gray-50 rounded-md p-3">
                                        <div>
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attempt->started_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-semibold text-blue-600">{{ $attempt->score }} điểm</span>
                                            <span class="ml-2 text-xs text-gray-500">{{ $attempt->correct_count }}/{{ $exam->total_questions ?? '-' }} câu đúng</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 italic">Chưa có lượt thi nào.</div>
                    @endforelse
                </div>
            </div>

            {{-- Tab 3: Thống kê kết quả --}}
            <div x-show="tab === 'stats'" x-cloak>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thống kê kết quả</h2>
                @php
                    $avgScore = $allAttempts->avg('score') ?? 0;
                    $total = $allAttempts->count();
                    $highest = $allAttempts->max('score') ?? 0;
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-500 mb-1">Trung bình</div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($avgScore, 1) }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-500 mb-1">Cao nhất</div>
                        <div class="text-2xl font-bold text-green-600">{{ $highest }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-500 mb-1">Số lượt thi</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $total }}</div>
                    </div>
                </div>
            </div>

            {{-- Tab 4: Gói đăng kí --}}
            <div x-show="tab === 'subscription'" x-cloak>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Gói đăng kí của bạn</h2>
                @if($subscriptions->isEmpty())
                    <div class="text-sm text-gray-500 italic">Bạn chưa đăng kí gói nào.</div>
                @else
                    <div class="space-y-4">
                        @foreach($subscriptions as $sub)
                            <div class="bg-yellow-50 rounded-lg p-4 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-yellow-800">{{ $sub->plan->name ?? 'Gói đã xóa' }}</div>
                                    <div class="text-xs text-gray-500">Đăng kí ngày: {{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">Hết hạn: {{ \Carbon\Carbon::parse($sub->expires_at)->format('d/m/Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full bg-yellow-200 text-yellow-900 text-xs font-semibold">
                                        {{ $sub->is_active ? 'Đang sử dụng' : 'Đã hết hạn' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
