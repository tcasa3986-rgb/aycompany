<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Office;
use Illuminate\Http\Request;

class DoctorOfficeController extends Controller
{
    public function index(Doctor $doctor)
    {
        $offices = $doctor->offices()->orderBy('is_active', 'desc')->orderBy('name')->get();
        return view('doctors.offices.index', compact('doctor', 'offices'));
    }

    public function create(Doctor $doctor)
    {
        return view('doctors.offices.create', compact('doctor'));
    }

    public function store(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'floor' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'maps_url' => 'nullable|url|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $doctor->offices()->create($validated);

        return redirect()->route('doctors.offices.index', $doctor)
            ->with('success', 'Consultorio agregado correctamente.');
    }

    public function edit(Doctor $doctor, Office $office)
    {
        abort_if($office->doctor_id !== $doctor->id, 403);
        return view('doctors.offices.edit', compact('doctor', 'office'));
    }

    public function update(Request $request, Doctor $doctor, Office $office)
    {
        abort_if($office->doctor_id !== $doctor->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'floor' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'maps_url' => 'nullable|url|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $office->update($validated);

        return redirect()->route('doctors.offices.index', $doctor)
            ->with('success', 'Consultorio actualizado correctamente.');
    }

    public function destroy(Doctor $doctor, Office $office)
    {
        abort_if($office->doctor_id !== $doctor->id, 403);
        $office->delete();

        return redirect()->route('doctors.offices.index', $doctor)
            ->with('success', 'Consultorio eliminado.');
    }
}
