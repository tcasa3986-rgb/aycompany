<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Exports\PatientsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('phone', 'like', "%{$search}%");
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $doctors = Doctor::with('user')->orderBy('id')->get();
        $insurances = \App\Models\Insurance::orderBy('name')->get();
        return view('patients.create', compact('doctors', 'insurances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'primary_doctor_id' => 'nullable|exists:doctors,id',
            'insurance_id' => 'nullable|exists:insurances,id',
            'policy_number' => 'nullable|string|max:100',
        ]);

        // Check if the email is already taken by a user THAT IS ALREADY a patient
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && $existingUser->patient) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'El correo electrónico ya pertenece a un paciente registrado.']);
        }

        \DB::transaction(function () use ($request, $existingUser) {
            // Reuse the user if it exists but has no patient yet; otherwise create a new one
            if ($existingUser) {
                $user = $existingUser;
                $user->update([
                    'name' => $request->name,
                    'password' => \Hash::make($request->password),
                ]);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            }

            $user->patient()->create([
                'dob' => $request->dob,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'allergies' => $request->allergies,
                'phone' => $request->phone,
                'address' => $request->address,
                'primary_doctor_id' => $request->primary_doctor_id ?: null,
                'insurance_id' => $request->insurance_id ?: null,
                'policy_number' => $request->policy_number,
            ]);
        });

        return redirect()->route('patients.index')
            ->with('success', 'Paciente registrado correctamente.');
    }


    public function show(Patient $patient)
    {
        $patient->load(['user', 'primaryDoctor.user', 'appointments.doctor.user', 'appointments.specialty']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $patient->load('user');
        $doctors = Doctor::with('user')->orderBy('id')->get();
        $insurances = \App\Models\Insurance::orderBy('name')->get();
        return view('patients.edit', compact('patient', 'doctors', 'insurances'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->user_id,
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'primary_doctor_id' => 'nullable|exists:doctors,id',
            'insurance_id' => 'nullable|exists:insurances,id',
            'policy_number' => 'nullable|string|max:100',
        ]);

        $patient->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $patient->update([
            'dob' => $request->dob,
            'gender' => $request->gender,
            'blood_type' => $request->blood_type,
            'allergies' => $request->allergies,
            'phone' => $request->phone,
            'address' => $request->address,
            'primary_doctor_id' => $request->primary_doctor_id ?: null,
            'insurance_id' => $request->insurance_id ?: null,
            'policy_number' => $request->policy_number,
        ]);

        return redirect()->route('patients.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Patient $patient)
    {
        $patient->user->delete(); // cascade elimina el patient
        return redirect()->route('patients.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'gender', 'blood_type']);
        return Excel::download(new PatientsExport($filters), 'pacientes_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['search', 'gender', 'blood_type']);
        $export = new PatientsExport($filters);
        $patients = $export->collection();
        $pdf = Pdf::loadView('patients.pdf', compact('patients'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('pacientes_' . now()->format('Ymd') . '.pdf');
    }

    public function exportProfilePdf(Patient $patient)
    {
        $patient->load([
            'user',
            'primaryDoctor.user',
            'appointments' => function ($query) {
                $query->orderBy('date', 'desc');
            },
            'appointments.doctor.user',
            'appointments.specialty',
            'appointments.prescriptions'
        ]);

        $pdf = Pdf::loadView('patients.pdf-single', compact('patient'));
        return $pdf->download('Ficha_Paciente_' . str_replace(' ', '_', $patient->user->name) . '.pdf');
    }
}
