<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiagnosticTemplateController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = \App\Models\DiagnosticTemplate::query();

        if ($user->hasRole('doctor') && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        }

        $templates = $query->with('doctor.user')->latest()->paginate(15);

        return view('diagnostic-templates.index', compact('templates'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->hasRole('doctor') && !$user->doctor) {
            return redirect()->route('dashboard')->with('error', 'No tienes un perfil de médico asignado.');
        }

        return view('diagnostic-templates.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icd_code' => 'nullable|string|max:50',
            'diagnosis_text' => 'required|string|max:2000',
            'treatment_text' => 'nullable|string|max:2000',
            'prescriptions_text' => 'nullable|string|max:2000',
            'notes_text' => 'nullable|string|max:2000',
        ]);

        if ($user->hasRole('doctor') && $user->doctor) {
            $validated['doctor_id'] = $user->doctor->id;
        } else {
            // Admin could create it for a specific doctor if we added a select, but for now we enforce doctor
            $validated['doctor_id'] = $request->input('doctor_id');
            $request->validate(['doctor_id' => 'required|exists:doctors,id']);
        }

        \App\Models\DiagnosticTemplate::create($validated);

        return redirect()->route('diagnostic-templates.index')->with('success', 'Plantilla guardada exitosamente.');
    }

    public function edit(\App\Models\DiagnosticTemplate $diagnosticTemplate)
    {
        $user = auth()->user();
        if ($user->hasRole('doctor') && $user->doctor && $diagnosticTemplate->doctor_id !== $user->doctor->id) {
            abort(403, 'No puedes editar plantillas que no te pertenecen.');
        }

        return view('diagnostic-templates.edit', compact('diagnosticTemplate'));
    }

    public function update(Request $request, \App\Models\DiagnosticTemplate $diagnosticTemplate)
    {
        $user = auth()->user();
        if ($user->hasRole('doctor') && $user->doctor && $diagnosticTemplate->doctor_id !== $user->doctor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icd_code' => 'nullable|string|max:50',
            'diagnosis_text' => 'required|string|max:2000',
            'treatment_text' => 'nullable|string|max:2000',
            'prescriptions_text' => 'nullable|string|max:2000',
            'notes_text' => 'nullable|string|max:2000',
        ]);

        $diagnosticTemplate->update($validated);

        return redirect()->route('diagnostic-templates.index')->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(\App\Models\DiagnosticTemplate $diagnosticTemplate)
    {
        $user = auth()->user();
        if ($user->hasRole('doctor') && $user->doctor && $diagnosticTemplate->doctor_id !== $user->doctor->id) {
            abort(403);
        }

        $diagnosticTemplate->delete();

        return redirect()->route('diagnostic-templates.index')->with('success', 'Plantilla eliminada.');
    }
}
