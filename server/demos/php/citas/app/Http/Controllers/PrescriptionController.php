<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient.user', 'doctor.user']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $prescriptions = $query->latest('date')->paginate(15)->withQueryString();
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();

        return view('prescriptions.index', compact('prescriptions', 'patients', 'doctors'));
    }

    public function create(Request $request)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();

        $selectedPatient = $request->query('patient_id');
        $selectedDoctor = $request->query('doctor_id');

        return view('prescriptions.create', compact('patients', 'doctors', 'selectedPatient', 'selectedDoctor'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string|max:255',
            'items.*.dosage' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.instructions' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $prescription = Prescription::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['items'] as $item) {
                $prescription->items()->create($item);
            }
        });

        return redirect()->route('prescriptions.index')->with('success', 'Receta médica creada exitosamente.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient.user', 'doctor.user', 'items']);
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $prescription->load(['patient.user', 'doctor.user', 'items']);
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();

        return view('prescriptions.edit', compact('prescription', 'patients', 'doctors'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string|max:255',
            'items.*.dosage' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.instructions' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($prescription, $validated) {
            $prescription->update([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            // Recreate all items for simplicity
            $prescription->items()->delete();

            foreach ($validated['items'] as $item) {
                $prescription->items()->create($item);
            }
        });

        return redirect()->route('prescriptions.index')->with('success', 'Receta médica actualizada exitosamente.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'Receta médica eliminada.');
    }

    public function exportPdf(Prescription $prescription)
    {
        $prescription->load(['patient.user', 'doctor.user', 'items']);
        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'));

        $dateStr = \Carbon\Carbon::parse($prescription->date)->format('Y-m-d');
        $filename = 'receta_' . str_replace(' ', '_', $prescription->patient->user->name) . '_' . $dateStr . '.pdf';

        return $pdf->download($filename);
    }
}
