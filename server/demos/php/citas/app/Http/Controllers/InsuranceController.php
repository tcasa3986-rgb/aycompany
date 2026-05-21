<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index()
    {
        $insurances = \App\Models\Insurance::latest()->paginate(15);
        return view('insurances.index', compact('insurances'));
    }

    public function create()
    {
        return view('insurances.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rnc_or_code' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'coverage_percentage' => 'required|numeric|min:0|max:100',
        ]);

        \App\Models\Insurance::create($validated);

        return redirect()->route('insurances.index')->with('success', 'Aseguradora guardada.');
    }

    public function edit(\App\Models\Insurance $insurance)
    {
        return view('insurances.edit', compact('insurance'));
    }

    public function update(Request $request, \App\Models\Insurance $insurance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rnc_or_code' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'coverage_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $insurance->update($validated);

        return redirect()->route('insurances.index')->with('success', 'Aseguradora actualizada.');
    }

    public function destroy(\App\Models\Insurance $insurance)
    {
        $insurance->delete();
        return redirect()->route('insurances.index')->with('success', 'Aseguradora eliminada.');
    }
}
