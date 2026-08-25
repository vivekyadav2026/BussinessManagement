<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        
        $phone = $notifiable->routeNotificationFor('WhatsApp') 
                 ?? $notifiable->phone 
                 ?? null;

        if (!$phone) {
            Log::warning("Cannot send WhatsApp: No phone number for notifiable.", ['notifiable' => get_class($notifiable)]);
            return;
        }

        // Faking external API call by logging
        Log::info("FAKE WHATSAPP DISPATCHED to {$phone}: {$message}");
        
        // Return a mock success or do nothing
    }
}
