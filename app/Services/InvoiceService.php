<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;
use App\Services\LocationManager;

class InvoiceService
{
    /**
     * Creates an invoice safely. Only accepts product_id and quantity,
     * recalculates everything server-side.
     */
    public static function createInvoice(array $data)
    {
        $organizationId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();
        
        if (!$locationId) {
            throw new \Exception("No active location selected.");
        }

        return DB::transaction(function () use ($data, $organizationId, $locationId) {
            
            // 1. Generate Invoice Number
            $prefix = 'INV-';
            $latest = Invoice::where('organization_id', $organizationId)->latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $invoiceNumber = $prefix . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // 2. Initialize totals
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = $data['discount'] ?? 0;
            
            $itemsData = [];

            // 3. Process items securely
            foreach ($data['items'] as $item) {
                if (isset($item['product_id'])) {
                    $product = Product::where('organization_id', $organizationId)->findOrFail($item['product_id']);
                    
                    if (($data['status'] ?? 'Draft') !== 'Draft' && $product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    
                    $name = $product->name;
                    $price = $product->selling_price;
                    $taxRate = $product->tax_rate ?? 0;
                    $productId = $product->id;
                    $stockProduct = $product;
                } else {
                    // For Restaurant Orders or custom items
                    $name = $item['name'];
                    $price = $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $productId = null;
                    $stockProduct = null;
                }

                $quantity = $item['quantity'];
                
                $lineTotalBase = $price * $quantity;
                $lineTax = ($lineTotalBase * $taxRate) / 100;
                $lineTotal = $lineTotalBase + $lineTax;

                $subtotal += $lineTotalBase;
                $totalTax += $lineTax;

                $itemsData[] = [
                    'product_id' => $productId,
                    'stock_product' => $stockProduct,
                    'snapshot' => $name,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'tax' => $lineTax,
                    'total' => $lineTotal
                ];
            }

            $grandTotal = $subtotal + $totalTax - $totalDiscount;
            
            // 4. Create Invoice Record
            $invoice = Invoice::create([
                'organization_id' => $organizationId,
                'location_id' => $locationId,
                'client_id' => $data['client_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? now()->addDays(7)->toDateString(),
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'discount' => $totalDiscount,
                'grand_total' => $grandTotal,
                'amount_paid' => $data['amount_paid'] ?? 0,
                'status' => $data['status'] ?? 'Draft',
                'notes' => $data['notes'] ?? null,
            ]);

            // 5. Create Items & Deduct Stock
            foreach ($itemsData as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'],
                    'product_name_snapshot' => $itemData['snapshot'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax' => $itemData['tax'],
                    'discount' => 0, // item level discount not implemented yet
                    'total' => $itemData['total'],
                ]);

                // Deduct stock if not Draft and product exists
                if ($invoice->status !== 'Draft' && $itemData['stock_product']) {
                    InventoryService::adjustStock(
                        $itemData['stock_product'], 
                        $locationId, 
                        -abs($itemData['quantity']), 
                        'out', 
                        "Invoice generated: {$invoice->invoice_number}"
                    );
                }
            }

            return $invoice;
        });
    }

    /**
     * Cancel invoice safely, restoring stock.
     */
    public static function cancelInvoice(Invoice $invoice)
    {
        if ($invoice->status === 'Cancelled') {
            return;
        }

        DB::transaction(function () use ($invoice) {
            if ($invoice->status !== 'Draft') {
                // Restore stock
                foreach ($invoice->items as $item) {
                    if ($item->product) { // if not deleted
                        InventoryService::adjustStock(
                            $item->product, 
                            $invoice->location_id, 
                            abs($item->quantity), 
                            'in', 
                            "Invoice cancelled: {$invoice->invoice_number}"
                        );
                    }
                }
            }

            // Reverse Payments and Issue Refunds if applicable
            $gatewayPayments = \App\Models\GatewayPayment::where('entity_type', Invoice::class)
                ->where('entity_id', $invoice->id)
                ->where('status', 'captured')
                ->get();
                
            foreach ($gatewayPayments as $payment) {
                try {
                    \App\Services\RazorpayPaymentService::issueRefund($payment);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Automated refund failed for Invoice ' . $invoice->id . ': ' . $e->getMessage());
                }
            }

            $invoice->update(['status' => 'Cancelled']);
        });
    }
}
