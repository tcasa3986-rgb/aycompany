<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->latest()->paginate(15);
        return view('specialties.index', compact('specialties'));
    }

    public function create()
    {
        return view('specialties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name',
            'description' => 'nullable|string',
        ]);

        Specialty::create($request->only('name', 'description'));

        return redirect()->route('specialties.index')
            ->with('success', 'Especialidad creada correctamente.');
    }

    public function edit(Specialty $specialty)
    {
        return view('specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'description' => 'nullable|string',
        ]);

        $specialty->update($request->only('name', 'description'));

        return redirect()->route('specialties.index')
            ->with('success', 'Especialidad actualizada.');
    }

    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return redirect()->route('specialties.index')
            ->with('success', 'Especialidad eliminada.');
    }
}
