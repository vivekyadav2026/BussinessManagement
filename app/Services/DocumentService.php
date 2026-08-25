<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentService
{
    /**
     * Generate an Invoice PDF.
     */
    public static function generateInvoicePdf(Invoice $invoice)
    {
        $invoice->load(['organization', 'client', 'items', 'location']);

        $pdf = Pdf::loadView('documents.invoice', [
            'invoice' => $invoice,
            'organization' => $invoice->organization,
            'client' => $invoice->client,
            'location' => $invoice->location,
        ]);

        return $pdf;
    }

    /**
     * Generate a Payslip PDF.
     */
    public static function generatePayslipPdf(Payroll $payroll)
    {
        $payroll->load(['organization', 'employee']);

        $pdf = Pdf::loadView('documents.payslip', [
            'payroll' => $payroll,
            'organization' => $payroll->organization,
            'employee' => $payroll->employee,
        ]);

        return $pdf;
    }
}
