<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        if (!$locationId) {
            return redirect()->route('organization.dashboard')->with('error', 'Please select a location.');
        }

        $tables = RestaurantTable::where('organization_id', $orgId)
                                 ->where('location_id', $locationId)
                                 ->get();

        return view('organization.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        RestaurantTable::create([
            'organization_id' => auth()->user()->organization_id,
            'location_id' => session('active_location_id'),
            'name' => $request->name,
        ]);

        return back()->with('success', 'Table created successfully.');
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $table->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Table updated.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return back()->with('success', 'Table deleted.');
    }

    public function regenerateQr(RestaurantTable $table)
    {
        $table->generateNewToken();
        return back()->with('success', 'QR Code regenerated. The old one is now invalid.');
    }

    public function printSheet()
    {
        $tables = RestaurantTable::where('organization_id', auth()->user()->organization_id)
                                 ->where('location_id', session('active_location_id'))
                                 ->where('is_active', true)
                                 ->get();
                                 
        return view('organization.tables.print', compact('tables'));
    }
}
