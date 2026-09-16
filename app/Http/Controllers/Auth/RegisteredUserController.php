<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $plans = \App\Models\Plan::where('is_active', true)
            ->where('type', 'base')
            ->with('features')
            ->get();

        $addons = \App\Models\Plan::where('is_active', true)
            ->where('type', 'addon')
            ->with('features')
            ->get();

        $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
        $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1' && $trialDays > 0;
        $razorpayKey = config('services.razorpay.key');

        return view('auth.register', compact('plans', 'addons', 'trialDays', 'enableTrial', 'razorpayKey'));
    }

    /**
     * Generate a Razorpay Order for registration payment.
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'business_name' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'owner_phone' => ['nullable', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
        ], [
            'owner_phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
        ]);

        $plan = \App\Models\Plan::where('id', $request->plan_id)
            ->where('is_active', true)
            ->firstOrFail();

        $cycle = $request->billing_cycle;
        $basePrice = (float) ($cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly);
        $totalPrice = $basePrice;

        $addonIds = $request->input('addon_ids', []);
        $selectedAddons = [];
        if (!empty($addonIds) && is_array($addonIds)) {
            $addonModels = \App\Models\Plan::whereIn('id', $addonIds)->where('type', 'addon')->where('is_active', true)->get();
            foreach ($addonModels as $addon) {
                $addonPrice = (float) ($cycle === 'yearly' ? $addon->price_yearly : $addon->price_monthly);
                $totalPrice += $addonPrice;
                $selectedAddons[] = [
                    'name' => $addon->name,
                    'price' => $addonPrice
                ];
            }
        }

        // If price is 0, return free status
        if ($totalPrice <= 0) {
            return response()->json([
                'success' => true,
                'is_free' => true,
                'amount' => 0,
                'total_formatted' => '0',
                'plan_name' => $plan->name,
                'plan_id' => $plan->id,
                'addon_ids' => $addonIds,
                'billing_cycle' => $cycle
            ]);
        }

        $key = config('services.razorpay.key');
        if (!$key || $key === 'rzp_test_xxxxxxxxx') {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway key is not properly configured.'
            ], 400);
        }

        try {
            $payment = \App\Services\RazorpayPaymentService::createOrder($plan, $totalPrice);
            $planDescription = $plan->name . ' (' . ucfirst($cycle) . ')';
            if (count($selectedAddons) > 0) {
                $planDescription .= ' + ' . count($selectedAddons) . ' Add-on(s)';
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
                'business_name' => $request->business_name ?: config('app.name'),
                'owner_name' => $request->owner_name ?: 'Valued Merchant',
                'owner_email' => $request->owner_email ?: '',
                'owner_phone' => $request->owner_phone ?: '',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration Razorpay Order Creation Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
        $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1' && $trialDays > 0;

        $request->validate([
            // Step 1: Business Details
            'organization_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:business,restaurant'],
            'business_phone' => ['required', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            
            // Step 2: Admin Details
            'name' => ['required', 'string', 'max:255'],
            'admin_phone' => ['required', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Step 3: Plan & Payment
            'plan' => ['nullable', 'string'], // plan ID or 'trial'
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['exists:plans,id'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly'],
            'razorpay_order_id' => ['nullable', 'string'],
            'razorpay_payment_id' => ['nullable', 'string'],
            'razorpay_signature' => ['nullable', 'string'],
        ], [
            'business_phone.regex' => 'Please enter a valid 10-digit Indian mobile number (e.g. 9876543210).',
            'admin_phone.regex' => 'Please enter a valid 10-digit Indian mobile number (e.g. 9876543210).',
        ]);

        $selectedPlanValue = $request->input('plan');
        $billingCycle = $request->input('billing_cycle', 'monthly');

        // Check if customer selected Free Trial
        $isTrialSelected = ($selectedPlanValue === 'trial' || empty($selectedPlanValue));

        // If customer opted for Free Trial but Super Admin has disabled Free Trial
        if ($isTrialSelected && !$enableTrial) {
            throw ValidationException::withMessages([
                'plan' => ['Free trial is currently disabled by administrator. Please select a subscription plan to register.'],
            ]);
        }

        // If paid plan selected, verify payment
        $gatewayPaymentId = null;
        $targetPlan = null;

        if (!$isTrialSelected) {
            $targetPlan = \App\Models\Plan::where('id', $selectedPlanValue)
                ->where('is_active', true)
                ->where('type', 'base')
                ->first();

            if (!$targetPlan) {
                throw ValidationException::withMessages([
                    'plan' => ['Selected plan is invalid or inactive.'],
                ]);
            }

            $expectedPrice = (float) ($billingCycle === 'yearly' ? $targetPlan->price_yearly : $targetPlan->price_monthly);

            if ($expectedPrice > 0) {
                // Must have order and payment id
                if (!$request->filled('razorpay_order_id') || !$request->filled('razorpay_payment_id')) {
                    throw ValidationException::withMessages([
                        'plan' => ['Payment confirmation is required to activate this plan.'],
                    ]);
                }

                try {
                    $payment = \App\Services\RazorpayPaymentService::verifyAndProcessPayment(
                        $request->razorpay_order_id,
                        $request->razorpay_payment_id,
                        $request->razorpay_signature
                    );
                    $gatewayPaymentId = $payment->id;
                } catch (\Exception $e) {
                    throw ValidationException::withMessages([
                        'plan' => ['Payment verification failed: ' . $e->getMessage()],
                    ]);
                }
            }
        }

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $isTrialSelected, $targetPlan, $billingCycle, $gatewayPaymentId, $trialDays, $enableTrial) {
            $fullAddress = trim("{$request->address}, {$request->state}, {$request->country} - {$request->pincode}");
            
            $org = \App\Models\Organization::create([
                'name' => $request->organization_name,
                'email' => $request->email,
                'phone' => $request->business_phone,
                'business_type' => $request->business_type,
                'gst_number' => $request->gst_number,
                'address' => $fullAddress,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $org->id,
            ]);

            \App\Models\Employee::create([
                'organization_id' => $org->id,
                'user_id' => $user->id,
                'first_name' => explode(' ', $request->name)[0] ?? $request->name,
                'last_name' => count(explode(' ', $request->name)) > 1 ? implode(' ', array_slice(explode(' ', $request->name), 1)) : null,
                'email' => $request->email,
                'phone' => $request->admin_phone,
                'designation' => 'Owner / Admin',
            ]);

            \App\Models\Location::create([
                'organization_id' => $org->id,
                'name' => 'Main Branch',
                'address' => $fullAddress,
                'phone' => $request->business_phone,
                'is_active' => true,
            ]);

            \App\Models\Role::firstOrCreate(['name' => 'Organization Admin', 'organization_id' => $org->id]);
            $user->assignRole('Organization Admin');

            if ($isTrialSelected) {
                // Assign Free / Trial plan
                $trialPlan = \App\Models\Plan::where('name', 'Free')->first();
                if (!$trialPlan) {
                    $trialPlan = \App\Models\Plan::create([
                        'name' => 'Free',
                        'category' => 'all',
                        'price_monthly' => 0,
                        'price_yearly' => 0,
                        'is_active' => true,
                        'description' => 'Default free trial plan'
                    ]);
                }

                \App\Models\PlanFeature::firstOrCreate(['plan_id' => $trialPlan->id, 'feature_code' => 'module_retail'], ['feature_value' => 'true']);
                \App\Models\PlanFeature::firstOrCreate(['plan_id' => $trialPlan->id, 'feature_code' => 'module_payroll'], ['feature_value' => 'true']);

                $status = ($enableTrial && $trialDays > 0) ? 'Trial' : 'Expired';
                $endsAt = ($enableTrial && $trialDays > 0) ? now()->addDays($trialDays) : now()->subDay();

                \App\Models\OrganizationSubscription::create([
                    'organization_id' => $org->id,
                    'plan_id' => $trialPlan->id,
                    'status' => $status,
                    'starts_at' => now(),
                    'ends_at' => $endsAt,
                ]);
            } else {
                // Paid Plan Activated
                $endsAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

                \App\Models\OrganizationSubscription::create([
                    'organization_id' => $org->id,
                    'plan_id' => $targetPlan->id,
                    'status' => 'Active',
                    'starts_at' => now(),
                    'ends_at' => $endsAt,
                    'gateway_payment_id' => $gatewayPaymentId,
                ]);

                // Also activate selected Add-ons if any
                $addonIds = $request->input('addon_ids', []);
                if (!empty($addonIds) && is_array($addonIds)) {
                    $addons = \App\Models\Plan::whereIn('id', $addonIds)->where('type', 'addon')->where('is_active', true)->get();
                    foreach ($addons as $addon) {
                        \App\Models\OrganizationSubscription::create([
                            'organization_id' => $org->id,
                            'plan_id' => $addon->id,
                            'status' => 'Active',
                            'starts_at' => now(),
                            'ends_at' => $endsAt,
                            'gateway_payment_id' => $gatewayPaymentId,
                        ]);
                    }
                }
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        if (\App\Services\SubscriptionService::isExpired($user->organization_id)) {
            return redirect(route('organization.subscription.index'))->with('error', 'Free trial is disabled or expired. Please select a subscription plan to activate your account.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
