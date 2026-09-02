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
        $locationId = LocationManager::getActiveLocationId();
        
        // Base query for KPI Cards (Outstanding balances)
        $unpaidBase = Invoice::where('organization_id', $orgId)
            ->when($locationId, function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })
            ->whereNotIn('status', ['Cancelled', 'Paid']);

        $totalOutstanding = (clone $unpaidBase)->get()->sum('amount_due');
        $totalOverdue = (clone $unpaidBase)->where('due_date', '<', now()->startOfDay())->get()->sum('amount_due');
        $dueToday = (clone $unpaidBase)->whereDate('due_date', now()->toDateString())->get()->sum('amount_due');
        $dueThisWeek = (clone $unpaidBase)->whereBetween('due_date', [now()->toDateString(), now()->endOfWeek()->toDateString()])->get()->sum('amount_due');

        // Dynamic Invoice Query for Table
        $listQuery = Invoice::where('organization_id', $orgId)
            ->when($locationId, function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })
            ->where('status', '!=', 'Cancelled');

        if ($request->filled('client_id')) {
            $listQuery->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Overdue') {
                $listQuery->whereNotIn('status', ['Cancelled', 'Paid'])
                          ->where('due_date', '<', now()->startOfDay());
            } elseif ($request->status === 'ALL') {
                // Show all non-cancelled invoices
            } else {
                $listQuery->where('status', $request->status);
            }
        } else {
            // Default when no status filter selected: show unpaid invoices
            $listQuery->whereNotIn('status', ['Paid']);
        }
        
        $invoices = $listQuery->with('client')->latest('due_date')->paginate(15)->withQueryString();
        
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
