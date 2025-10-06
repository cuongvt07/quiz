<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query();
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }
        $plans = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'attempts' => 'required|integer|min:0',
        ]);
        SubscriptionPlan::create($data);
        return redirect()->route('admin.subscription_plans.index')->with('success', 'Tạo gói thành công!');
    }

    public function update(Request $request, SubscriptionPlan $subscription_plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'attempts' => 'required|integer|min:0',
        ]);
        $subscription_plan->update($data);
        return redirect()->route('admin.subscription_plans.index')->with('success', 'Cập nhật gói thành công!');
    }

    public function destroy(SubscriptionPlan $subscription_plan)
    {
        $subscription_plan->delete();
        return redirect()->route('admin.subscription_plans.index')->with('success', 'Đã xoá gói!');
    }
}
