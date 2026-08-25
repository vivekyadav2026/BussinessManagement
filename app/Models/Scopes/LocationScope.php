<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\LocationManager;

class LocationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // If a location is currently active in the session, automatically filter by it.
        // We only apply this if we are actually in a web context where LocationManager works.
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        $activeLocationId = LocationManager::getActiveLocationId();
        
        if ($activeLocationId) {
            $builder->where($model->getTable() . '.location_id', $activeLocationId);
        }
    }
}
