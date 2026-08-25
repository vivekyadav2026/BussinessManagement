<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Services\Reminders\ReminderManager;
use Illuminate\Support\Facades\URL;

class ReminderController extends Controller
{
    public function send(Request $request, Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'channel' => 'required|in:email,whatsapp'
        ]);

        try {
            $result = ReminderManager::send($invoice, $request->channel);
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    public function generateLink(Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);
        
        // Generate a signed URL that expires in 30 days
        $url = URL::temporarySignedRoute(
            'public.invoice.pay', now()->addDays(30), ['invoice' => $invoice->id]
        );

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
