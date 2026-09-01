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
        $orgId = auth()->user()->organization_id;
        $currentProductsCount = Product::where('organization_id', $orgId)->count();

        if (\App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_products', $currentProductsCount)) {
            $limit = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_products');
            return back()->withInput()->with('error', "Product inventory limit reached ({$currentProductsCount}/{$limit}). Please upgrade your plan to add more products.");
        }

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
            'images.*' => 'nullable|image|max:2048',
        ]);

        $sku = $request->sku ?: InventoryService::generateSku(auth()->user()->organization_id, $request->category_id, $request->name);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = \App\Services\ImageOptimizer::compressAndStore($request->file('image'), 'products', 'public', 1200, 75);
        }

        $product = Product::create([
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

        // Upload compressed multi-image gallery
        if ($request->hasFile('images')) {
            $order = 0;
            foreach ($request->file('images') as $galleryFile) {
                $path = \App\Services\ImageOptimizer::compressAndStore($galleryFile, 'products/gallery', 'public', 1200, 75);
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $order++,
                ]);
                if (!$product->image_path && $order === 1) {
                    $product->update(['image_path' => $path]);
                }
            }
        }

        return redirect()->route('organization.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        $product->load('images');
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
            'images.*' => 'nullable|image|max:2048',
            'delete_images' => 'nullable|array',
        ]);

        // Delete selected gallery images
        if ($request->filled('delete_images')) {
            $imagesToDelete = \App\Models\ProductImage::where('product_id', $product->id)
                ->whereIn('id', $request->delete_images)
                ->get();

            foreach ($imagesToDelete as $imgToDelete) {
                Storage::disk('public')->delete($imgToDelete->image_path);
                $imgToDelete->delete();
            }
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = \App\Services\ImageOptimizer::compressAndStore($request->file('image'), 'products', 'public', 1200, 75);
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

        // Upload additional compressed gallery images
        if ($request->hasFile('images')) {
            $currentOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $galleryFile) {
                $currentOrder++;
                $path = \App\Services\ImageOptimizer::compressAndStore($galleryFile, 'products/gallery', 'public', 1200, 75);
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $currentOrder,
                ]);
            }
        }


        return redirect()->route('organization.products.index')->with('success', 'Product updated successfully.');
    }

    public function show(Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        $product->load(['category', 'images']);
        return view('organization.products.show', compact('product'));
    }

    public function printBarcode(Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        return view('organization.products.print-barcode', compact('product'));
    }

    public function destroy(Product $product)
    {
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('organization.products.index')->with('success', 'Product deleted successfully.');
    }
}

