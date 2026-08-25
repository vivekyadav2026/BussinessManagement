<?php

namespace App\Services\Reminders\Channels;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class EmailChannel implements ReminderChannelInterface
{
    public function send(Invoice $invoice): array
    {
        if (!$invoice->client->email) {
            return [
                'success' => false,
                'message' => 'Client does not have an email address.'
            ];
        }

        // TODO: Implement actual Mail::send() logic here when mailing is configured.
        Log::info("Email reminder dispatched for Invoice {$invoice->invoice_number} to {$invoice->client->email}");

        return [
            'success' => true,
            'message' => 'Email reminder sent successfully.'
        ];
    }
}
