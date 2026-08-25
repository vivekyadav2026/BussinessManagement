<?php

namespace App\Traits;

use App\Models\Scopes\LocationScope;
use App\Models\Location;
use App\Services\LocationManager;

trait BelongsToLocation
{
    protected static function bootBelongsToLocation()
    {
        static::addGlobalScope(new LocationScope);

        static::creating(function ($model) {
            if (!$model->location_id) {
                $model->location_id = LocationManager::getActiveLocationId();
            }
        });
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
