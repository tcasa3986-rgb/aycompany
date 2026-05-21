<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentCancelled;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user', 'specialty', 'office']);

        if (auth()->user()->hasRole('doctor') && !auth()->user()->hasRole('admin')) {
            $query->where('doctor_id', auth()->user()->doctor->id ?? 0);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $appointments = $query->latest('date')->paginate(15)->withQueryString();
        $doctors = Doctor::with('user')->get();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('name')->get();
        $patients = Patient::with('user')->get();
        return view('appointments.create', compact('specialties', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

        // Verificar solapamiento: el médico ya tiene otra cita en este bloque de tiempo
        $exists = Appointment::where('doctor_id', $request->doctor_id)
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
                ->withErrors(['date' => 'El horario choca con otra cita programada. El servicio requiere ' . $appointmentType->duration_minutes . ' minutos.']);
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'specialty_id' => $request->specialty_id,
            'office_id' => $request->office_id,
            'appointment_type_id' => $request->appointment_type_id,
            'date' => $startDate,
            'end_time' => $endDate,
            'status' => 'pending',
            'notes' => $request->reason,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Cita agendada correctamente.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'specialty', 'office']);
        return view('appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $allowed = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowed),
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $previousStatus = $appointment->status;

        $appointment->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Fire notifications on status transitions
        $appointment->load(['patient.user', 'doctor', 'specialty']);
        $patientUser = $appointment->patient?->user;

        if ($patientUser) {
            if (
                $request->status === 'confirmed' && $previousStatus !== 'confirmed'
                && config('settings.notify_on_confirm', '1') === '1'
            ) {
                $patientUser->notify(new AppointmentConfirmed($appointment));
            }

            if (
                $request->status === 'cancelled' && $previousStatus !== 'cancelled'
            ) {
                if (config('settings.notify_on_cancel', '1') === '1') {
                    $patientUser->notify(new AppointmentCancelled($appointment));
                }

                // Check Waitlist
                $waitlistCandidates = \App\Models\Waitlist::where('doctor_id', $appointment->doctor_id)
                    ->where('status', 'waiting')
                    ->where(function ($q) use ($appointment) {
                        $date = \Carbon\Carbon::parse($appointment->date)->format('Y-m-d');
                        $q->whereNull('requested_date_from')->orWhere('requested_date_from', '<=', $date);
                    })
                    ->where(function ($q) use ($appointment) {
                        $date = \Carbon\Carbon::parse($appointment->date)->format('Y-m-d');
                        $q->whereNull('requested_date_to')->orWhere('requested_date_to', '>=', $date);
                    })
                    ->get();

                if ($waitlistCandidates->count() > 0) {
                    session()->flash('waitlist_alert', 'Se liberó un espacio y hay ' . $waitlistCandidates->count() . ' paciente(s) en lista de espera para el Dr. ' . $appointment->doctor->user->name . '.');
                }
            }
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Estado de la cita actualizado.');
    }

    public function getDoctorsBySpecialty(Specialty $specialty)
    {
        $doctors = $specialty->doctors()->with([
            'user',
            'offices' => function ($q) {
                $q->where('is_active', true);
            },
            'appointmentTypes' => function ($q) {
                $q->where('is_active', true);
            }
        ])->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->user->name,
                'offices' => $d->offices->map(fn($o) => ['id' => $o->id, 'name' => $o->name]),
                'appointmentTypes' => $d->appointmentTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'duration_minutes' => $t->duration_minutes])
            ];
        });

        return response()->json($doctors);
    }

    public function calendar()
    {
        $doctors = Doctor::with('user')->orderBy('id')->get();
        return view('appointments.calendar', compact('doctors'));
    }

    public function calendarEvents(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user', 'specialty', 'office'])
            ->when($request->filled('doctor_id'), fn($q) => $q->where('doctor_id', $request->doctor_id))
            ->when($request->filled('start'), fn($q) => $q->where('date', '>=', $request->start))
            ->when($request->filled('end'), fn($q) => $q->where('date', '<=', $request->end));

        if (auth()->user()->hasRole('doctor') && !auth()->user()->hasRole('admin')) {
            $query->where('doctor_id', auth()->user()->doctor->id ?? 0);
        }

        $colors = [
            'pending' => '#f59e0b',
            'confirmed' => '#4A88F6',
            'in_progress' => '#8b5cf6',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            'no_show' => '#6b7280',
        ];

        $events = $query->get()->map(function ($appt) use ($colors) {
            return [
                'id' => $appt->id,
                'title' => $appt->patient->user->name . ' — Dr. ' . $appt->doctor->user->name,
                'start' => $appt->date->toIso8601String(),
                'backgroundColor' => $colors[$appt->status] ?? '#6b7280',
                'borderColor' => $colors[$appt->status] ?? '#6b7280',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'patient' => $appt->patient->user->name,
                    'doctor' => $appt->doctor->user->name,
                    'specialty' => $appt->specialty->name,
                    'status' => $appt->status,
                    'statusLabel' => $appt->status_label,
                ],
            ];
        });

        return response()->json($events);
    }

    public function reschedule(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Solo se pueden reagendar citas pendientes o confirmadas.');
        }
        $appointment->load(['patient.user', 'doctor.user', 'specialty']);
        return view('appointments.reschedule', compact('appointment'));
    }

    public function doReschedule(Request $request, Appointment $appointment)
    {
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'No se puede reagendar esta cita.');
        }

        $request->validate([
            'date' => 'required|date|after:now',
        ]);

        // Check slot not occupied by another appointment (excluding this one)
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['date' => 'El médico ya tiene una cita en ese horario.']);
        }

        $appointment->update(['date' => $request->date]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Cita reagendada correctamente.');
    }
}
