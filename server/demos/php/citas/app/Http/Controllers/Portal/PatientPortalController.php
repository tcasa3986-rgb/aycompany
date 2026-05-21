<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Specialty;
use Illuminate\Http\Request;

class PatientPortalController extends Controller
{
    /** Asegurar que quien accede tiene perfil de paciente */
    private function getPatient()
    {
        $patient = auth()->user()->patient;
        if (!$patient) {
            abort(403, 'Aceso denegado. Se requiere un perfil de paciente.');
        }
        return $patient;
    }

    /** Dashboard Principal del Paciente */
    public function dashboard()
    {
        $patient = $this->getPatient();

        $upcomingAppointments = Appointment::with(['doctor.user', 'specialty', 'office'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('date', '>=', now()->startOfDay())
            ->orderBy('date')
            ->take(3)
            ->get();

        $recentHistory = MedicalRecord::with(['doctor.user', 'appointment'])
            ->where('patient_id', $patient->id)
            ->latest('record_date')
            ->take(5)
            ->get();

        return view('portal.dashboard', compact('patient', 'upcomingAppointments', 'recentHistory'));
    }

    /** Lista completa de citas */
    public function appointments()
    {
        $patient = $this->getPatient();
        $appointments = Appointment::with(['doctor.user', 'specialty'])
            ->where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('portal.appointments', compact('patient', 'appointments'));
    }

    /** Cancelar una cita */
    public function cancelAppointment(Request $request, Appointment $appointment)
    {
        $patient = $this->getPatient();

        if ($appointment->patient_id !== $patient->id) {
            abort(403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason
        ]);

        return back()->with('success', 'Tu cita ha sido cancelada.');
    }

    /** Historial Clínico completo para el paciente */
    public function medicalHistory()
    {
        $patient = $this->getPatient();
        $records = MedicalRecord::with(['doctor.user', 'appointment', 'attachments'])
            ->where('patient_id', $patient->id)
            ->where('is_private', false) // El paciente no debe ver los registros privados del médico
            ->orderBy('record_date', 'desc')
            ->paginate(15);

        return view('portal.medical-history', compact('patient', 'records'));
    }
    /** Formulario de Agendamiento Simplificado */
    public function createAppointment()
    {
        $patient = $this->getPatient();
        $specialties = Specialty::orderBy('name')->get();
        return view('portal.appointments-create', compact('patient', 'specialties'));
    }

    /** Procesar el agendamiento */
    public function storeAppointment(Request $request)
    {
        $patient = $this->getPatient();

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'office_id' => 'required|exists:offices,id',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        $appointmentType = \App\Models\AppointmentType::find($request->appointment_type_id);
        $startDate = \Carbon\Carbon::parse($request->date);
        $endDate = $startDate->copy()->addMinutes($appointmentType->duration_minutes);

        // Verificar solapamiento para ese médico
        $exists = \App\Models\Appointment::where('doctor_id', $request->doctor_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate->copy()->subMinute()])
                    ->orWhereBetween('end_time', [$startDate->copy()->addMinute(), $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('date', '<=', $startDate)
                            ->where('end_time', '>=', $endDate);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['date' => 'El horario choca con otra cita programada o no está disponible.']);
        }

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'specialty_id' => $request->specialty_id,
            'office_id' => $request->office_id,
            'appointment_type_id' => $request->appointment_type_id,
            'date' => $startDate,
            'end_time' => $endDate,
            'status' => 'pending',
            'notes' => $request->reason,
        ]);

        return redirect()->route('portal.appointments')
            ->with('success', 'Tu cita ha sido agendada correctamente.');
    }

    /** Vista de facturas o recibos del paciente */
    public function invoices()
    {
        $patient = $this->getPatient();

        $invoices = \App\Models\Invoice::with(['appointment.doctor.user'])
            ->whereHas('appointment', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('portal.invoices', compact('patient', 'invoices'));
    }
}
