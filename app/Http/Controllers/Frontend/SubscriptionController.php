<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('id', 'desc')->get();
        return view('frontend.subscriptions.index', compact('plans'));
    }

    public function show(SubscriptionPlan $plan)
    {
        return response()->json([
            'plan' => $plan,
            'qrCode' => $this->generateQRCode($plan)
        ]);
    }

    private function generateQRCode($plan)
    {
        // Thông tin tài khoản ngân hàng
        $bankInfo = [
            'bankBin' => 'tpbank', // TPBank
            'bankNumber' => '01877146501',
            'amount' => $plan->price,
            'purpose' => "Nang cap {$plan->name}"
        ];

        return [
            'bankInfo' => $bankInfo,
            'amount' => number_format($plan->price) . ' VNĐ',
            'accountName' => 'NGUYEN VAN A', // Tên chủ tài khoản
            'bankName' => 'TPBank'
        ];
    }
}