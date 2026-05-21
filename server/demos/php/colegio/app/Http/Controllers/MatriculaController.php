<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Seccion;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    public function index(Request $request)
    {
        $query = Matricula::with(['alumno', 'grado', 'seccion']);

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->whereHas('alumno', fn($a) =>
                $a->where('nombres', 'like', "%$q%")
                  ->orWhere('apellidos', 'like', "%$q%")
                  ->orWhere('dni', 'like', "%$q%")
            );
        }

        if ($request->filled('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        if ($request->filled('anio')) {
            $query->where('anio_escolar', $request->anio);
        }

        $matriculas = $query->latest()->paginate(15)->withQueryString();
        $grados     = Grado::all();

        return view('enrollments.index', compact('matriculas', 'grados'));
    }

    public function create()
    {
        $alumnos  = Alumno::where('estado', 'activo')->get();
        $grados   = Grado::with('secciones')->get();
        $secciones = Seccion::with('grado')->get();
        return view('enrollments.create', compact('alumnos', 'grados', 'secciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alumno_id'      => 'required|exists:alumnos,id',
            'grado_id'       => 'required|exists:grados,id',
            'seccion_id'     => 'required|exists:secciones,id',
            'anio_escolar'   => 'required|integer|min:2020|max:2030',
            'fecha_matricula'=> 'required|date',
        ]);

        $data = $request->all();
        $data['numero']        = Matricula::generarNumero();
        $data['registrado_por']= auth()->id();

        Matricula::create($data);

        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula registrada correctamente.');
    }

    public function show(Matricula $matricula)
    {
        $matricula->load(['alumno', 'grado', 'seccion', 'registradoPor']);
        return view('enrollments.show', compact('matricula'));
    }

    public function edit(Matricula $matricula)
    {
        $alumnos  = Alumno::where('estado', 'activo')->get();
        $grados   = Grado::with('secciones')->get();
        $secciones = Seccion::with('grado')->get();
        return view('enrollments.edit', compact('matricula', 'alumnos', 'grados', 'secciones'));
    }

    public function update(Request $request, Matricula $matricula)
    {
        $request->validate([
            'grado_id'   => 'required|exists:grados,id',
            'seccion_id' => 'required|exists:secciones,id',
            'estado'     => 'required|in:activo,retirado,trasladado',
        ]);

        $matricula->update($request->all());

        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula actualizada correctamente.');
    }

    public function destroy(Matricula $matricula)
    {
        $matricula->update(['estado' => 'retirado']);
        return redirect()->route('matriculas.index')
            ->with('success', 'Matrícula retirada.');
    }
}
