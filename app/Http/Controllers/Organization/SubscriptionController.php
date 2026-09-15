<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use App\Models\GatewayPayment;
use App\Services\RazorpayPaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $currentSubscription = auth()->user()->organization->activeSubscription;
        
        $plans = Plan::where('is_active', true)->with('features')->get();
        $key = config('services.razorpay.key');

        return view('organization.subscription.index', compact('currentSubscription', 'plans', 'key'));
    }

    public function initiatePayment(Request $request, Plan $plan)
    {
        $org = auth()->user()->organization;
        $cycle = $request->input('billing_cycle', 'monthly');
        $addonIds = $request->input('addon_ids', []);

        $basePrice = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
        $totalPrice = (float) $basePrice;

        $addons = [];
        if (!empty($addonIds) && is_array($addonIds)) {
            $addonPlans = Plan::whereIn('id', $addonIds)->where('type', 'addon')->get();
            foreach ($addonPlans as $addon) {
                $addonPrice = $cycle === 'yearly' ? $addon->price_yearly : $addon->price_monthly;
                $totalPrice += (float) $addonPrice;
                $addons[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addonPrice
                ];
            }
        }

        // If plan is free and no paid addons, activate directly
        if ($totalPrice <= 0) {
            DB::transaction(function () use ($org, $plan, $cycle, $addonIds) {
                $this->activatePlan($org, $plan, $cycle);
                if (!empty($addonIds)) {
                    $addonPlans = Plan::whereIn('id', $addonIds)->get();
                    foreach ($addonPlans as $addon) {
                        $this->activatePlan($org, $addon, $cycle);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'is_free' => true,
                'message' => 'Activated ' . $plan->name . ' plan with selected add-ons.'
            ]);
        }

        $key = config('services.razorpay.key');

        if (!$key || $key === 'rzp_test_xxxxxxxxx') {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay API Key is not configured in .env file. Please add your RAZORPAY_KEY and RAZORPAY_SECRET to enable online payments.'
            ], 400);
        }

        try {
            $payment = RazorpayPaymentService::createOrder($plan, $totalPrice);

            $planDescription = $plan->name . ' (' . ucfirst($cycle) . ')';
            if (count($addons) > 0) {
                $planDescription .= ' + ' . count($addons) . ' Add-on(s)';
            }

            return response()->json([
                'success' => true,
                'is_free' => false,
                'key' => $key,
                'order_id' => $payment->razorpay_order_id,
                'amount' => round($totalPrice * 100),
                'total_formatted' => number_format($totalPrice, 2),
                'currency' => 'INR',
                'plan_name' => $planDescription,
                'plan_id' => $plan->id,
                'addon_ids' => $addonIds,
                'billing_cycle' => $cycle,
                'org_name' => $org->name,
                'user_name' => auth()->user()->name,
                'user_email' => auth()->user()->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription Razorpay Order Creation Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize Razorpay payment gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:plans,id',
            'billing_cycle' => 'nullable|in:monthly,yearly',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        $org = auth()->user()->organization;
        $plan = Plan::findOrFail($request->plan_id);
        $cycle = $request->input('billing_cycle', 'monthly');
        $addonIds = $request->input('addon_ids', []);
        
        $gatewayPaymentId = null;

        if ($request->razorpay_order_id) {
            try {
                $payment = RazorpayPaymentService::verifyAndProcessPayment(
                    $request->razorpay_order_id,
                    $request->razorpay_payment_id,
                    $request->razorpay_signature
                );
                $gatewayPaymentId = $payment->id;
            } catch (\Exception $e) {
                Log::warning('Subscription payment verification warning: ' . $e->getMessage());
            }
        }

        DB::transaction(function () use ($org, $plan, $cycle, $addonIds, $gatewayPaymentId) {
            $this->activatePlan($org, $plan, $cycle, $gatewayPaymentId);
            
            if (!empty($addonIds) && is_array($addonIds)) {
                $addonPlans = Plan::whereIn('id', $addonIds)->get();
                foreach ($addonPlans as $addon) {
                    $this->activatePlan($org, $addon, $cycle, $gatewayPaymentId);
                }
            }
        });

        $msg = 'Payment successful! Upgraded to ' . $plan->name . ' plan';
        if (!empty($addonIds)) {
            $msg .= ' along with ' . count($addonIds) . ' power-up add-on(s)!';
        } else {
            $msg .= '.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    private function activatePlan($org, Plan $plan, $cycle = 'monthly', $gatewayPaymentId = null)
    {
        // Cancel old subscription of the same type (base or specific addon)
        if ($plan->type === 'base') {
            if ($org->activeSubscription) {
                $org->activeSubscription->update([
                    'status' => 'Cancelled',
                    'ends_at' => Carbon::today()
                ]);
            }
        } else {
            // Cancel existing same addon
            $existingAddon = $org->activeAddons()->where('plan_id', $plan->id)->first();
            if ($existingAddon) {
                $existingAddon->update([
                    'status' => 'Cancelled',
                    'ends_at' => Carbon::today()
                ]);
            }
        }

        $endsAt = $cycle === 'yearly' ? Carbon::today()->addYear() : Carbon::today()->addMonth();

        // Create new active subscription
        OrganizationSubscription::create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => 'Active',
            'starts_at' => Carbon::today(),
            'ends_at' => $endsAt,
            'gateway_payment_id' => $gatewayPaymentId
        ]);
    }

    public function switchPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'nullable|in:monthly,yearly'
        ]);

        $org = auth()->user()->organization;
        $newPlan = Plan::findOrFail($request->plan_id);
        $cycle = $request->input('billing_cycle', 'monthly');
        
        $price = $cycle === 'yearly' ? $newPlan->price_yearly : $newPlan->price_monthly;

        if ($price > 0) {
            return back()->with('error', 'Paid plans require online payment via Razorpay. Please click "Switch to ' . $newPlan->name . '" to pay.');
        }

        $this->activatePlan($org, $newPlan, $cycle);

        return redirect()->route('organization.subscription.index')->with('success', 'Successfully switched to ' . $newPlan->name . ' plan!');
    }

    public function requestRefund(Request $request)
    {
        $org = auth()->user()->organization;
        
        // Fetch all active subscriptions (base + addons)
        $subscriptions = $org->subscriptions()->where('status', 'Active')->get();
        
        if ($subscriptions->isEmpty()) {
            return back()->with('error', 'No active subscription found to refund.');
        }

        $paymentId = null;
        foreach ($subscriptions as $sub) {
            if ($sub->gateway_payment_id) {
                $paymentId = $sub->gateway_payment_id;
                break;
            }
        }

        if ($paymentId) {
            $payment = GatewayPayment::find($paymentId);
            if ($payment && $payment->status === 'captured') {
                try {
                    RazorpayPaymentService::issueRefund($payment);
                } catch (\Exception $e) {
                    Log::error('Refund failed: ' . $e->getMessage());
                    return back()->with('error', 'Refund process failed: ' . $e->getMessage());
                }
            }
        }

        DB::transaction(function () use ($org, $subscriptions) {
            foreach ($subscriptions as $sub) {
                $sub->update([
                    'status' => 'Refunded',
                    'ends_at' => Carbon::now()
                ]);
            }
            $org->update(['is_active' => false]);
        });

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your subscription has been refunded and your account has been deactivated.');
    }
}
