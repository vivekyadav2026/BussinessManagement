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
        $locations = Location::where('organization_id', auth()->user()->organization_id)->get();
        return view('organization.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('organization.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
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
        return view('organization.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        abort_if($location->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
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
        return view('organization.locations.show', compact('location'));
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
