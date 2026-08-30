<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('organization_id', auth()->user()->organization_id)->get();
        return view('organization.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Category::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->organization_id !== auth()->user()->organization_id, 403);
        $request->validate(['name' => 'required|string|max:255']);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->organization_id !== auth()->user()->organization_id, 403);
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category with active products.');
        }
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
