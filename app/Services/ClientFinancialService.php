<?php

namespace App\Services;

use App\Models\Client;

class ClientFinancialService
{
    /**
     * Get total invoiced amount for the client.
     */
    public static function getTotalPurchased(Client $client)
    {
        return $client->invoices()->where('status', '!=', 'Cancelled')->sum('grand_total');
    }

    public static function getTotalPaid(Client $client)
    {
        return $client->invoices()->where('status', '!=', 'Cancelled')->sum('amount_paid');
    }

    public static function getOutstandingAmount(Client $client)
    {
        $purchased = self::getTotalPurchased($client);
        $paid = self::getTotalPaid($client);
        return max(0, $purchased - $paid);
    }

    public static function getOverdueAmount(Client $client)
    {
        return $client->invoices()
            ->where('status', '!=', 'Cancelled')
            ->where('due_date', '<', now()->startOfDay())
            ->get()
            ->sum(function($invoice) {
                return max(0, $invoice->grand_total - $invoice->amount_paid);
            });
    }
}
