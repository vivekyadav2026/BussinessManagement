<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::where('organization_id', auth()->user()->organization_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15);
        return view('organization.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('organization.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        Client::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'gst_number' => $request->gst_number,
            'notes' => $request->notes,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('organization.clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        abort_if($client->organization_id !== auth()->user()->organization_id, 403);
        return view('organization.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        abort_if($client->organization_id !== auth()->user()->organization_id, 403);
        return view('organization.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        abort_if($client->organization_id !== auth()->user()->organization_id, 403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $client->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'gst_number' => $request->gst_number,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('organization.clients.index')->with('success', 'Client updated successfully.');
    }

    public function apiSearch(Request $request)
    {
        $search = $request->q;
        
        $clients = Client::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'phone')
            ->limit(10)
            ->get();
            
        return response()->json($clients);
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $client = Client::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone
            ]
        ]);
    }

    public function destroy(Client $client)
    {
        abort_if($client->organization_id !== auth()->user()->organization_id, 403);
        if ($client->invoices()->count() > 0) {
            return redirect()->route('organization.clients.index')->with('error', 'Cannot delete client who has associated invoices.');
        }
        $client->delete();
        return redirect()->route('organization.clients.index')->with('success', 'Client deleted successfully.');
    }
}
