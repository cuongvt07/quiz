@extends('layouts.frontend')

@section('title', 'Gói nâng cấp')

@push('styles')
<style>
    .plan-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .plan-card:hover {
        transform: translateY(-5px);
    }
    .feature-list li {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .feature-list li svg {
        margin-right: 0.5rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Gói nâng cấp tài khoản</h1>
            <p class="text-xl text-gray-600">Nâng cấp tài khoản để mở khóa thêm nhiều tính năng hấp dẫn</p>
        </div>

        <!-- Plans Grid -->
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="plan-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Plan Header -->
                <div class="p-8 bg-gradient-to-br {{ $loop->iteration === 2 ? 'from-blue-600 to-purple-600' : 'from-gray-50 to-gray-100' }}">
                    <!-- Plan Header -->
                    <h3 class="text-2xl font-bold {{ $loop->iteration === 2 ? 'text-white' : 'text-gray-900' }} mb-2">
                        {{ $plan->name }}
                    </h3>
                    
                    <!-- Price -->
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold {{ $loop->iteration === 2 ? 'text-white' : 'text-gray-900' }}">
                            {{ number_format($plan->price, 0, ',', '.') }}
                        </span>
                        <span class="ml-2 {{ $loop->iteration === 2 ? 'text-blue-100' : 'text-gray-500' }}">
                            VNĐ
                        </span>
                    </div>

                    <!-- Duration -->
                    <div class="mt-4 space-y-2">
                        <p class="{{ $loop->iteration === 2 ? 'text-blue-100' : 'text-gray-500' }} flex items-center">
                            <svg class="w-5 h-5 mr-2 {{ $loop->iteration === 2 ? 'text-blue-200' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Thời hạn: {{ number_format($plan->duration_days / 30) }} tháng
                        </p>
                        <p class="{{ $loop->iteration === 2 ? 'text-blue-100' : 'text-gray-500' }} flex items-center">
                            <svg class="w-5 h-5 mr-2 {{ $loop->iteration === 2 ? 'text-blue-200' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Số lượt thi: {{ $plan->attempts }} lượt
                        </p>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="p-8">
                    <!-- Plan Description -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-blue-800">{{ $plan->description }}</p>
                    </div>

                    <!-- Feature List -->
                    <ul class="feature-list space-y-4">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Truy cập tất cả đề thi
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Xem lời giải chi tiết
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Thống kê kết quả chi tiết
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Hỗ trợ ưu tiên 24/7
                        </li>
                    </ul>

                    <!-- Purchase Button -->
                    <button onclick="showPaymentModal({{ $plan->id }})" 
                            class="mt-8 w-full px-6 py-3 border border-blue-600 text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Nâng cấp ngay
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div x-data="{ show: false, plan: null }" 
     x-show="show" 
     x-on:payment-modal.window="show = true; plan = $event.detail"
     class="fixed inset-0 overflow-y-auto z-50"
     x-cloak>
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="show"></div>

    <!-- Modal panel -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-xl transform transition-all"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Close button -->
            <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal content -->
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4" x-text="plan?.name"></h3>
                
                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Số tiền:</span>
                        <span class="font-semibold text-gray-900" x-text="plan?.amount"></span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Ngân hàng:</span>
                        <span class="font-semibold text-gray-900" x-text="plan?.bankName"></span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Số tài khoản:</span>
                        <span class="font-semibold text-gray-900" x-text="plan?.bankInfo.bankNumber"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tên tài khoản:</span>
                        <span class="font-semibold text-gray-900" x-text="plan?.accountName"></span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="flex justify-center mb-6">
                    <div id="qrcode" class="bg-white p-4 rounded-xl shadow-inner"></div>
                </div>

                <!-- Instructions -->
                <div class="text-sm text-gray-500">
                    <p class="mb-2">1. Quét mã QR bằng ứng dụng ngân hàng của bạn</p>
                    <p class="mb-2">2. Kiểm tra thông tin chuyển khoản</p>
                    <p>3. Xác nhận và hoàn tất giao dịch</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vietnam-qr-pay-pure-js@1.0.1/dist/vietnam-qr-pay.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
<script>
function clearQRCode() {
    const qrcodeElement = document.getElementById("qrcode");
    if (qrcodeElement) {
        qrcodeElement.innerHTML = '';
    }
}

async function showPaymentModal(planId) {
    try {
        // Clear previous QR code if exists
        const qrcodeElement = document.getElementById("qrcode");
        qrcodeElement.innerHTML = '';

        const response = await fetch(`/subscriptions/${planId}`);
        const data = await response.json();
        
        if (!data || !data.qrCode || !data.qrCode.bankInfo) {
            throw new Error('Không nhận được thông tin thanh toán hợp lệ');
        }

        // Format data for QR code generation
        const bankInfo = data.qrCode.bankInfo;
        const qrPay = VietnamQRPay.QRPay.initVietQR({
            bankBin: VietnamQRPay.BanksObject[bankInfo.bankBin].bin,
            bankNumber: bankInfo.bankNumber,
            amount: bankInfo.amount.replace(/[^0-9]/g, ''), // Remove non-numeric characters
            purpose: bankInfo.purpose
        });
        
        const qrData = qrPay.build();
        
        // Clear existing QR code
        clearQRCode();
        
        // Generate new QR code
        new QRCode("qrcode", {
            text: qrData,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H || 2
        });
        
        // Format the data for the modal
        const modalData = {
            name: data.plan.name,
            amount: new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
            }).format(data.plan.price),
            bankName: data.qrCode.bankName,
            bankNumber: data.qrCode.bankInfo.bankNumber,
            accountName: data.qrCode.accountName,
            bankInfo: data.qrCode.bankInfo
        };

        // Dispatch event to show modal
        window.dispatchEvent(new CustomEvent('payment-modal', {
            detail: modalData
        }));
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi tạo mã QR: ' + error.message);
    }
}
</script>
@endpush