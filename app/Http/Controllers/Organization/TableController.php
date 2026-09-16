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
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        if (!$locationId) {
            return back()->with('error', 'Please select a location first.');
        }

        $currentTablesCount = RestaurantTable::where('organization_id', $orgId)->count();

        if (\App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_tables', $currentTablesCount)) {
            $limit = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_tables');
            return back()->with('error', "Table limit reached ({$currentTablesCount}/{$limit}). Please upgrade your plan to add more tables.");
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('restaurant_tables')->where(function ($query) use ($orgId, $locationId) {
                    return $query->where('organization_id', $orgId)->where('location_id', $locationId);
                })
            ]
        ], [
            'name.unique' => 'A table with this name already exists in this branch. Please use a unique table name (e.g. Table 1, Table 2, VIP 1).'
        ]);

        RestaurantTable::create([
            'organization_id' => $orgId,
            'location_id' => $locationId,
            'name' => trim($request->name),
        ]);

        return back()->with('success', 'Table created successfully.');
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('restaurant_tables')->where(function ($query) use ($orgId, $locationId) {
                    return $query->where('organization_id', $orgId)->where('location_id', $locationId);
                })->ignore($table->id)
            ],
            'is_active' => 'boolean'
        ], [
            'name.unique' => 'A table with this name already exists in this branch. Please use a unique table name.'
        ]);

        $table->update([
            'name' => trim($request->name),
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Table updated successfully.');
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

    public function printSheet(Request $request)
    {
        $query = RestaurantTable::where('organization_id', auth()->user()->organization_id)
                                 ->where('location_id', session('active_location_id'));

        if ($request->filled('table_id')) {
            $query->where('id', $request->table_id);
        } else {
            $query->where('is_active', true);
        }

        $tables = $query->get();
                                 
        return view('organization.tables.print', compact('tables'));
    }
}
