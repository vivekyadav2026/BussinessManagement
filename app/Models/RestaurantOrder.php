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
}
