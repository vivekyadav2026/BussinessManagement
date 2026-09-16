<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Services\LocationManager;

class LocationController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $locations = Location::where('organization_id', $orgId)->withCount('employees')->latest()->paginate(15)->withQueryString();
        $totalLocations = Location::where('organization_id', $orgId)->count();
        $activeLocations = Location::where('organization_id', $orgId)->where('is_active', true)->count();
        $totalAssignedEmployees = \App\Models\Employee::where('organization_id', $orgId)->whereNotNull('location_id')->count();
        $maxLocations = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_locations') ?? '1';
        $activeLocationId = LocationManager::getActiveLocationId();
        $limitReached = \App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_locations', $totalLocations);

        return view('organization.locations.index', compact(
            'locations',
            'maxLocations',
            'totalLocations',
            'activeLocations',
            'totalAssignedEmployees',
            'activeLocationId',
            'limitReached'
        ));
    }


    public function create()
    {
        $orgId = auth()->user()->organization_id;
        $totalLocations = Location::where('organization_id', $orgId)->count();
        $maxLocations = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_locations') ?? '1';
        $limitReached = \App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_locations', $totalLocations);

        return view('organization.locations.create', compact('totalLocations', 'maxLocations', 'limitReached'));
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $currentLocationsCount = Location::where('organization_id', $orgId)->count();

        if (\App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_locations', $currentLocationsCount)) {
            $limit = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_locations');
            return back()->withInput()->with('error', "Location limit reached ({$currentLocationsCount}/{$limit}). Please upgrade your plan to add more locations.");
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'address' => 'nullable|string',
        ], [
            'phone.regex' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
        ]);

        Location::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => true,
        ]);

        return redirect()->route('organization.locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);
        $location->loadCount('employees');
        return view('organization.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'address' => 'nullable|string',
        ], [
            'phone.regex' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
        ]);

        $location->update($request->only('name', 'phone', 'address'));

        return redirect()->route('organization.locations.index')->with('success', 'Location updated successfully.');
    }

    public function toggleStatus(Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);
        $location->update(['is_active' => !$location->is_active]);
        
        // If we just deactivated the currently active location, clear it from session
        if (!$location->is_active && LocationManager::getActiveLocationId() == $location->id) {
            LocationManager::setActiveLocationId(null);
        }

        return back()->with('success', 'Location status updated.');
    }

    public function switchLocation(Request $request)
    {
        $request->validate(['location_id' => 'required|exists:locations,id']);
        
        $location = Location::find($request->location_id);
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);
        abort_if(!auth()->user()->hasAccessToLocation($location->id), 403, 'You do not have access to this location.');

        LocationManager::setActiveLocationId($location->id);
        
        return back()->with('success', 'Active location switched to ' . $location->name);
    }

    public function show(Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);
        $location->load(['employees' => function ($q) {
            $q->latest();
        }, 'users']);
        $activeLocationId = LocationManager::getActiveLocationId();
        return view('organization.locations.show', compact('location', 'activeLocationId'));
    }

    public function destroy(Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);
        if ($location->employees()->count() > 0) {
            return redirect()->route('organization.locations.index')->with('error', 'Cannot delete location that has active employees.');
        }
        $location->delete();
        return redirect()->route('organization.locations.index')->with('success', 'Location deleted successfully.');
    }
}
