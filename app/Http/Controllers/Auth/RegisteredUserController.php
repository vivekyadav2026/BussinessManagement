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
            'organization_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $org = \App\Models\Organization::create([
                'name' => $request->organization_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'organization_id' => $org->id,
            ]);

            // Assign Organization Admin role (will fail if role doesn't exist, handled by seeder)
            // Ensure the role exists, or just create it if it doesn't for robustness
            \App\Models\Role::firstOrCreate(['name' => 'Organization Admin', 'organization_id' => $org->id]);
            $user->assignRole('Organization Admin');

            // Assign default Plan subscription based on Super Admin Trial Settings
            $freePlan = \App\Models\Plan::where('name', 'Free')->first();
            if (!$freePlan) {
                $freePlan = \App\Models\Plan::create([
                    'name' => 'Free',
                    'price_monthly' => 0,
                    'price_yearly' => 0,
                    'is_active' => true,
                    'description' => 'Default free trial plan'
                ]);
            }
            
            // Ensure plan has retail and payroll modules enabled
            \App\Models\PlanFeature::firstOrCreate(['plan_id' => $freePlan->id, 'feature_code' => 'module_retail'], ['feature_value' => 'true']);
            \App\Models\PlanFeature::firstOrCreate(['plan_id' => $freePlan->id, 'feature_code' => 'module_payroll'], ['feature_value' => 'true']);

            $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
            $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1');

            if ($enableTrial === '0' || $trialDays <= 0) {
                $status = 'Expired';
                $endsAt = now();
            } else {
                $status = 'Trial';
                $endsAt = now()->addDays($trialDays);
            }

            \App\Models\OrganizationSubscription::create([
                'organization_id' => $org->id,
                'plan_id' => $freePlan->id,
                'status' => $status,
                'starts_at' => now(),
                'ends_at' => $endsAt,
            ]);

            return $user;
        });


        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
