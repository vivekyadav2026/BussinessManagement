<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $currentSubscription = auth()->user()->organization->activeSubscription;
        
        $plans = Plan::where('is_active', true)->with('features')->get();

        return view('organization.subscription.index', compact('currentSubscription', 'plans'));
    }

    public function switchPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $org = auth()->user()->organization;
        $newPlan = Plan::findOrFail($request->plan_id);

        // Terminate old
        if ($org->activeSubscription) {
            $org->activeSubscription->update([
                'status' => 'Cancelled',
                'ends_at' => Carbon::today()
            ]);
        }

        // Create new
        OrganizationSubscription::create([
            'organization_id' => $org->id,
            'plan_id' => $newPlan->id,
            'status' => 'Active',
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addYear() // Simulate yearly for now
        ]);

        return redirect()->route('organization.subscription.index')->with('success', 'Successfully switched to ' . $newPlan->name . ' plan!');
    }
}
