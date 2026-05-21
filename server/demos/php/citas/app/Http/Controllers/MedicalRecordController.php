<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    /** Lista de registros clínicos de un paciente */
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient.user', 'doctor.user', 'appointment']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $records = $query->latest('record_date')->paginate(15)->withQueryString();
        $patients = Patient::with('user')->orderBy('id')->get();
        $doctors = Doctor::with('user')->orderBy('id')->get();

        return view('medical-records.index', compact('records', 'patients', 'doctors'));
    }

    /** Formulario de nuevo registro clínico */
    public function create(Request $request)
    {
        $patients = Patient::with('user')->orderBy('id')->get();
        $doctors = Doctor::with('user')->orderBy('id')->get();
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->when($request->filled('patient_id'), fn($q) => $q->where('patient_id', $request->patient_id))
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->latest('date')
            ->get();

        // Si viene desde una cita, prellenamos
        $appointment = $request->filled('appointment_id')
            ? Appointment::with(['patient', 'doctor'])->find($request->appointment_id)
            : null;

        $user = auth()->user();
        $diagnosticTemplates = [];
        if ($user->hasRole('doctor') && $user->doctor) {
            $diagnosticTemplates = \App\Models\DiagnosticTemplate::where('doctor_id', $user->doctor->id)->get();
        }

        return view('medical-records.create', compact('patients', 'doctors', 'appointments', 'appointment', 'diagnosticTemplates'));
    }

    /** Guardar nuevo registro clínico */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'record_date' => 'required|date',
            'chief_complaint' => 'required|string|max:1000',
            'diagnosis' => 'required|string|max:2000',
            'treatment' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'prescriptions' => 'nullable|string|max:2000',
            'referred_to' => 'nullable|string|max:500',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|string|max:10',
            'temperature' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'height' => 'nullable|string|max:10',
            'oxygen_saturation' => 'nullable|string|max:10',
            'is_private' => 'boolean',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $validated['attachments'] = null; // Replaced by relationship table

        $record = MedicalRecord::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical_attachments', 'local');

                MedicalRecordAttachment::create([
                    'medical_record_id' => $record->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if ($request->appointment_id) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment && !in_array($appointment->status, ['cancelled', 'no_show'])) {
                $appointment->update(['status' => 'completed']);
            }

            return redirect()->route('appointments.show', $request->appointment_id)
                ->with('success', 'Historia clínica registrada correctamente.');
        }

        return redirect()->route('medical-records.index')
            ->with('success', 'Historia clínica registrada correctamente.');
    }

    /** Ver detalle de un registro clínico */
    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient.user', 'doctor.user', 'appointment']);
        return view('medical-records.show', compact('medicalRecord'));
    }

    /** Formulario de edición */
    public function edit(MedicalRecord $medicalRecord)
    {
        $doctors = Doctor::with('user')->orderBy('id')->get();
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('patient_id', $medicalRecord->patient_id)
            ->latest('date')->get();

        $user = auth()->user();
        $diagnosticTemplates = [];
        if ($user->hasRole('doctor') && $user->doctor) {
            $diagnosticTemplates = \App\Models\DiagnosticTemplate::where('doctor_id', $user->doctor->id)->get();
        }

        return view('medical-records.edit', compact('medicalRecord', 'doctors', 'appointments', 'diagnosticTemplates'));
    }

    /** Actualizar registro */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'record_date' => 'required|date',
            'chief_complaint' => 'required|string|max:1000',
            'diagnosis' => 'required|string|max:2000',
            'treatment' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'prescriptions' => 'nullable|string|max:2000',
            'referred_to' => 'nullable|string|max:500',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|string|max:10',
            'temperature' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'height' => 'nullable|string|max:10',
            'oxygen_saturation' => 'nullable|string|max:10',
            'is_private' => 'boolean',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Append new uploaded files
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical_attachments', 'local');

                MedicalRecordAttachment::create([
                    'medical_record_id' => $medicalRecord->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
        $validated['attachments'] = null;

        $medicalRecord->update($validated);

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Historia clínica actualizada.');
    }

    /** Eliminar registro */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $patientId = $medicalRecord->patient_id;
        $medicalRecord->delete();
        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Registro eliminado correctamente.');
    }

    /** Historial clínico completo de un paciente */
    public function patientHistory(Patient $patient)
    {
        $records = MedicalRecord::with(['doctor.user', 'appointment', 'attachments'])
            ->where('patient_id', $patient->id)
            ->latest('record_date')
            ->get();

        $patient->load('user');
        return view('medical-records.patient-history', compact('patient', 'records'));
    }

    /** Eliminar un archivo adjunto individual */
    public function destroyAttachment(MedicalRecordAttachment $attachment)
    {
        // Remove from storage
        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Archivo eliminado.');
    }

    /** Descargar archivo adjunto */
    public function downloadAttachment(MedicalRecordAttachment $attachment)
    {
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return response()->download(storage_path('app/private/' . $attachment->file_path), $attachment->file_name);
    }
}
