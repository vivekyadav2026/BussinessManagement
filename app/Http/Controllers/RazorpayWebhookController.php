<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RazorpayPaymentService;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (!$signature) {
            return response()->json(['error' => 'No signature found'], 400);
        }

        try {
            RazorpayPaymentService::processWebhook($payload, $signature);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
