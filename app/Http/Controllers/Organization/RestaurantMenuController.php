<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class RestaurantMenuController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        if (!$locationId) {
            return redirect()->route('organization.dashboard')->with('error', 'Please select a location to manage the menu.');
        }

        $categories = MenuCategory::with(['items' => function($q) {
            $q->orderBy('sort_order');
        }])->where('organization_id', $orgId)
          ->where('location_id', $locationId)
          ->orderBy('sort_order')
          ->get();

        return view('organization.menu.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        MenuCategory::create([
            'organization_id' => auth()->user()->organization_id,
            'location_id' => session('active_location_id'),
            'name' => $request->name,
            'sort_order' => MenuCategory::where('location_id', session('active_location_id'))->count()
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, MenuCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $category = MenuCategory::find($request->menu_category_id);
        if (!$category || $category->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $path = $request->hasFile('photo') ? $request->file('photo')->store('menu_photos', 'public') : null;

        MenuItem::create([
            'menu_category_id' => $request->menu_category_id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'photo' => $path,
            'sort_order' => MenuItem::where('menu_category_id', $request->menu_category_id)->count()
        ]);

        return back()->with('success', 'Item added successfully.');
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        if ($item->category->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_available' => $request->has('is_available'),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menu_photos', 'public');
        }

        $item->update($data);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroyCategory(MenuCategory $category)
    {
        // Category scoped globally so finding it inherently means it's yours
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    public function destroyItem(MenuItem $item)
    {
        if ($item->category->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();
        return back()->with('success', 'Item deleted.');
    }
}
