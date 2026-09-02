<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Services\LocationManager;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $activeLocationId = LocationManager::getActiveLocationId();
        
        $query = Product::with(['inventoryStocks' => function($q) use ($activeLocationId) {
            $q->where('location_id', $activeLocationId);
        }])->where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20)->withQueryString();
        return view('organization.inventory.index', compact('products'));
    }

    public function scanner()
    {
        return view('organization.inventory.scanner');
    }

    public function processBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);
        
        $raw = trim($request->barcode);
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', $raw);

        $product = Product::where('organization_id', auth()->user()->organization_id)
            ->where(function($q) use ($raw, $clean) {
                $q->where('barcode', $raw)
                  ->orWhere('sku', $raw)
                  ->orWhere('barcode', 'like', "%{$raw}%")
                  ->orWhere('sku', 'like', "%{$raw}%");
                if (!empty($clean)) {
                    $q->orWhereRaw("LOWER(REPLACE(barcode, '-', '')) = ?", [strtolower($clean)])
                      ->orWhereRaw("LOWER(REPLACE(sku, '-', '')) = ?", [strtolower($clean)]);
                }
            })->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => "Product not found for code: {$raw}"], 404);
        }
        
        // Include stock info for the current location
        $stock = clone $product;
        $stock->current_stock = $product->stock;
        
        return response()->json(['success' => true, 'product' => $stock]);
    }


    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|not_in:0',
            'type' => 'required|in:in,out,adjustment',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        abort_if($product->organization_id !== auth()->user()->organization_id, 403);
        
        $locationId = LocationManager::getActiveLocationId();
        abort_if(!$locationId, 403, 'No active location selected.');

        $quantity = clone $request; // just scope
        $quantity = $request->quantity;
        if ($request->type === 'out') {
            $quantity = -abs($quantity);
        } elseif ($request->type === 'in') {
            $quantity = abs($quantity);
        }

        try {
            InventoryService::adjustStock(
                $product, 
                $locationId, 
                $quantity, 
                $request->type, 
                $request->notes
            );

            if ($request->expectsJson()) {
                $product->refresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Stock adjusted successfully.',
                    'new_stock' => $product->stock
                ]);
            }

            return back()->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }


    public function history()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('location_id', LocationManager::getActiveLocationId())
            ->latest()
            ->paginate(30);
            
        return view('organization.inventory.history', compact('movements'));
    }
}
