<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Location;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\OrganizationSubscription;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = clone \App\Models\User::firstOrNew(['email' => 'superadmin@example.com']);
        $superAdmin->name = 'Super Admin';
        $superAdmin->password = Hash::make('password');
        $superAdmin->save();
        
        $roleSuper = Role::firstOrCreate(['name' => 'Super Admin', 'organization_id' => null]);
        if(!$superAdmin->roles->contains($roleSuper->id)) $superAdmin->roles()->attach($roleSuper);

        // Ensure Plans Exist
        $freePlan = Plan::firstOrCreate(
            ['name' => 'Free'],
            ['price_monthly' => 0, 'price_yearly' => 0, 'is_active' => true, 'description' => 'Great for beginners']
        );
        $freeFeatures = ['1 User', 'Unlimited Products', 'Up to 50 Invoices/mo', 'Basic Reports'];
        foreach($freeFeatures as $feat) {
            PlanFeature::firstOrCreate(['plan_id' => $freePlan->id, 'feature_code' => $feat], ['feature_value' => 'true']);
        }

        $proPlan = Plan::firstOrCreate(
            ['name' => 'Pro Plan'],
            ['price_monthly' => 499, 'price_yearly' => 4990, 'is_active' => true, 'description' => 'Best for growing businesses']
        );
        $oldFeatures = ['max_employees' => '50', 'module_payroll' => 'true', 'module_restaurant' => 'true'];
        foreach($oldFeatures as $code => $value) {
            PlanFeature::firstOrCreate(['plan_id' => $proPlan->id, 'feature_code' => $code], ['feature_value' => $value]);
        }
        $proFeaturesList = ['5 Users', 'Unlimited Invoices', 'Employee Payroll', 'Payment Gateway (Razorpay)'];
        foreach($proFeaturesList as $feat) {
            PlanFeature::firstOrCreate(['plan_id' => $proPlan->id, 'feature_code' => $feat], ['feature_value' => 'true']);
        }

        $restPlan = Plan::firstOrCreate(
            ['name' => 'Restaurant'],
            ['price_monthly' => 999, 'price_yearly' => 9990, 'is_active' => true, 'description' => 'Tailored for food joints']
        );
        $restFeatures = ['Everything in Pro', 'Digital QR Menu', 'Kitchen Display System', 'Table Management'];
        foreach($restFeatures as $feat) {
            PlanFeature::firstOrCreate(['plan_id' => $restPlan->id, 'feature_code' => $feat], ['feature_value' => 'true']);
        }

        $entPlan = Plan::firstOrCreate(
            ['name' => 'Enterprise'],
            ['price_monthly' => 0, 'price_yearly' => 0, 'is_active' => true, 'description' => 'Custom solutions']
        );
        $entFeatures = ['Unlimited Users', 'Multiple Locations', 'Priority Support', 'Custom Integrations'];
        foreach($entFeatures as $feat) {
            PlanFeature::firstOrCreate(['plan_id' => $entPlan->id, 'feature_code' => $feat], ['feature_value' => 'true']);
        }

        // 2. Retail/ERP Organization
        $orgA = Organization::firstOrCreate(['name' => 'TechCorp Retail']);
        OrganizationSubscription::firstOrCreate([
            'organization_id' => $orgA->id,
            'plan_id' => $proPlan->id,
            'status' => 'Active',
            'starts_at' => now(),
            'ends_at' => now()->addYear()
        ]);

        $locA = Location::firstOrCreate(['organization_id' => $orgA->id, 'name' => 'Main Branch']);
        $locA2 = Location::firstOrCreate(['organization_id' => $orgA->id, 'name' => 'Warehouse']);

        $roleAdminA = Role::firstOrCreate(['organization_id' => $orgA->id, 'name' => 'Organization Admin']);
        
        $adminA = clone \App\Models\User::firstOrNew(['email' => 'admin@techcorp.com']);
        $adminA->name = 'TechCorp Admin';
        $adminA->password = Hash::make('password');
        $adminA->organization_id = $orgA->id;
        $adminA->save();
        if(!$adminA->roles->contains($roleAdminA->id)) $adminA->roles()->attach($roleAdminA);

        $employeeA = Employee::firstOrCreate([
            'organization_id' => $orgA->id, 'first_name' => 'Rahul', 'last_name' => 'Sharma', 'location_id' => $locA->id
        ], ['status' => 'Active']);

        $catA = Category::firstOrCreate(['organization_id' => $orgA->id, 'name' => 'Electronics']);
        $productA = Product::firstOrCreate([
            'organization_id' => $orgA->id, 'name' => 'Wireless Mouse', 'barcode' => 'WM-001'
        ], [
            'category_id' => $catA->id, 'selling_price' => 599, 'purchase_price' => 300
        ]);
        InventoryStock::firstOrCreate([
            'organization_id' => $orgA->id, 'location_id' => $locA->id, 'product_id' => $productA->id
        ], ['quantity' => 50]);

        $clientA = Client::firstOrCreate(['organization_id' => $orgA->id, 'name' => 'Acme Corp'], ['email' => 'contact@acme.com', 'phone' => '9876543210']);
        
        $invoiceA = Invoice::firstOrCreate([
            'organization_id' => $orgA->id, 'invoice_number' => 'INV-0001'
        ], [
            'location_id' => $locA->id, 'client_id' => $clientA->id, 'invoice_date' => now(), 'subtotal' => 1198, 'tax' => 215.64, 'grand_total' => 1413.64, 'amount_paid' => 1413.64, 'status' => 'Paid'
        ]);
        InvoiceItem::firstOrCreate([
            'invoice_id' => $invoiceA->id, 'product_id' => $productA->id
        ], [
            'product_name_snapshot' => 'Wireless Mouse', 'quantity' => 2, 'unit_price' => 599, 'tax' => 215.64, 'total' => 1413.64
        ]);
        \App\Models\Transaction::firstOrCreate(['invoice_id' => $invoiceA->id], [
            'organization_id' => $orgA->id, 'location_id' => $locA->id, 'amount' => 1413.64, 'payment_method' => 'UPI', 'payment_date' => now()
        ]);

        // 3. Restaurant Organization
        $orgB = Organization::firstOrCreate(['name' => 'Spice Kitchen Restaurant']);
        OrganizationSubscription::firstOrCreate([
            'organization_id' => $orgB->id, 'plan_id' => $proPlan->id, 'status' => 'Active', 'starts_at' => now(), 'ends_at' => now()->addYear()
        ]);
        $locB = Location::firstOrCreate(['organization_id' => $orgB->id, 'name' => 'Downtown Cafe']);

        $roleAdminB = Role::firstOrCreate(['organization_id' => $orgB->id, 'name' => 'Organization Admin']);
        $roleKitchenB = Role::firstOrCreate(['organization_id' => $orgB->id, 'name' => 'Kitchen Staff']);
        // Add restaurant permissions to kitchen staff
        $restaurantPerms = \App\Models\Permission::where('module', 'Restaurant')->pluck('id')->toArray();
        $roleKitchenB->permissions()->sync($restaurantPerms);

        $adminB = clone \App\Models\User::firstOrNew(['email' => 'admin@spicekitchen.com']);
        $adminB->name = 'SpiceKitchen Admin';
        $adminB->password = Hash::make('password');
        $adminB->organization_id = $orgB->id;
        $adminB->save();
        if(!$adminB->roles->contains($roleAdminB->id)) $adminB->roles()->attach($roleAdminB);

        $kitchenB = clone \App\Models\User::firstOrNew(['email' => 'kitchen@spicekitchen.com']);
        $kitchenB->name = 'Chef Sanjay';
        $kitchenB->password = Hash::make('password');
        $kitchenB->organization_id = $orgB->id;
        $kitchenB->save();
        if(!$kitchenB->roles->contains($roleKitchenB->id)) $kitchenB->roles()->attach($roleKitchenB);

        $menuCat = MenuCategory::firstOrCreate(['organization_id' => $orgB->id, 'name' => 'Main Course'], ['location_id' => $locB->id]);
        $menuItem = MenuItem::firstOrCreate(['menu_category_id' => $menuCat->id, 'name' => 'Butter Chicken'], ['description' => 'Creamy rich tomato gravy', 'price' => 350]);
        $menuItem2 = MenuItem::firstOrCreate(['menu_category_id' => $menuCat->id, 'name' => 'Garlic Naan'], ['description' => 'Fresh bread', 'price' => 50]);

        $table = RestaurantTable::firstOrCreate(['organization_id' => $orgB->id, 'name' => 'Table 01'], ['location_id' => $locB->id, 'public_token' => Str::random(16)]);

        $kot = RestaurantOrder::firstOrCreate(['organization_id' => $orgB->id, 'order_number' => 'ORD-100'], [
            'location_id' => $locB->id, 'restaurant_table_id' => $table->id, 'customer_name' => 'Amit', 'order_type' => 'Dine-In', 'subtotal' => 400, 'tax' => 20, 'total' => 420, 'payment_status' => 'Unpaid', 'status' => 'Preparing'
        ]);
        RestaurantOrderItem::firstOrCreate(['restaurant_order_id' => $kot->id, 'menu_item_id' => $menuItem->id], ['name_snapshot' => 'Butter Chicken', 'price_snapshot' => 350, 'quantity' => 1, 'total' => 350]);
        RestaurantOrderItem::firstOrCreate(['restaurant_order_id' => $kot->id, 'menu_item_id' => $menuItem2->id], ['name_snapshot' => 'Garlic Naan', 'price_snapshot' => 50, 'quantity' => 1, 'total' => 50]);
    }
}
