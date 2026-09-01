<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Services\LocationManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    public function dashboard(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        
        // Base query for all metrics
        $query = Invoice::where('organization_id', $orgId)
            ->whereNotIn('status', ['Cancelled', 'Paid']);
            
        // Apply Location Filter
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        } else {
            // Default to all allowed locations for this user? The request asked for a filter. 
            // If no filter, we can show across the org, or restrict to active location. 
            // Let's restrict to active location to maintain the paradigm, but allow "All Locations" if Super/Org Admin.
            // For now, let's keep it bound to the active location unless overridden.
            $query->where('location_id', LocationManager::getActiveLocationId());
        }

        // Global KPIs
        $kpiQuery = clone $query;
        $totalOutstanding = $kpiQuery->sum('grand_total') - $kpiQuery->sum('amount_paid');
        
        $kpiQuery = clone $query;
        $totalOverdue = $kpiQuery->where('due_date', '<', now()->startOfDay())->get()->sum('amount_due');
        
        $kpiQuery = clone $query;
        $dueToday = $kpiQuery->whereDate('due_date', now()->toDateString())->get()->sum('amount_due');
        
        $kpiQuery = clone $query;
        $dueThisWeek = $kpiQuery->whereBetween('due_date', [now()->toDateString(), now()->endOfWeek()->toDateString()])->get()->sum('amount_due');

        // Invoice List with dynamic filtering
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'Overdue') {
                $query->where('due_date', '<', now()->startOfDay());
            } else {
                $query->where('status', $request->status);
            }
        }
        
        $invoices = $query->with('client')->latest('due_date')->paginate(15);
        
        $clients = Client::where('organization_id', $orgId)->orderBy('name')->get();

        return view('organization.receivables.index', compact(
            'totalOutstanding', 'totalOverdue', 'dueToday', 'dueThisWeek', 'invoices', 'clients'
        ));
    }

    public function clientReport(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        $clients = Client::where('organization_id', $orgId)
            ->with(['invoices' => function($q) use ($locationId) {
                $q->whereNotIn('status', ['Cancelled', 'Paid'])
                  ->when($locationId, function($lq) use ($locationId) {
                      $lq->where('location_id', $locationId);
                  });
            }])
            ->get()
            ->map(function($client) {
                $client->total_outstanding = $client->invoices->sum(function($inv) {
                    return $inv->amount_due;
                });
                $client->total_overdue = $client->invoices->where('due_date', '<', now()->startOfDay())->sum(function($inv) {
                    return $inv->amount_due;
                });
                $client->invoice_count = $client->invoices->count();
                return $client;
            })
            ->filter(function($client) {
                return $client->total_outstanding > 0;
            })
            ->sortByDesc('total_outstanding');

        return view('organization.receivables.client_report', compact('clients'));
    }

    public function overdueReport(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();
        
        $invoices = Invoice::with('client')
            ->where('organization_id', $orgId)
            ->when($locationId, function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })
            ->whereNotIn('status', ['Cancelled', 'Paid'])
            ->where('due_date', '<', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->paginate(20);
            
        return view('organization.receivables.overdue_report', compact('invoices'));
    }

}
