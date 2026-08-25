<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use \App\Traits\BelongsToOrganization;
    use \App\Traits\BelongsToLocation;

    protected $fillable = ['organization_id', 'location_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
