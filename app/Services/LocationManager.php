<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Session;

class LocationManager
{
    public static function getActiveLocationId()
    {
        return Session::get('active_location_id');
    }

    public static function setActiveLocationId($locationId)
    {
        Session::put('active_location_id', $locationId);
    }
    
    public static function getActiveLocation()
    {
        $id = self::getActiveLocationId();
        return $id ? Location::find($id) : null;
    }
}
