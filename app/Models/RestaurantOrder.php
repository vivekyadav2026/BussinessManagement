<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    use \App\Traits\BelongsToOrganization;
    use \App\Traits\BelongsToLocation;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    /**
     * Generate a guaranteed unique order number for an organization/location.
     */
    public static function generateNextOrderNumber($organizationId, $locationId)
    {
        $prefix = 'TKN-' . $organizationId . '-';

        // Find the last order with this prefix for this organization
        $lastOrder = static::where('organization_id', $organizationId)
            ->where('order_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        do {
            $orderNumber = $prefix . $nextNumber;
            $exists = static::where('order_number', $orderNumber)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $orderNumber;
    }
}

