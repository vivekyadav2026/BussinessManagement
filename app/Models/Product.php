<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'category_id', 'name', 'sku', 'barcode', 'description', 
        'purchase_price', 'selling_price', 'tax_rate', 'min_stock_level', 
        'image_path', 'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function inventoryStocks()
    {

        return $this->hasMany(InventoryStock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    
    // Helper to get stock for current location
    public function getStockAttribute()
    {
        $activeLocationId = \App\Services\LocationManager::getActiveLocationId();
        if (!$activeLocationId) return 0;
        
        $stock = $this->inventoryStocks()->where('location_id', $activeLocationId)->first();
        return $stock ? $stock->quantity : 0;
    }
}
