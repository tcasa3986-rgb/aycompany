<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends CashierController
{
    /**
     * Handle checkout session completed.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCheckoutSessionCompleted(array $payload)
    {
        $session = $payload['data']['object'];

        // We can extract custom data if we passed it in the session metadata, 
        // or rely on Cashier's default relations. 
        // Cashier typically links payments to the Billable model (User).
        // Let's check if the session contains the client_reference_id for the Invoice.

        $invoiceId = $session['client_reference_id'] ?? null;

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->status !== 'paid') {
                $invoice->update([
                    'status' => 'paid',
                ]);
                Log::info("Stripe Webhook: Invoice {$invoiceId} marked as paid from session {$session['id']}");
            }
        } else {
            Log::warning("Stripe Webhook: CheckoutSession {$session['id']} completed but no client_reference_id found.");
        }

        return $this->successMethod();
    }

    /**
     * Handle payment intent succeeded.
     * Use this if you want to capture standard charges outside of Checkout Sessions.
     */
    protected function handlePaymentIntentSucceeded(array $payload)
    {
        $intent = $payload['data']['object'];

        // Similarly, attempt to find associated invoice from metadata if present
        $invoiceId = $intent['metadata']['invoice_id'] ?? null;

        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->status !== 'paid') {
                $invoice->update([
                    'status' => 'paid',
                ]);
                Log::info("Stripe Webhook: Invoice {$invoiceId} marked as paid from PaymentIntent {$intent['id']}");
            }
        }

        return $this->successMethod();
    }
}
