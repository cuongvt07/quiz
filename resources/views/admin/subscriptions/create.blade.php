@extends('layouts.admin')

@push('styles')
<!-- Select2 CSS local -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<style>
    /* Minimal style: Chỉ cơ bản để tránh overlap và fit width */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #333;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
        right: 8px;
    }
    /* Đảm bảo search box không bị ẩn */
    .select2-search--dropdown {
        display: block !important;
    }
    .select2-search__field {
        width: 100% !important;
        padding: 4px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        box-sizing: border-box;
    }
    /* Ẩn native select để tránh chồng chéo */
    select {
        opacity: 0;
        position: absolute;
        z-index: -1;
    }
</style>
@endpush

@extends('layouts.admin')

@section('content')
@php
$plansJson = json_encode($plans->mapWithKeys(function($plan) {
    return [$plan->id => [
        'duration_days' => $plan->duration_days,
        'name' => $plan->name
    ]];
}));
@endphp
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-3">
            <i data-feather="credit-card" class="text-blue-600 w-7 h-7"></i>
            <span>Đăng ký gói nâng cấp</span>
        </h1>
        <a href="{{ route('admin.subscriptions.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:text-blue-600 hover:border-blue-400 transition">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Quay lại
        </a>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-2">
            <i data-feather="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
        <form action="{{ route('admin.subscriptions.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Nhóm 1: Thông tin người dùng --}}
            <fieldset class="border border-gray-200 rounded-lg p-6">
                <legend class="px-3 text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin người dùng</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    {{-- Thành viên --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Thành viên <span class="text-red-500">*</span>
                        </label>
                        <select id="select-user" name="user_id" required class="w-full text-sm">
                            <option value="">-- Chọn hoặc tìm thành viên --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" data-search="{{ $user->email }}"
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gói nâng cấp --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gói nâng cấp <span class="text-red-500">*</span>
                        </label>
                        <select id="select-plan" name="plan_id" required class="w-full text-sm">
                            <option value="">-- Chọn gói nâng cấp --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" data-search="{{ $plan->description }}"
                                    {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} — {{ number_format($plan->price) }}đ ({{ $plan->attempts }} lượt)
                                </option>
                            @endforeach
                        </select>
                        @error('plan_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Nhóm 2: Thời gian áp dụng --}}
            <fieldset class="border border-gray-200 rounded-lg p-6">
                <legend class="px-3 text-sm font-semibold text-gray-600 uppercase tracking-wide">Thời gian áp dụng</legend>

                <input type="hidden" name="start_date" id="start_date" value="{{ now()->format('Y-m-d') }}">
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày kết thúc <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                               class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm px-3 py-2" readonly>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Hành động --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    <i data-feather="x" class="w-4 h-4"></i> Hủy
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    <i data-feather="save" class="w-4 h-4"></i> Lưu đăng ký
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Nạp Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    // Debug: Check load thành công
    console.log('jQuery version:', $.fn.jquery);  // Phải log 3.6.0
    console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');  // Phải log true

    if (window.feather) feather.replace();

    // Chỉ init nếu Select2 sẵn sàng
    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 not loaded! Check file paths.');
        return;
    }

    function customMatcher(params, data) {
        if ($.trim(params.term) === '') return data;
        const term = params.term.toLowerCase();
        const combinedText = (data.text || '') + ' ' + ($(data.element).data('search') || '');
        return combinedText.toLowerCase().includes(term) ? data : null;
    }

    // Init với force search box
    $('#select-user').select2({
        placeholder: 'Chọn hoặc tìm thành viên...',
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 0,
        matcher: customMatcher,
        language: {
            searching: function () { return 'Đang tìm...'; },
            noResults: function () { return 'Không tìm thấy kết quả.'; },
            searchPlaceholder: 'Tìm theo tên hoặc email...'
        }
    });
    console.log('User Select2 init OK!');

    $('#select-plan').select2({
        placeholder: 'Chọn gói nâng cấp...',
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: 0,
        matcher: customMatcher,
        language: {
            searching: function () { return 'Đang tìm...'; },
            noResults: function () { return 'Không tìm thấy kết quả.'; },
            searchPlaceholder: 'Tìm theo tên hoặc mô tả...'
        }
    });
    console.log('Plan Select2 init OK!');

    // Force focus vào input search khi mở
    $('#select-user, #select-plan').on('select2:open', function () {
        setTimeout(function() {
            $('.select2-search__field').focus();
        }, 50);
    });

    // Khởi tạo dữ liệu plans
    const plansData = JSON.parse('{!! $plansJson !!}');

    // Lắng nghe sự kiện thay đổi gói đăng ký
    $('#select-plan').on('change', function() {
        const planId = $(this).val();
        const selectedPlan = plansData[planId];

        if (selectedPlan) {
            // Lấy ngày hiện tại từ trường ẩn
            const startDate = new Date($('#start_date').val());
            
            // Tính ngày kết thúc bằng cách cộng duration_days
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + selectedPlan.duration_days);
            
            // Format ngày kết thúc thành YYYY-MM-DD
            const formattedEndDate = endDate.toISOString().split('T')[0];
            
            // Cập nhật trường ngày kết thúc
            $('#end_date').val(formattedEndDate);
        } else {
            $('#end_date').val('');
        }
    });
});
</script>