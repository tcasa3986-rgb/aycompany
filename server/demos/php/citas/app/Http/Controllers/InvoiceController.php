<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['appointment.patient', 'appointment.doctor'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'cancelled' => Invoice::where('status', 'cancelled')->count(),
            'revenue' => Invoice::where('status', 'paid')->sum('amount'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create(Request $request)
    {
        // Preselect appointment if provided via query param
        $selectedAppointment = null;
        if ($request->has('appointment_id')) {
            $selectedAppointment = Appointment::with(['patient', 'doctor'])
                ->findOrFail($request->appointment_id);
        }

        // Only show completed appointments that do NOT have an invoice yet
        $appointments = Appointment::with(['patient.insurance', 'doctor'])
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->orderByDesc('date')
            ->get();

        $insurances = \App\Models\Insurance::orderBy('name')->get();

        return view('invoices.create', compact('appointments', 'selectedAppointment', 'insurances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id|unique:invoices,appointment_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string|max:500',
            'insurance_id' => 'nullable|exists:insurances,id',
            'insurance_coverage_amount' => 'nullable|numeric|min:0',
            'patient_copay_amount' => 'nullable|numeric|min:0',
        ]);

        Invoice::create($validated);

        return redirect()->route('invoices.index')
            ->with('success', 'Factura registrada correctamente.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['appointment.patient', 'appointment.doctor', 'appointment.specialty']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['appointment.patient.insurance', 'appointment.doctor']);
        $insurances = \App\Models\Insurance::orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'insurances'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string|max:500',
            'insurance_id' => 'nullable|exists:insurances,id',
            'insurance_coverage_amount' => 'nullable|numeric|min:0',
            'patient_copay_amount' => 'nullable|numeric|min:0',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'Factura eliminada.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['appointment.patient', 'appointment.doctor', 'appointment.specialty', 'insurance']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $filename = 'factura-' . $invoice->id . '.pdf';
        return $pdf->download($filename);
    }
}
