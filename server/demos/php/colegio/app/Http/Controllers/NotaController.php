<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Seccion;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::with('grado')->get();
        $materias  = Materia::activas()->get();
        $anio      = $request->get('anio', Configuracion::anioEscolar());

        $seccionId = $request->get('seccion_id');
        $materiaId = $request->get('materia_id');

        $libroNotas = collect();
        $seccion    = null;
        $materia    = null;

        if ($seccionId && $materiaId) {
            $seccion = Seccion::with('grado')->find($seccionId);
            $materia = Materia::find($materiaId);

            // Obtener alumnos matriculados en esta sección/año
            $matriculas = Matricula::where('seccion_id', $seccionId)
                ->where('anio_escolar', $anio)
                ->where('estado', 'activo')
                ->with('alumno')
                ->get();

            foreach ($matriculas as $matricula) {
                $notasBimestres = [];
                for ($b = 1; $b <= 4; $b++) {
                    $notasBimestres[$b] = Nota::where([
                        'alumno_id'   => $matricula->alumno_id,
                        'materia_id'  => $materiaId,
                        'seccion_id'  => $seccionId,
                        'anio_escolar'=> $anio,
                        'bimestre'    => $b,
                    ])->first();
                }

                $notasValidas = collect($notasBimestres)->filter()->pluck('nota')->filter();
                $promedio     = $notasValidas->count() > 0 ? round($notasValidas->avg(), 2) : null;
                $notaMin      = Configuracion::notaMinima();

                $libroNotas->push([
                    'alumno'   => $matricula->alumno,
                    'bimestres'=> $notasBimestres,
                    'promedio' => $promedio,
                    'estado'   => $promedio !== null ? ($promedio >= $notaMin ? 'aprobado' : 'desaprobado') : 'pendiente',
                ]);
            }
        }

        return view('notas.index', compact('secciones','materias','seccion','materia','anio','libroNotas','seccionId','materiaId'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'notas'       => 'required|array',
            'seccion_id'  => 'required|exists:secciones,id',
            'materia_id'  => 'required|exists:materias,id',
            'anio_escolar'=> 'required|integer',
            'bimestre'    => 'required|integer|min:1|max:4',
        ]);

        $notaMin = Configuracion::notaMinima();

        foreach ($request->notas as $alumnoId => $valor) {
            if ($valor === null || $valor === '') continue;

            $valorNum = (float) $valor;
            Nota::updateOrCreate(
                [
                    'alumno_id'   => $alumnoId,
                    'materia_id'  => $request->materia_id,
                    'seccion_id'  => $request->seccion_id,
                    'anio_escolar'=> $request->anio_escolar,
                    'bimestre'    => $request->bimestre,
                ],
                [
                    'nota'            => $valorNum,
                    'estado'          => $valorNum >= $notaMin ? 'aprobado' : 'desaprobado',
                    'registrado_por'  => auth()->id(),
                ]
            );
        }

        return back()->with('success', "Notas del Bimestre {$request->bimestre} guardadas correctamente.");
    }

    public function boleta(int $alumnoId, Request $request)
    {
        $anio     = $request->get('anio', Configuracion::anioEscolar());
        $matricula= Matricula::where('alumno_id', $alumnoId)
            ->where('anio_escolar', $anio)
            ->where('estado', 'activo')
            ->with(['alumno','grado','seccion'])
            ->firstOrFail();

        $materias = Materia::activas()->get();
        $notaMin  = Configuracion::notaMinima();

        $libroMaterias = $materias->map(function ($materia) use ($alumnoId, $matricula, $anio, $notaMin) {
            $bimestres = [];
            for ($b = 1; $b <= 4; $b++) {
                $nota = Nota::where([
                    'alumno_id'  => $alumnoId,
                    'materia_id' => $materia->id,
                    'seccion_id' => $matricula->seccion_id,
                    'anio_escolar'=> $anio,
                    'bimestre'   => $b,
                ])->first();
                $bimestres[$b] = $nota?->nota;
            }
            $validas  = collect($bimestres)->filter();
            $promedio = $validas->count() > 0 ? round($validas->avg(), 2) : null;

            return [
                'materia'  => $materia,
                'bimestres'=> $bimestres,
                'promedio' => $promedio,
                'estado'   => $promedio !== null ? ($promedio >= $notaMin ? 'aprobado' : 'desaprobado') : 'pendiente',
            ];
        });

        $config = [
            'nombre_colegio' => Configuracion::nombreColegio(),
            'nota_minima'    => $notaMin,
        ];

        return view('notas.boleta', compact('matricula', 'libroMaterias', 'anio', 'config'));
    }
}
