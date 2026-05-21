<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Seccion;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumno::query();

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function($q2) use ($q) {
                $q2->where('nombres', 'like', "%$q%")
                   ->orWhere('apellidos', 'like', "%$q%")
                   ->orWhere('dni', 'like', "%$q%")
                   ->orWhere('codigo', 'like', "%$q%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $alumnos = $query->latest()->paginate(15)->withQueryString();

        return view('students.index', compact('alumnos'));
    }

    public function create()
    {
        $grados   = Grado::all();
        $secciones = Seccion::with('grado')->get();
        return view('students.create', compact('grados', 'secciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni'             => 'required|string|max:20|unique:alumnos',
            'nombres'         => 'required|string|max:100',
            'apellidos'       => 'required|string|max:100',
            'fecha_nacimiento'=> 'required|date',
            'genero'          => 'required|in:M,F',
            'apoderado_nombre'=> 'nullable|string|max:150',
            'apoderado_telefono' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        $data['codigo'] = Alumno::generarCodigo();

        Alumno::create($data);

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno registrado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $alumno->load(['matriculas.grado', 'matriculas.seccion', 'pagos.concepto']);
        return view('students.show', compact('alumno'));
    }

    public function edit(Alumno $alumno)
    {
        $grados   = Grado::all();
        $secciones = Seccion::with('grado')->get();
        return view('students.edit', compact('alumno', 'grados', 'secciones'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'dni'             => 'required|string|max:20|unique:alumnos,dni,' . $alumno->id,
            'nombres'         => 'required|string|max:100',
            'apellidos'       => 'required|string|max:100',
            'fecha_nacimiento'=> 'required|date',
            'genero'          => 'required|in:M,F',
        ]);

        $alumno->update($request->all());

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->update(['estado' => 'inactivo']);
        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno desactivado correctamente.');
    }
}
