<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Client;
use App\Models\Organization;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class PublicComplaintController extends Controller
{
    public function create(Request $request)
    {
        $orgId = $request->query('org_id');
        $invoiceId = $request->query('invoice_id');
        
        $selectedOrg = $orgId ? Organization::find($orgId) : null;
        $invoice = $invoiceId ? Invoice::with('client')->find($invoiceId) : null;

        if ($invoice && !$selectedOrg) {
            $selectedOrg = $invoice->organization;
        }

        $organizations = Organization::where('is_active', true)->get(['id', 'name']);

        return view('public.complaint', compact('organizations', 'selectedOrg', 'invoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['nullable', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'nullable|in:Low,Medium,High,Urgent',
        ], [
            'phone.regex' => 'Please enter a valid 10-digit mobile number.',
            'description.min' => 'Please provide a detailed description (at least 10 characters).',
        ]);

        $orgId = $request->organization_id;

        // Find existing client or create new client record
        $client = Client::where('organization_id', $orgId)
            ->where(function($q) use ($request) {
                $q->where('email', $request->email);
                if ($request->phone) {
                    $q->orWhere('phone', $request->phone);
                }
            })->first();

        if (!$client) {
            $client = Client::create([
                'organization_id' => $orgId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => true,
            ]);
        }

        $complaint = Complaint::create([
            'organization_id' => $orgId,
            'client_id' => $client->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority ?: 'Medium',
            'status' => 'Open',
        ]);

        Log::info("Public complaint #{$complaint->id} registered by client {$client->email} for Org #{$orgId}");

        return redirect()->route('public.complaint.success', $complaint->id)
            ->with('success', 'Your complaint ticket has been submitted successfully.');
    }

    public function success(Complaint $complaint)
    {
        $complaint->load(['organization', 'client']);
        return view('public.complaint_success', compact('complaint'));
    }
}
