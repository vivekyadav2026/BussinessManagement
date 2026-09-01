<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use App\Models\GatewayPayment;
use App\Services\RazorpayPaymentService;
use Carbon\Carbon;
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

        // If plan is free, activate directly
        if ($plan->price_monthly <= 0) {
            $this->activatePlan($org, $plan);
            return response()->json([
                'success' => true,
                'is_free' => true,
                'message' => 'Switched to ' . $plan->name . ' plan.'
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
            $payment = RazorpayPaymentService::createOrder($plan, $plan->price_monthly);

            return response()->json([
                'success' => true,
                'is_free' => false,
                'key' => $key,
                'order_id' => $payment->razorpay_order_id,
                'amount' => round($plan->price_monthly * 100),
                'currency' => 'INR',
                'plan_name' => $plan->name,
                'plan_id' => $plan->id,
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
            'razorpay_order_id' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        $org = auth()->user()->organization;
        $plan = Plan::findOrFail($request->plan_id);

        if ($request->razorpay_order_id) {
            $gatewayPayment = GatewayPayment::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($gatewayPayment) {
                $gatewayPayment->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'status' => 'captured',
                ]);
            }
        }

        $this->activatePlan($org, $plan);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful! Upgraded to ' . $plan->name . ' plan.'
        ]);
    }

    private function activatePlan($org, Plan $plan)
    {
        // Cancel old subscription
        if ($org->activeSubscription) {
            $org->activeSubscription->update([
                'status' => 'Cancelled',
                'ends_at' => Carbon::today()
            ]);
        }

        // Create new active subscription
        OrganizationSubscription::create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => 'Active',
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addYear()
        ]);
    }

    public function switchPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $org = auth()->user()->organization;
        $newPlan = Plan::findOrFail($request->plan_id);

        if ($newPlan->price_monthly > 0) {
            return back()->with('error', 'Paid plans require online payment via Razorpay. Please click "Switch to ' . $newPlan->name . '" to pay.');
        }

        $this->activatePlan($org, $newPlan);

        return redirect()->route('organization.subscription.index')->with('success', 'Successfully switched to ' . $newPlan->name . ' plan!');
    }

}
