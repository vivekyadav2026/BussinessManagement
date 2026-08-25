<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\LocationManager;
use App\Models\Location;

class LocationContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->organization_id) {
            $user = auth()->user();
            $activeId = LocationManager::getActiveLocationId();

            if (!$activeId || !$user->hasAccessToLocation($activeId)) {
                // Auto-select first available location
                if ($user->hasRole('Organization Admin') || $user->hasRole('Super Admin')) {
                    $firstLocation = Location::where('organization_id', $user->organization_id)
                                              ->where('is_active', true)->first();
                } else {
                    $firstLocation = $user->locations()->where('is_active', true)->first();
                }

                if ($firstLocation) {
                    LocationManager::setActiveLocationId($firstLocation->id);
                } else {
                    // No active locations available
                    LocationManager::setActiveLocationId(null);
                }
            }
        }

        return $next($request);
    }
}
