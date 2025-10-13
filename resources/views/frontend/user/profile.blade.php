@extends('layouts.frontend')

@section('title', 'Thông tin người dùng')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-10 flex flex-col md:flex-row gap-8">

        {{-- Cột bên trái --}}
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

        {{-- Cột phải --}}
        <div class="w-full md:w-2/3" x-data="{ tab: localStorage.getItem('userTab') || 'info' }"
             x-init="$watch('tab', t => localStorage.setItem('userTab', t))">

            {{-- Tabs Header --}}
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

            {{-- TAB 1: Thông tin người dùng --}}
            <div x-show="tab === 'info'" class="space-y-6">
                {{-- Form cập nhật thông tin --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Thông tin cơ bản</h3>
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="bg-white rounded-lg border p-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $user->name) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                                Cập nhật thông tin
                            </button>
                        </div>
                    </form>

                    @if (session('status') === 'profile-updated')
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600">Thông tin đã được cập nhật thành công!</p>
                        </div>
                    @endif
                </div>

                {{-- Form đổi mật khẩu --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Đổi mật khẩu</h3>
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mật khẩu hiện tại
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mật khẩu mới
                            </label>
                            <input type="password" name="password" id="password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Xác nhận mật khẩu mới
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                                Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>

                    @if (session('status') === 'password-updated')
                        <div class="mt-4 p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600">Mật khẩu đã được cập nhật thành công!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB 2: Lịch sử lượt thi --}}
            <div x-show="tab === 'attempts'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Đề thi đã làm</h2>
                    <div class="text-sm text-gray-500">Tổng số: {{ $attemptsByExam->count() }} đề thi</div>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên đề thi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Môn</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lần thi</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lần thi gần nhất</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($attemptsByExam as $examId => $examAttempts)
                                @php 
                                    $exam = $examAttempts->first()->exam;
                                    $lastAttempt = $examAttempts->first();
                                    $bestAttempt = $examAttempts->sortByDesc('score')->first();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $exam->title ?? 'Đề đã xóa' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ optional($exam->subject)->name ?? 'Chưa phân loại' }}</td>
                                    <td class="px-6 py-4 text-center">{{ $examAttempts->count() }}</td>
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ \Carbon\Carbon::parse($lastAttempt->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('exam-history.index', ['exam_id' => $examId]) }}" class="text-blue-600 hover:text-blue-900">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Chưa có lượt thi nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: Thống kê --}}
            <div x-show="tab === 'stats'" x-cloak>
                @php
                    $avgScore = $allAttempts->avg('score') ?? 0;
                    $total = $allAttempts->count();
                    $highest = $allAttempts->max('score') ?? 0;
                @endphp
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thống kê kết quả</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <div class="text-sm text-gray-500 mb-1">Trung bình</div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($avgScore, 1) }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-sm text-gray-500 mb-1">Cao nhất</div>
                        <div class="text-2xl font-bold text-green-600">{{ $highest }}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <div class="text-sm text-gray-500 mb-1">Số lượt thi</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $total }}</div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: Gói đăng kí --}}
            <div x-show="tab === 'subscription'" x-cloak>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Gói đăng kí của bạn</h2>
                @if($subscriptions->isEmpty())
                    <p class="text-sm text-gray-500 italic">Bạn chưa đăng kí gói nào.</p>
                @else
                    <div class="space-y-4">
                        @foreach($subscriptions as $sub)
                            <div class="bg-yellow-50 p-4 rounded-lg flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-yellow-800">{{ $sub->plan->name ?? 'Gói đã xóa' }}</div>
                                    <div class="text-xs text-gray-500">Đăng kí: {{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">Hết hạn: {{ \Carbon\Carbon::parse($sub->expires_at)->format('d/m/Y') }}</div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-yellow-200 text-yellow-900 text-xs font-semibold">
                                    {{ $sub->is_active ? 'Đang sử dụng' : 'Đã hết hạn' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
