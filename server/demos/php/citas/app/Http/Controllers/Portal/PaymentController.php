<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Start Stripe Checkout Session for a specific invoice
     */
    public function checkout(Invoice $invoice)
    {
        $patient = \App\Models\Patient::where('user_id', auth()->id())->first();

        // Ensure invoice belongs to current patient
        if (!$patient || $invoice->appointment->patient_id !== $patient->id) {
            abort(403, 'Acceso denegado a este recibo.');
        }

        if ($invoice->status !== 'pending') {
            return redirect()->route('portal.invoices')->with('error', 'Esta factura no está pendiente de pago o ya ha sido cancelada.');
        }

        // Cashier logic to generate a temporary Stripe check-out session
        // amount is in cents
        $priceInCents = (int) round((float) $invoice->amount * 100);
        $conceptName = 'Pago de Cita Médica #' . $invoice->appointment->id;

        return auth()->user()->checkoutCharge($priceInCents, $conceptName, 1, [
            'success_url' => route('portal.invoices.success', $invoice) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('portal.invoices'),
            'client_reference_id' => $invoice->id,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'appointment_id' => $invoice->appointment_id
            ]
        ]);
    }

    /**
     * Handle the successful redirect from Stripe Checkout
     */
    public function success(Request $request, Invoice $invoice)
    {
        $patient = \App\Models\Patient::where('user_id', auth()->id())->first();
        if (!$patient || $invoice->appointment->patient_id !== $patient->id) {
            abort(403);
        }

        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('portal.invoices')->with('error', 'El pago no pudo ser validado.');
        }

        // Con los webhooks configurados, el webhook en background marcará la factura como pagada.
        // Como contingencia rápida para la UI (en caso de que el webhook se retrase unos ms),
        // comprobamos si está pendiente y la forzamos, o simplemente mostramos el éxito.
        if ($invoice->status === 'pending') {
            $invoice->update([
                'status' => 'paid',
            ]);
        }

        return redirect()->route('portal.invoices')->with('success', '¡Pago procesado con éxito! Tu recibo ha sido saldado.');
    }
}
