<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\AppointmentType;
use Illuminate\Http\Request;

class AppointmentTypeController extends Controller
{
    public function index(Doctor $doctor)
    {
        $appointmentTypes = $doctor->appointmentTypes()->latest()->paginate(10);
        return view('doctors.appointment-types.index', compact('doctor', 'appointmentTypes'));
    }

    public function create(Doctor $doctor)
    {
        return view('doctors.appointment-types.create', compact('doctor'));
    }

    public function store(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5|max:480', // limit up to 8 hours max just in case
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $doctor->appointmentTypes()->create($validated);

        return redirect()->route('doctors.appointment-types.index', $doctor)
            ->with('success', 'Tipo de cita creado exitosamente.');
    }

    public function edit(Doctor $doctor, AppointmentType $appointmentType)
    {
        if ($appointmentType->doctor_id !== $doctor->id) {
            abort(403);
        }
        return view('doctors.appointment-types.edit', compact('doctor', 'appointmentType'));
    }

    public function update(Request $request, Doctor $doctor, AppointmentType $appointmentType)
    {
        if ($appointmentType->doctor_id !== $doctor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $appointmentType->update($validated);

        return redirect()->route('doctors.appointment-types.index', $doctor)
            ->with('success', 'Tipo de cita actualizado.');
    }

    public function destroy(Doctor $doctor, AppointmentType $appointmentType)
    {
        if ($appointmentType->doctor_id !== $doctor->id) {
            abort(403);
        }

        $appointmentType->delete();

        return redirect()->route('doctors.appointment-types.index', $doctor)
            ->with('success', 'Tipo de cita eliminado.');
    }
}
