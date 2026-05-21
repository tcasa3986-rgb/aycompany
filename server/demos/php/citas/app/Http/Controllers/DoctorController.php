<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'specialty']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('specialty', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty')) {
            $query->where('specialty_id', $request->specialty);
        }

        $doctors = $query->latest()->paginate(15)->withQueryString();
        $specialties = Specialty::orderBy('name')->get();

        return view('doctors.index', compact('doctors', 'specialties'));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('name')->get();
        return view('doctors.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'specialty_id' => 'required|exists:specialties,id',
            'collegiate_number' => 'required|string|unique:doctors,collegiate_number',
            'biography' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $request->specialty_id,
            'collegiate_number' => $request->collegiate_number,
            'biography' => $request->biography,
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Médico registrado correctamente.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'specialty', 'appointments.patient.user']);
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $specialties = Specialty::orderBy('name')->get();
        return view('doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'specialty_id' => 'required|exists:specialties,id',
            'collegiate_number' => 'required|string|unique:doctors,collegiate_number,' . $doctor->id,
            'biography' => 'nullable|string',
        ]);

        $doctor->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $doctor->update([
            'specialty_id' => $request->specialty_id,
            'collegiate_number' => $request->collegiate_number,
            'biography' => $request->biography,
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->user->delete();
        return redirect()->route('doctors.index')
            ->with('success', 'Médico eliminado correctamente.');
    }
}
