@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2"><i data-feather="credit-card"></i> Chi tiết Thành viên đăng ký gói</h1>
</div>

        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Thông tin thành viên</h3>
                    <dl class="mt-2 text-sm text-gray-600">
                        <div class="mt-3">
                            <dt class="font-medium">Họ tên:</dt>
                            <dd class="mt-1">{{ $subscription->user->name }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="font-medium">Email:</dt>
                            <dd class="mt-1">{{ $subscription->user->email }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="font-medium">Số lượt thi còn lại:</dt>
                            <dd class="mt-1">{{ $subscription->user->free_slots }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900">Thông tin gói</h3>
                    <dl class="mt-2 text-sm text-gray-600">
                        <div class="mt-3">
                            <dt class="font-medium">Tên gói:</dt>
                            <dd class="mt-1">{{ $subscription->plan->name }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="font-medium">Ngày bắt đầu:</dt>
                            <dd class="mt-1">{{ \Carbon\Carbon::parse($subscription->start_date)->format('d/m/Y') }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="font-medium">Ngày kết thúc:</dt>
                            <dd class="mt-1">{{ \Carbon\Carbon::parse($subscription->end_date)->format('d/m/Y') }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="font-medium">Trạng thái:</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-sm font-semibold rounded-full
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $subscription->status === 'active' ? 'Đang hoạt động' : 'Hết hạn' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.subscriptions.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Quay lại
                </a>
            </div>
        </div>

<script>
if(window.feather) feather.replace();
</script>
@endsection