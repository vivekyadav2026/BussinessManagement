<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payroll;
use App\Services\DocumentService;

class PayslipNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payroll;
    public $channels;

    public function __construct(Payroll $payroll, array $channels = ['mail'])
    {
        $this->payroll = $payroll;
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
        $pdf = DocumentService::generatePayslipPdf($this->payroll);
        
        $month = date("F", mktime(0, 0, 0, $this->payroll->month, 10)) . ' ' . $this->payroll->year;

        return (new MailMessage)
                    ->subject('Payslip for ' . $month)
                    ->greeting('Hello ' . ($this->payroll->employee->first_name ?? 'Employee') . ',')
                    ->line('Please find attached your payslip for ' . $month . '.')
                    ->line('Net Salary: ' . number_format($this->payroll->net_salary, 2))
                    ->attachData($pdf->output(), 'payslip_' . $month . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }

    public function toWhatsApp($notifiable)
    {
        $month = date("F", mktime(0, 0, 0, $this->payroll->month, 10)) . ' ' . $this->payroll->year;
        return "Hello {$this->payroll->employee->first_name}, your payslip for {$month} has been generated. Net Salary: " . number_format($this->payroll->net_salary, 2);
    }
}
