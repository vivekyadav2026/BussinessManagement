<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::where('organization_id', auth()->user()->organization_id)->get();
        
        return view('organization.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('organization_id', auth()->user()->organization_id)->get();
        return view('organization.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $sku = $request->sku ?: InventoryService::generateSku(auth()->user()->organization_id, $request->category_id, $request->name);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'organization_id' => auth()->user()->organization_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $sku,
            'barcode' => $request->barcode,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price ?? 0,
            'selling_price' => $request->selling_price,
            'tax_rate' => $request->tax_rate ?? 0,
            'min_stock_level' => $request->min_stock_level ?? 0,
            'image_path' => $imagePath,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('organization.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        $categories = Category::where('organization_id', auth()->user()->organization_id)->get();
        return view('organization.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku ?: $product->sku,
            'barcode' => $request->barcode,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price ?? 0,
            'selling_price' => $request->selling_price,
            'tax_rate' => $request->tax_rate ?? 0,
            'min_stock_level' => $request->min_stock_level ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('organization.products.index')->with('success', 'Product updated successfully.');
    }
}
