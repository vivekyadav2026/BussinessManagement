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
        $response = $this->postJson(route('organization.subscription.confirm'), ['plan_id' => $proPlan->id]);
        $response->assertStatus(200);

        // Refresh org
        $org->refresh();
        
        $this->assertFalse(SubscriptionService::hasReachedLimit($org->id, 'max_employees', 100)); // Unlimited
        $this->assertTrue(SubscriptionService::hasFeature($org->id, 'module_payroll')); // Payroll true
    }

    public function test_can_activate_addons_without_cancelling_base_plan_and_stack_limits()
    {
        $org = Organization::create(['name' => 'Addon Test Org', 'business_type' => 'business']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = \App\Models\Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $user->roles()->attach($role);

        $basePlan = Plan::create(['name' => 'Starter Base', 'type' => 'base', 'price_monthly' => 199]);
        $basePlan->features()->create(['feature_code' => 'max_invoices_per_month', 'feature_value' => '50']);
        $basePlan->features()->create(['feature_code' => 'module_payroll', 'feature_value' => 'false']);
        $basePlan->features()->create(['feature_code' => 'advanced_analytics', 'feature_value' => 'false']);

        // Set Base Subscription
        OrganizationSubscription::create([
            'organization_id' => $org->id,
            'plan_id' => $basePlan->id,
            'status' => 'Active',
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addYear()
        ]);

        $invoiceAddon = Plan::create(['name' => 'Extra 5000 Invoices (Add-on)', 'type' => 'addon', 'price_monthly' => 149]);
        $invoiceAddon->features()->create(['feature_code' => 'max_invoices_per_month', 'feature_value' => '5000']);

        $analyticsAddon = Plan::create(['name' => 'Advanced Analytics (Add-on)', 'type' => 'addon', 'price_monthly' => 99]);
        $analyticsAddon->features()->create(['feature_code' => 'advanced_analytics', 'feature_value' => 'true']);

        // 1. Initial State
        $this->assertEquals('Starter Base', $org->activeSubscription->plan->name);
        $this->assertEquals('50', SubscriptionService::getFeatureValue($org->id, 'max_invoices_per_month'));
        $this->assertFalse(SubscriptionService::hasFeature($org->id, 'advanced_analytics'));

        // 2. Buy Invoice Add-on
        $this->actingAs($user);
        $res1 = $this->postJson(route('organization.subscription.confirm'), ['plan_id' => $invoiceAddon->id]);
        $res1->assertStatus(200);

        $org->refresh();

        // Verify Base Plan is NOT cancelled
        $this->assertNotNull($org->activeSubscription);
        $this->assertEquals('Starter Base', $org->activeSubscription->plan->name);
        $this->assertEquals('Active', $org->activeSubscription->status);
        $this->assertCount(1, $org->activeAddons);
        
        // Verify Invoice limit stacked (50 + 5000 = 5050)
        $this->assertEquals('5050', SubscriptionService::getFeatureValue($org->id, 'max_invoices_per_month'));

        // 3. Buy Second Add-on (Analytics)
        $res2 = $this->postJson(route('organization.subscription.confirm'), ['plan_id' => $analyticsAddon->id]);
        $res2->assertStatus(200);

        $org->refresh();

        // Verify Base Plan + Both Add-ons are active
        $this->assertEquals('Starter Base', $org->activeSubscription->plan->name);
        $this->assertCount(2, $org->activeAddons);
        $this->assertEquals('5050', SubscriptionService::getFeatureValue($org->id, 'max_invoices_per_month'));
        $this->assertTrue(SubscriptionService::hasFeature($org->id, 'advanced_analytics'));
    }

    public function test_can_bundle_and_checkout_base_plan_with_multiple_addons_in_single_payment()
    {
        $org = Organization::create(['name' => 'Bundle Org', 'business_type' => 'restaurant']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = \App\Models\Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $user->roles()->attach($role);

        $basePlan = Plan::create(['name' => 'Restaurant Base', 'type' => 'base', 'price_monthly' => 599, 'category' => 'restaurant']);
        $basePlan->features()->create(['feature_code' => 'max_tables', 'feature_value' => '10']);
        $basePlan->features()->create(['feature_code' => 'kitchen_display', 'feature_value' => 'false']);

        $tableAddon = Plan::create(['name' => '+20 Tables', 'type' => 'addon', 'price_monthly' => 199]);
        $tableAddon->features()->create(['feature_code' => 'max_tables', 'feature_value' => '20']);

        $kdsAddon = Plan::create(['name' => 'KDS Kitchen Display', 'type' => 'addon', 'price_monthly' => 299]);
        $kdsAddon->features()->create(['feature_code' => 'kitchen_display', 'feature_value' => 'true']);

        $this->actingAs($user);

        // 1. Initiate Bundled Payment
        $initResponse = $this->postJson(route('organization.subscription.initiate', $basePlan->id), [
            'billing_cycle' => 'monthly',
            'addon_ids' => [$tableAddon->id, $kdsAddon->id]
        ]);
        $initResponse->assertStatus(200);
        $initResponse->assertJson([
            'success' => true,
            'amount' => (599 + 199 + 299) * 100 // 1097 * 100
        ]);

        // 2. Confirm Bundled Payment
        $confirmResponse = $this->postJson(route('organization.subscription.confirm'), [
            'plan_id' => $basePlan->id,
            'addon_ids' => [$tableAddon->id, $kdsAddon->id],
            'billing_cycle' => 'monthly',
            'razorpay_order_id' => $initResponse->json('order_id')
        ]);
        $confirmResponse->assertStatus(200);

        $org->refresh();

        // Verify Base Plan is active
        $this->assertNotNull($org->activeSubscription);
        $this->assertEquals('Restaurant Base', $org->activeSubscription->plan->name);
        $this->assertEquals('Active', $org->activeSubscription->status);

        // Verify both addons are active
        $this->assertCount(2, $org->activeAddons);
        
        // Verify limits & features combined
        $this->assertEquals('30', SubscriptionService::getFeatureValue($org->id, 'max_tables')); // 10 + 20 = 30
        $this->assertTrue(SubscriptionService::hasFeature($org->id, 'kitchen_display'));
    }
}
