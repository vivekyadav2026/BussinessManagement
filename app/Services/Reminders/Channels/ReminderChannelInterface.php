<?php

namespace App\Services\Reminders\Channels;

use App\Models\Invoice;

interface ReminderChannelInterface
{
    /**
     * Send a reminder for the given invoice.
     * 
     * @param Invoice $invoice
     * @return array ['success' => bool, 'message' => string]
     */
    public function send(Invoice $invoice): array;
}
