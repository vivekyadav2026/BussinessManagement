<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;
use App\Services\DocumentService;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $channels;

    public function __construct(Invoice $invoice, array $channels = ['mail'])
    {
        $this->invoice = $invoice;
        $this->channels = $channels;
    }

    public function via($notifiable)
    {
        $resolvedChannels = [];
        foreach ($this->channels as $channel) {
            if ($channel === 'whatsapp') {
                $resolvedChannels[] = \App\Notifications\Channels\WhatsAppChannel::class;
            } else {
                $resolvedChannels[] = $channel;
            }
        }
        return $resolvedChannels;
    }

    public function toMail($notifiable)
    {
        $pdf = DocumentService::generateInvoicePdf($this->invoice);
        
        return (new MailMessage)
                    ->subject('Invoice #' . $this->invoice->invoice_number . ' from ' . $this->invoice->organization->name)
                    ->greeting('Hello ' . ($notifiable->name ?? 'Customer') . ',')
                    ->line('Please find attached your invoice.')
                    ->line('Total Amount: ' . number_format($this->invoice->grand_total, 2))
                    ->line('Amount Paid: ' . number_format($this->invoice->amount_paid, 2))
                    ->line('Balance Due: ' . number_format($this->invoice->grand_total - $this->invoice->amount_paid, 2))
                    ->line('Payment Status: ' . $this->invoice->status)
                    ->attachData($pdf->output(), 'invoice_' . $this->invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }

    public function toWhatsApp($notifiable)
    {
        $balance = number_format($this->invoice->grand_total - $this->invoice->amount_paid, 2);
        
        return "Hello {$notifiable->name}, your invoice #{$this->invoice->invoice_number} for {$this->invoice->organization->name} has been generated.\n" .
               "Total: {$this->invoice->grand_total}\n" .
               "Balance Due: {$balance}\n" .
               "Status: {$this->invoice->status}\n\n" .
               "Thank you!";
    }
}
