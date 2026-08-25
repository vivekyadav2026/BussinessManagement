<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\RestaurantOrder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvoiceNotification;
use App\Notifications\PayslipNotification;
use App\Notifications\RestaurantOrderNotification;

class CommunicationService
{
    /**
     * Send Invoice Notification (Email, WhatsApp)
     */
    public static function sendInvoice(Invoice $invoice, array $channels = ['mail'])
    {
        // For walk-in customers with no email/phone, we skip
        if (!$invoice->client) {
            return false;
        }

        $invoice->client->notify(new InvoiceNotification($invoice, $channels));
        
        return true;
    }

    /**
     * Send Payslip Notification (Email)
     */
    public static function sendPayslip(Payroll $payroll, array $channels = ['mail'])
    {
        if (!$payroll->employee || !$payroll->employee->user) {
            // Employee doesn't have a linked user account to receive emails, 
            // Or we could send directly if Employee model has an email field.
            return false;
        }

        // Assuming employee has email
        Notification::route('mail', $payroll->employee->email ?? $payroll->employee->user->email)
            ->notify(new PayslipNotification($payroll, $channels));
            
        return true;
    }

    /**
     * Send Restaurant Order Notification
     * type: 'confirmation', 'status_update', 'payment'
     */
    public static function sendRestaurantOrderNotification(RestaurantOrder $order, $type = 'status_update')
    {
        if (!$order->customer_phone && !$order->customer_email) {
            return false; // No contact info provided by walk-in/QR customer
        }
        
        // Notify via Anonymous Notifiable
        $route = Notification::route('WhatsApp', $order->customer_phone);
        
        // Optionally route to mail if email exists
        if (isset($order->customer_email)) {
            $route->route('mail', $order->customer_email);
        }

        $route->notify(new RestaurantOrderNotification($order, $type));

        return true;
    }
}
