<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\AppointmentType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaitlistController extends Controller
{
    public function index(Request $request)
    {
        $query = Waitlist::with(['patient.user', 'doctor.user', 'appointmentType'])
            ->orderByRaw("FIELD(status, 'waiting', 'notified', 'resolved', 'cancelled')")
            ->orderBy('created_at', 'desc');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $waitlists = $query->paginate(15)->withQueryString();
        $doctors = Doctor::with('user')->get();

        return view('waitlists.index', compact('waitlists', 'doctors'));
    }

    public function create()
    {
        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();
        return view('waitlists.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_type_id' => 'nullable|exists:appointment_types,id',
            'requested_date_from' => 'nullable|date|after_or_equal:today',
            'requested_date_to' => 'nullable|date|after_or_equal:requested_date_from',
            'notes' => 'nullable|string|max:1000',
        ]);

        Waitlist::create($request->all());

        return redirect()->route('waitlists.index')
            ->with('success', 'Paciente añadido a la lista de espera.');
    }

    public function edit(Waitlist $waitlist)
    {
        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();
        // Load appointment types for the selected doctor
        $appointmentTypes = AppointmentType::where('doctor_id', $waitlist->doctor_id)->get();

        return view('waitlists.edit', compact('waitlist', 'doctors', 'patients', 'appointmentTypes'));
    }

    public function update(Request $request, Waitlist $waitlist)
    {
        $request->validate([
            'appointment_type_id' => 'nullable|exists:appointment_types,id',
            'requested_date_from' => 'nullable|date',
            'requested_date_to' => 'nullable|date|after_or_equal:requested_date_from',
            'status' => 'required|in:waiting,notified,resolved,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $waitlist->update([
            'appointment_type_id' => $request->appointment_type_id,
            'requested_date_from' => $request->requested_date_from,
            'requested_date_to' => $request->requested_date_to,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('waitlists.index')
            ->with('success', 'Lista de espera actualizada.');
    }

    public function destroy(Waitlist $waitlist)
    {
        $waitlist->delete();

        return redirect()->route('waitlists.index')
            ->with('success', 'Entrada eliminada de la lista de espera.');
    }
}
