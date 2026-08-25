<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Client;
use App\Notifications\ComplaintAssignedNotification;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $query = Complaint::where('organization_id', $orgId)->with(['client', 'reporter', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();
        $employees = Employee::where('organization_id', $orgId)->get();
        $clients = Client::where('organization_id', $orgId)->get();

        return view('organization.complaints.index', compact('complaints', 'employees', 'clients'));
    }

    public function create()
    {
        $orgId = auth()->user()->organization_id;
        $employees = Employee::where('organization_id', $orgId)->get();
        $clients = Client::where('organization_id', $orgId)->get();
        return view('organization.complaints.create', compact('employees', 'clients'));
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'client_id' => 'nullable|exists:clients,id',
            'assigned_to' => 'nullable|exists:employees,id'
        ]);

        $complaint = Complaint::create([
            'organization_id' => $orgId,
            'client_id' => $request->client_id,
            'employee_id' => auth()->user()->employee ? auth()->user()->employee->id : null,
            'assigned_to' => $request->assigned_to,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'Open',
        ]);

        if ($complaint->assigned_to) {
            $assigneeUser = $complaint->assignee->user;
            if ($assigneeUser) {
                $assigneeUser->notify(new ComplaintAssignedNotification($complaint));
            }
        }

        return redirect()->route('organization.complaints.index')->with('success', 'Complaint logged successfully.');
    }

    public function show(Complaint $complaint)
    {
        abort_if($complaint->organization_id !== auth()->user()->organization_id, 403);
        $complaint->load(['client', 'reporter', 'assignee']);
        $employees = Employee::where('organization_id', $complaint->organization_id)->get();
        
        return view('organization.complaints.show', compact('complaint', 'employees'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        abort_if($complaint->organization_id !== auth()->user()->organization_id, 403);
        
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'assigned_to' => 'nullable|exists:employees,id'
        ]);

        $oldAssignee = $complaint->assigned_to;
        
        $complaint->update([
            'status' => $request->status,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to
        ]);

        if ($request->assigned_to && $request->assigned_to != $oldAssignee) {
            $assigneeUser = $complaint->assignee->user;
            if ($assigneeUser) {
                $assigneeUser->notify(new ComplaintAssignedNotification($complaint));
            }
        }

        return back()->with('success', 'Complaint updated successfully.');
    }
}
