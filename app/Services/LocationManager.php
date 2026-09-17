<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Session;

class LocationManager
{
    public static function getActiveLocationId()
    {
        $id = Session::get('active_location_id');

        if (auth()->check() && auth()->user()->organization_id) {
            $user = auth()->user();

            // Validate that the session location is accessible by the current user
            if ($id && !$user->hasAccessToLocation($id)) {
                $id = null;
            }

            // If no valid active location is set, resolve default allowed location for user
            if (!$id) {
                if ($user->hasRole('Organization Admin') || $user->hasRole('Super Admin')) {
                    $firstLocation = Location::where('organization_id', $user->organization_id)
                        ->where('is_active', true)
                        ->first();
                } else {
                    // For employees, restrict to assigned locations in user_locations or employee profile
                    $firstLocation = $user->locations()->where('is_active', true)->first();
                    
                    if (!$firstLocation && $user->employee && $user->employee->location_id) {
                        $firstLocation = Location::where('id', $user->employee->location_id)
                            ->where('is_active', true)
                            ->first();
                        
                        // Automatically attach to user_locations for future checks
                        if ($firstLocation) {
                            $user->locations()->syncWithoutDetaching([$firstLocation->id]);
                        }
                    }
                }

                if ($firstLocation) {
                    $id = $firstLocation->id;
                    Session::put('active_location_id', $id);
                } else {
                    Session::forget('active_location_id');
                }
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
