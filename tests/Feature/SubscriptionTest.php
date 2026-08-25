<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\OrganizationSubscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_switch_plan_and_check_limits()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = \App\Models\Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $user->roles()->attach($role);

        $freePlan = Plan::create(['name' => 'Free', 'price_monthly' => 0]);
        $freePlan->features()->create(['feature_code' => 'max_employees', 'feature_value' => '2']);
        $freePlan->features()->create(['feature_code' => 'module_payroll', 'feature_value' => 'false']);

        $proPlan = Plan::create(['name' => 'Pro', 'price_monthly' => 50]);
        $proPlan->features()->create(['feature_code' => 'max_employees', 'feature_value' => 'unlimited']);
        $proPlan->features()->create(['feature_code' => 'module_payroll', 'feature_value' => 'true']);

        // Set to Free Plan
        OrganizationSubscription::create([
            'organization_id' => $org->id,
            'plan_id' => $freePlan->id,
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addYear()
        ]);

        $this->assertTrue(SubscriptionService::hasReachedLimit($org->id, 'max_employees', 2)); // Reached limit 2
        $this->assertFalse(SubscriptionService::hasReachedLimit($org->id, 'max_employees', 1)); // Under limit 2
        $this->assertFalse(SubscriptionService::hasFeature($org->id, 'module_payroll')); // Payroll false

        // User switches to Pro plan
        $this->actingAs($user);
        $response = $this->post(route('organization.subscription.switch'), ['plan_id' => $proPlan->id]);
        $response->assertRedirect(route('organization.subscription.index'));

        // Refresh org
        $org->refresh();

        $this->assertFalse(SubscriptionService::hasReachedLimit($org->id, 'max_employees', 100)); // Unlimited
        $this->assertTrue(SubscriptionService::hasFeature($org->id, 'module_payroll')); // Payroll true
    }
}
