<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Resultado;
use App\Models\Muestra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoController extends Controller
{
    public function index(Request $request)
    {
        // En listarórdenes que están "En proceso"
        $ordenes = Orden::with(['paciente'])->where('estado', 'En proceso')->latest()->paginate(10);
        return view('resultados.index', compact('ordenes'));
    }

    public function create(Orden $orden)
    {
        // Cargar paciente y pruebas que faltan por validar
        $orden->load(['paciente', 'medico', 'detalles.prueba']);
        
        return view('resultados.create', compact('orden'));
    }

    public function store(Request $request, Orden $orden)
    {
        $request->validate([
            'resultados' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            $completados = 0;
            $criticos = 0;

            foreach ($request->resultados as $detalle_id => $datos) {
                if (!empty($datos['valor'])) {
                    $detalle = OrdenDetalle::findOrFail($detalle_id);
                    $prueba = $detalle->prueba;
                    
                    // Buscar muestra asociada (tomamos la primera de este tipo para la orden)
                    $muestra = Muestra::where('orden_id', $orden->id)
                            ->where('tipo_muestra', $prueba->muestra_tipo)
                            ->first();

                    $resultado = Resultado::create([
                        'orden_detalle_id' => $detalle->id,
                        'muestra_id' => $muestra ? $muestra->id : null,
                        'valor' => $datos['valor'],
                        'unidad' => $prueba->unidad,
                        'valores_referencia' => $prueba->valores_referencia,
                        'interpretacion' => $datos['interpretacion'] ?? 'Normal',
                        'metodo' => $datos['metodo'] ?? 'Automático',
                        'validado_por' => auth()->id(),
                        'fecha_validacion' => now(),
                        'valor_critico' => isset($datos['interpretacion']) && $datos['interpretacion'] == 'Crítico'
                    ]);

                    if ($resultado->valor_critico) {
                        $criticos++;
                    }

                    $detalle->update(['estado' => 'Completado']);
                    $completados++;
                }
            }

            // Si todos los detalles están completados, completar la orden
            $totalDetalles = $orden->detalles()->count();
            $completadosDB = $orden->detalles()->where('estado', 'Completado')->count();

            if ($completadosDB == $totalDetalles) {
                $orden->update(['estado' => 'Completado']);
            }

            DB::commit();
            
            $msg = "Resultados registrados satisfactoriamente.";
            if ($criticos > 0) {
                $msg .= " ¡ATENCIÓN! Se registraron $criticos valores críticos.";
            }

            return redirect()->route('resultados.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar resultados: ' . $e->getMessage());
        }
    }
}
