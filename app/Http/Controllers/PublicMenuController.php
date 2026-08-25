<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Location;
use App\Models\MenuCategory;

class PublicMenuController extends Controller
{
    public function show(Organization $organization, Location $location)
    {
        if ($location->organization_id !== $organization->id) {
            abort(404);
        }

        $categories = MenuCategory::with(['items' => function($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->where('organization_id', $organization->id)
          ->where('location_id', $location->id)
          ->where('is_active', true)
          ->orderBy('sort_order')
          ->get();

        return view('public.menu.index', compact('organization', 'location', 'categories'));
    }

    public function showByToken($token)
    {
        $table = \App\Models\RestaurantTable::where('public_token', $token)->where('is_active', true)->firstOrFail();
        
        session(['restaurant_table_id' => $table->id]);

        return $this->show($table->organization, $table->location);
    }
}
