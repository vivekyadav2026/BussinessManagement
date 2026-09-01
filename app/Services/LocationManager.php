<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Session;

class LocationManager
{
    public static function getActiveLocationId()
    {
        $id = Session::get('active_location_id');
        if (!$id && auth()->check() && auth()->user()->organization_id) {
            $firstLocation = Location::where('organization_id', auth()->user()->organization_id)->first();
            if ($firstLocation) {
                $id = $firstLocation->id;
                Session::put('active_location_id', $id);
            }
        }
        return $id;
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
