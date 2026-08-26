<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationSubscription;
use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = OrganizationSubscription::with(['organization', 'plan'])->latest()->paginate(20);
        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }

    public function edit(OrganizationSubscription $subscription)
    {
        // Need to bypass global scope since we're in Super Admin
        $plans = Plan::where('is_active', true)->get();
        return view('super-admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    public function update(Request $request, OrganizationSubscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:Active,Trial,Expired,Cancelled',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $subscription->update($validated);

        return redirect()->route('super-admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }
}
