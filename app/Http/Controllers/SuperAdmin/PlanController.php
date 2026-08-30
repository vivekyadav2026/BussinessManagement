<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('features')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'array',
            'features.*.code' => 'required_with:features|string',
            'features.*.value' => 'required_with:features|string',
        ]);

        $plan = Plan::create($request->only('name', 'price_monthly', 'price_yearly', 'description', 'is_active'));

        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                if (!empty($feature['code']) && !empty($feature['value'])) {
                    $plan->features()->create([
                        'feature_code' => $feature['code'],
                        'feature_value' => $feature['value']
                    ]);
                }
            }
        }

        return redirect()->route('super-admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $plan->load('features');
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'array',
            'features.*.code' => 'required_with:features|string',
            'features.*.value' => 'required_with:features|string',
        ]);

        $plan->update($request->only('name', 'price_monthly', 'price_yearly', 'description', 'is_active'));

        $plan->features()->delete();
        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                if (!empty($feature['code']) && !empty($feature['value'])) {
                    $plan->features()->create([
                        'feature_code' => $feature['code'],
                        'feature_value' => $feature['value']
                    ]);
                }
            }
        }

        return redirect()->route('super-admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function show(Plan $plan)
    {
        $plan->load('features');
        return view('super-admin.plans.show', compact('plan'));
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->count() > 0) {
            return redirect()->route('super-admin.plans.index')->with('error', 'Cannot delete plan that has active subscriptions.');
        }
        $plan->features()->delete();
        $plan->delete();
        return redirect()->route('super-admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
