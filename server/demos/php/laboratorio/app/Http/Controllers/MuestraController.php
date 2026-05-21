<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Muestra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MuestraController extends Controller
{
    public function index(Request $request)
    {
        // Órdenes pendientes o en proceso que necesitan toma de muestra o análisis
        $ordenes = Orden::with(['paciente', 'detalles.prueba'])->whereIn('estado', ['Pendiente', 'En proceso'])->latest()->paginate(10);
        return view('muestras.index', compact('ordenes'));
    }

    public function tomarMuestra(Request $request, Orden $orden)
    {
        try {
            DB::beginTransaction();

            // Agrupar pruebas por tipo de muestra para no generar códigos duplicados
            // Ej: todas las de sangre usan 1 solo tubo/código de muestra
            $tiposMuestra = $orden->detalles->pluck('prueba.muestra_tipo')->unique();

            foreach ($tiposMuestra as $index => $tipo) {
                // Verificar si ya existe muestra para este tipo
                $existe = Muestra::where('orden_id', $orden->id)->where('tipo_muestra', $tipo)->exists();
                
                if (!$existe) {
                    Muestra::create([
                        'orden_id' => $orden->id,
                        'codigo_muestra' => 'MUE-' . date('Ymd') . '-' . str_pad($orden->id, 4, '0', STR_PAD_LEFT) . '-' . ($index + 1),
                        'tipo_muestra' => $tipo,
                        'fecha_toma' => now(),
                        'tomado_por' => auth()->id(),
                        'estado' => 'Recibida'
                    ]);
                }
            }

            // Cambiar estado de la orden
            if ($orden->estado === 'Pendiente') {
                $orden->update(['estado' => 'En proceso']);
                
                // Cambiar estado de detalles
                foreach ($orden->detalles as $detalle) {
                    if ($detalle->estado === 'Pendiente') {
                        $detalle->update(['estado' => 'En proceso']);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Toma de muestras registrada. Etiquetas generadas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar toma de muestra: ' . $e->getMessage());
        }
    }

    public function imprimirEtiquetas(Orden $orden)
    {
        $orden->load(['paciente', 'muestras']);
        
        if ($orden->muestras->count() == 0) {
            return back()->with('error', 'No hay muestras registradas para esta orden.');
        }

        return view('muestras.labels', compact('orden'));
    }
}
