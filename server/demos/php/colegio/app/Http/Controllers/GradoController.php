<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use App\Models\Seccion;
use App\Models\Personal;
use App\Models\Materia;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function index()
    {
        $grados = Grado::withCount(['secciones', 'matriculas'])->get();
        return view('grados.index', compact('grados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'nivel'  => 'required|in:inicial,primaria,secundaria',
        ]);
        Grado::create($request->all());
        return back()->with('success', 'Grado creado correctamente.');
    }

    public function update(Request $request, Grado $grado)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'nivel'  => 'required|in:inicial,primaria,secundaria',
        ]);
        $grado->update($request->all());
        return back()->with('success', 'Grado actualizado.');
    }

    public function destroy(Grado $grado)
    {
        if ($grado->matriculas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene matrículas asociadas.');
        }
        $grado->delete();
        return back()->with('success', 'Grado eliminado.');
    }

    // ---------- SECCIONES ----------
    public function secciones(Grado $grado)
    {
        $grado->load(['secciones.matriculas']);
        $docentes = Personal::where('tipo', 'docente')->where('estado', 'activo')->get();
        return view('grados.secciones', compact('grado', 'docentes'));
    }

    public function storeSeccion(Request $request, Grado $grado)
    {
        $request->validate([
            'nombre'    => 'required|string|max:10',
            'turno'     => 'required|in:mañana,tarde,noche',
            'capacidad' => 'required|integer|min:1',
        ]);
        Seccion::create(array_merge($request->all(), ['grado_id' => $grado->id]));
        return back()->with('success', 'Sección creada.');
    }

    public function updateSeccion(Request $request, Seccion $seccion)
    {
        $request->validate([
            'nombre'    => 'required|string|max:10',
            'turno'     => 'required|in:mañana,tarde,noche',
            'capacidad' => 'required|integer|min:1',
        ]);
        $seccion->update($request->all());
        return back()->with('success', 'Sección actualizada.');
    }

    public function destroySeccion(Seccion $seccion)
    {
        if ($seccion->matriculas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene alumnos matriculados.');
        }
        $seccion->delete();
        return back()->with('success', 'Sección eliminada.');
    }

    // ---------- MATERIAS ----------
    public function materias()
    {
        $materias  = Materia::withCount('asignaciones')->get();
        $docentes  = Personal::where('tipo', 'docente')->where('estado', 'activo')->get();
        $secciones = Seccion::with('grado')->get();
        $anio      = date('Y');
        $asignaciones = Asignacion::with(['personal', 'materia', 'seccion.grado'])
            ->where('anio_escolar', $anio)->get();

        return view('materias.index', compact('materias', 'docentes', 'secciones', 'asignaciones', 'anio'));
    }

    public function storeMateria(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'codigo'         => 'required|string|max:20|unique:materias',
            'nivel'          => 'required|in:inicial,primaria,secundaria,todos',
            'horas_semanales'=> 'required|integer|min:1',
        ]);
        Materia::create($request->all());
        return back()->with('success', 'Materia creada.');
    }

    public function updateMateria(Request $request, Materia $materia)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'horas_semanales'=> 'required|integer|min:1',
        ]);
        $materia->update($request->all());
        return back()->with('success', 'Materia actualizada.');
    }

    public function destroyMateria(Materia $materia)
    {
        $materia->update(['activo' => false]);
        return back()->with('success', 'Materia desactivada.');
    }

    public function storeAsignacion(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'materia_id'  => 'required|exists:materias,id',
            'seccion_id'  => 'required|exists:secciones,id',
            'anio_escolar'=> 'required|integer',
        ]);

        Asignacion::updateOrCreate(
            [
                'materia_id'  => $request->materia_id,
                'seccion_id'  => $request->seccion_id,
                'anio_escolar'=> $request->anio_escolar,
            ],
            ['personal_id' => $request->personal_id]
        );

        return back()->with('success', 'Docente asignado correctamente.');
    }

    public function destroyAsignacion(Asignacion $asignacion)
    {
        $asignacion->delete();
        return back()->with('success', 'Asignación eliminada.');
    }
}
