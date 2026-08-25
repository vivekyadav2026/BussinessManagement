<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Complaint;
use App\Notifications\Channels\WhatsAppChannel;

class ComplaintAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $complaint;

    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("Ticket Assigned: {$this->complaint->subject}")
                    ->line("You have been assigned a new complaint ticket (Priority: {$this->complaint->priority}).")
                    ->line($this->complaint->description)
                    ->action('View Complaint', url('/organization/complaints/' . $this->complaint->id))
                    ->line('Thank you for providing prompt support!');
    }

    /**
     * Get the array representation of the notification for the database.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'complaint_assigned',
            'complaint_id' => $this->complaint->id,
            'subject' => $this->complaint->subject,
            'priority' => $this->complaint->priority,
            'message' => 'You were assigned a new ticket: ' . $this->complaint->subject
        ];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        return "You have been assigned ticket #{$this->complaint->id}: {$this->complaint->subject}. Priority: {$this->complaint->priority}. Please login to view details.";
    }
}
