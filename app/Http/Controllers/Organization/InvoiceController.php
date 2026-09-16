<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\LocationManager;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:invoices.create', only: ['create', 'store', 'apiProductSearch']),
            new Middleware('permission:invoices.cancel', only: ['cancel']),
            new Middleware('permission:invoices.download', only: ['print', 'receipt']),
        ];
    }
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

        // Subscription monthly quota calculation
        $orgId = auth()->user()->organization_id;
        $monthlyCount = Invoice::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $maxInvoices = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_invoices_per_month');
        $limitReached = \App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_invoices_per_month', $monthlyCount);

        // Calculate KPI stats for active location using database aggregate queries for high performance
        $statsQuery = Invoice::where('organization_id', $orgId)
            ->where('location_id', $locationId);

        $stats = [
            'paid_sum' => (clone $statsQuery)->where('status', 'Paid')->sum('grand_total'),
            'unpaid_sum' => (clone $statsQuery)->whereIn('status', ['Due', 'Partially Paid', 'Overdue'])->sum(\Illuminate\Support\Facades\DB::raw('grand_total - amount_paid')),
            'overdue_count' => (clone $statsQuery)->where('status', 'Overdue')->count(),
            'total_count' => (clone $statsQuery)->count(),
            'total_invoiced' => (clone $statsQuery)->sum('grand_total'),
        ];

        $invoices = $query->with('client')->latest()->paginate(15)->withQueryString();
        return view('organization.invoices.index', compact('invoices', 'stats', 'monthlyCount', 'maxInvoices', 'limitReached'));
    }

    public function create()
    {
        return view('organization.invoices.create');
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $monthlyCount = Invoice::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if (\App\Services\SubscriptionService::hasReachedLimit($orgId, 'max_invoices_per_month', $monthlyCount)) {
            $limit = \App\Services\SubscriptionService::getFeatureValue($orgId, 'max_invoices_per_month');
            return response()->json([
                'success' => false,
                'message' => "Monthly invoice limit reached for your plan ({$monthlyCount}/{$limit}). Please upgrade your subscription plan to generate more invoices."
            ], 422);
        }

        $request->validate([

            'client_id' => 'nullable|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:fixed,percent,percentage',
            'discount_value' => 'nullable|numeric|min:0',
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

    public function receipt(Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        $invoice->load(['client', 'items.product', 'organization', 'location']);
        return view('organization.invoices.receipt', compact('invoice'));
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

    public function finalizeDraft(Request $request, Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        
        try {
            $targetStatus = $request->input('target_status', 'Due');
            InvoiceService::finalizeDraft($invoice, $targetStatus);
            return back()->with('success', "Draft invoice #{$invoice->invoice_number} successfully converted to official bill ({$invoice->status}) and inventory stock deducted.");
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
