<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:products.create', only: ['store']),
            new Middleware('permission:products.edit', only: ['update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        // KPI metrics
        $allCategories = Category::where('organization_id', $orgId)->withCount('products')->get();
        $totalCategories = $allCategories->count();
        $totalCategorizedProducts = $allCategories->sum('products_count');
        $emptyCategoriesCount = $allCategories->where('products_count', 0)->count();
        $topCategory = $allCategories->sortByDesc('products_count')->first();

        // Query with search
        $query = Category::where('organization_id', $orgId)->withCount('products');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(15)->withQueryString();
        
        return view('organization.categories.index', compact(
            'categories',
            'totalCategories',
            'totalCategorizedProducts',
            'emptyCategoriesCount',
            'topCategory'
        ));
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
