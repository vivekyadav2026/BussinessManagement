<?php

namespace App\Services\Reminders\Channels;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel implements ReminderChannelInterface
{
    public function send(Invoice $invoice): array
    {
        if (!$invoice->client->phone) {
            return [
                'success' => false,
                'message' => 'Client does not have a phone number.'
            ];
        }

        // WhatsApp is explicitly left as an architectural placeholder per instructions.
        Log::info("Attempted to send WhatsApp reminder for Invoice {$invoice->invoice_number}. Provider not configured.");

        return [
            'success' => false,
            'message' => 'WhatsApp Provider (e.g., Meta API / Twilio) is not configured yet. Service placeholder reached.'
        ];
    }
}
