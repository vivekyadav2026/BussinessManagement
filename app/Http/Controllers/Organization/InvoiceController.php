<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\LocationManager;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $locationId = LocationManager::getActiveLocationId();
        
        $query = Invoice::with(['client'])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('location_id', $locationId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Calculate KPI stats for active location
        $statsBase = Invoice::where('organization_id', auth()->user()->organization_id)
            ->where('location_id', $locationId)
            ->get();

        $stats = [
            'paid_sum' => $statsBase->where('status', 'Paid')->sum('grand_total'),
            'unpaid_sum' => $statsBase->filter(fn($i) => in_array($i->status, ['Due', 'Partially Paid', 'Overdue']))->sum(fn($i) => $i->amount_due),
            'overdue_count' => $statsBase->where('status', 'Overdue')->count(),
            'total_count' => $statsBase->count()
        ];

        $invoices = $query->latest()->paginate(15);
        return view('organization.invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        return view('organization.invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:Draft,Paid,Partially Paid,Due',
            'notes' => 'nullable|string'
        ]);

        try {
            $invoice = InvoiceService::createInvoice($request->all());
            return response()->json([
                'success' => true,
                'redirect' => route('organization.invoices.show', $invoice)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        $invoice->load(['client', 'items.product']);
        return view('organization.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        $invoice->load(['client', 'items.product']);
        return view('organization.invoices.print', compact('invoice'));
    }

    public function cancel(Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        
        try {
            InvoiceService::cancelInvoice($invoice);
            return back()->with('success', 'Invoice cancelled successfully and stock restored.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function apiProductSearch(Request $request)
    {
        $search = $request->q;
        
        $products = Product::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'sku', 'barcode', 'selling_price', 'tax_rate')
            ->limit(10)
            ->get()
            ->map(function($product) {
                $product->current_stock = $product->stock;
                return $product;
            });
            
        return response()->json($products);
    }
}
