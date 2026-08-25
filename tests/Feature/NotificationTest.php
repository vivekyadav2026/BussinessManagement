<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use App\Services\InventoryService;
use App\Notifications\LowStockNotification;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_triggered()
    {
        Notification::fake();

        $org = Organization::create(['name' => 'Test Org']);
        
        $admin = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $admin->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'Store', 'is_active' => true]);
        $cat = Category::create(['organization_id' => $org->id, 'name' => 'Toys']);

        $product = Product::create([
            'organization_id' => $org->id,
            'category_id' => $cat->id,
            'name' => 'Action Figure',
            'min_stock_level' => 5,
        ]);

        // Add 10 stock
        InventoryService::adjustStock($product, $loc->id, 10, 'in');

        // It should NOT notify since it was 0 and now is 10 (above min 5)
        Notification::assertNothingSent();

        // Reduce 6 stock, new stock is 4
        InventoryService::adjustStock($product, $loc->id, -6, 'out');

        Notification::assertSentTo(
            [$admin], LowStockNotification::class, function ($notification) use ($product) {
                return $notification->product->id === $product->id;
            }
        );
    }
}
