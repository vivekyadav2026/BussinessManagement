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

        try {
            \App\Services\CommunicationService::sendInvoice($invoice, ['mail']);
            Log::info("Email invoice PDF dispatched for Invoice {$invoice->invoice_number} to {$invoice->client->email}");

            return [
                'success' => true,
                'message' => 'Invoice email sent successfully to ' . $invoice->client->email
            ];
        } catch (\Exception $e) {
            Log::error("Failed to send invoice email: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ];
        }
    }
}
