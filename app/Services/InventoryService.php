<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Strictly adjusts stock levels and records the movement within a transaction.
     *
     * @param Product $product
     * @param int $locationId
     * @param int $quantity The delta to apply (positive or negative)
     * @param string $type 'in', 'out', or 'adjustment'
     * @param string|null $notes
     * @param string|null $reference
     * @param int|null $userId
     * @return InventoryStock
     * @throws \Exception
     */
    public static function adjustStock(Product $product, $locationId, $quantity, $type, $notes = null, $reference = null, $userId = null)
    {
        if ($quantity == 0) {
            throw new \Exception("Quantity cannot be zero for a stock movement.");
        }

        return DB::transaction(function () use ($product, $locationId, $quantity, $type, $notes, $reference, $userId) {
            
            // Get or create the stock record for this location
            $stock = InventoryStock::firstOrCreate(
                [
                    'organization_id' => $product->organization_id,
                    'location_id' => $locationId,
                    'product_id' => $product->id,
                ],
                ['quantity' => 0]
            );

            // Calculate new quantity
            $oldQuantity = $stock->quantity;
            $newQuantity = $oldQuantity + $quantity;
            
            // Note: we allow negative stock in some businesses (e.g., ringing up items before delivery is officially received)
            // If you want strict positive stock, uncomment below:
            // if ($newQuantity < 0) {
            //     throw new \Exception("Insufficient stock for product: " . $product->name);
            // }

            // Update stock
            $stock->update(['quantity' => $newQuantity]);

            // Fire Low Stock Notification
            if ($newQuantity <= $product->min_stock_level && $oldQuantity > $product->min_stock_level) {
                // Notify organization admins
                $admins = \App\Models\User::where('organization_id', $product->organization_id)
                    ->whereHas('roles', function($q) {
                        $q->where('name', 'Organization Admin')->orWhere('name', 'Admin');
                    })->get();
                    
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($product));
            }

            // Record movement
            StockMovement::create([
                'organization_id' => $product->organization_id,
                'location_id' => $locationId,
                'product_id' => $product->id,
                'user_id' => $userId ?? auth()->id(),
                'type' => $type,
                'quantity' => $quantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            return $stock;
        });
    }

    /**
     * Generates a unique SKU based on category and product name
     */
    public static function generateSku($organizationId, $categoryId, $productName)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $productName), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }
        
        $uniqueId = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $sku = "{$prefix}-{$uniqueId}";
        
        // Ensure unique
        while (Product::where('organization_id', $organizationId)->where('sku', $sku)->exists()) {
            $uniqueId = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $sku = "{$prefix}-{$uniqueId}";
        }
        
        return $sku;
    }
}
