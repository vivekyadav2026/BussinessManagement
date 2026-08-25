<?php

namespace App\Services\Reminders;

use App\Models\Invoice;
use App\Services\Reminders\Channels\EmailChannel;
use App\Services\Reminders\Channels\WhatsAppChannel;

class ReminderManager
{
    /**
     * Dispatch a reminder via the specified channel.
     */
    public static function send(Invoice $invoice, string $channel): array
    {
        $channelInstance = match (strtolower($channel)) {
            'email' => new EmailChannel(),
            'whatsapp' => new WhatsAppChannel(),
            default => throw new \InvalidArgumentException("Unsupported reminder channel: {$channel}"),
        };

        return $channelInstance->send($invoice);
    }
}
