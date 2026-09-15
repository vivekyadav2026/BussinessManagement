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
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Step 1: Business Details
            'organization_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:business,restaurant'],
            'business_phone' => ['required', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            
            // Step 2: Admin Details
            'name' => ['required', 'string', 'max:255'],
            'admin_phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Step 3: Plan
            'plan' => ['nullable', 'integer']
        ]);

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $fullAddress = trim("{$request->address}, {$request->state}, {$request->country} - {$request->pincode}");
            
            $org = \App\Models\Organization::create([
                'name' => $request->organization_name,
                'email' => $request->email, // Using admin email as org email
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

            // Assign Organization Admin role (will fail if role doesn't exist, handled by seeder)
            // Ensure the role exists, or just create it if it doesn't for robustness
            \App\Models\Role::firstOrCreate(['name' => 'Organization Admin', 'organization_id' => $org->id]);
            $user->assignRole('Organization Admin');

            // Assign default or selected Plan subscription based on Super Admin Trial Settings
            $targetPlan = null;
            if ($request->filled('plan')) {
                $targetPlan = \App\Models\Plan::where('id', $request->plan)->where('is_active', true)->where('type', 'base')->first();
            }
            if (!$targetPlan) {
                $targetPlan = \App\Models\Plan::where('name', 'Free')->first();
            }
            if (!$targetPlan) {
                $targetPlan = \App\Models\Plan::create([
                    'name' => 'Free',
                    'category' => 'all',
                    'price_monthly' => 0,
                    'price_yearly' => 0,
                    'is_active' => true,
                    'description' => 'Default free trial plan'
                ]);
            }
            
            // Ensure plan has retail and payroll modules enabled
            \App\Models\PlanFeature::firstOrCreate(['plan_id' => $targetPlan->id, 'feature_code' => 'module_retail'], ['feature_value' => 'true']);
            \App\Models\PlanFeature::firstOrCreate(['plan_id' => $targetPlan->id, 'feature_code' => 'module_payroll'], ['feature_value' => 'true']);

            $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
            $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1');

            if ($enableTrial === '0' || $trialDays <= 0) {
                $status = 'Expired';
                $endsAt = now()->subDay();
            } else {
                $status = 'Trial';
                $endsAt = now()->addDays($trialDays);
            }

            \App\Models\OrganizationSubscription::create([
                'organization_id' => $org->id,
                'plan_id' => $targetPlan->id,
                'status' => $status,
                'starts_at' => now(),
                'ends_at' => $endsAt,
            ]);

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
