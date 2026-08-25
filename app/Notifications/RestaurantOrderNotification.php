<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RestaurantOrder;

class RestaurantOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $type;

    public function __construct(RestaurantOrder $order, $type = 'status_update')
    {
        $this->order = $order;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', \App\Notifications\Channels\WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Order ' . $this->order->order_number . ' Update - ' . $this->order->organization->name)
            ->greeting('Hello ' . ($this->order->customer_name ?? 'Customer') . ',');

        if ($this->type === 'confirmation') {
            $message->line('Your order has been received successfully.');
        } elseif ($this->type === 'payment') {
            $message->line('We have received your payment for order #' . $this->order->order_number . '.');
        } else {
            $message->line('Your order status has been updated to: ' . $this->order->status);
        }

        $message->line('Total Amount: ' . number_format($this->order->total, 2));

        return $message;
    }

    public function toWhatsApp($notifiable)
    {
        $greeting = "Hello " . ($this->order->customer_name ?? "Customer");
        
        if ($this->type === 'confirmation') {
            $text = "Your order #{$this->order->order_number} has been received.";
        } elseif ($this->type === 'payment') {
            $text = "Payment confirmed for your order #{$this->order->order_number}.";
        } else {
            $text = "Your order #{$this->order->order_number} status is now: {$this->order->status}.";
        }

        return "{$greeting}, {$text} Total: " . number_format($this->order->total, 2) . " from " . $this->order->organization->name;
    }
}
