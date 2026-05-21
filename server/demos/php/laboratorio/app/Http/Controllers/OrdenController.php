<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Paciente;
use App\Models\MedicoReferidor;
use App\Models\Convenio;
use App\Models\Prueba;
use App\Models\OrdenDetalle;
use App\Models\Muestra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenController extends Controller
{
    public function index(Request $request)
    {
        $query = Orden::with(['paciente', 'medico']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('numero_orden', 'like', "%{$search}%")
                  ->orWhereHas('paciente', function($q) use ($search) {
                      $q->where('nombres', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%");
                  });
        }

        $ordenes = $query->latest('fecha_registro')->paginate(10);
        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        $medicos = MedicoReferidor::all();
        $convenios = Convenio::all();
        $pruebas = Prueba::with('area')->get();
        
        return view('ordenes.create', compact('pacientes', 'medicos', 'convenios', 'pruebas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'nullable|exists:medicos_referidores,id',
            'convenio_id' => 'nullable|exists:convenios,id',
            'prioridad' => 'required|in:Normal,Urgente,Emergencia',
            'pruebas' => 'required|array',
            'pruebas.*' => 'exists:pruebas,id'
        ]);

        try {
            DB::beginTransaction();

            $pruebasSeleccionadas = Prueba::whereIn('id', $request->pruebas)->get();
            $subtotal = $pruebasSeleccionadas->sum('precio');
            
            $descuento = 0;
            if ($request->convenio_id) {
                $convenio = Convenio::find($request->convenio_id);
                if ($convenio) {
                    $descuento = $subtotal * ($convenio->descuento_porcentaje / 100);
                }
            }

            $total = $subtotal - $descuento;

            $orden = Orden::create([
                'numero_orden' => Orden::generarNumero(),
                'paciente_id' => $request->paciente_id,
                'medico_id' => $request->medico_id,
                'convenio_id' => $request->convenio_id,
                'user_id' => auth()->id(),
                'fecha_registro' => now(),
                'diagnostico_presuntivo' => $request->diagnostico_presuntivo,
                'prioridad' => $request->prioridad,
                'estado' => 'Pendiente',
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'pagado' => false,
            ]);

            foreach ($pruebasSeleccionadas as $prueba) {
                OrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'prueba_id' => $prueba->id,
                    'precio_unitario' => $prueba->precio,
                    'descuento' => $request->convenio_id ? ($prueba->precio * ($convenio->descuento_porcentaje / 100)) : 0,
                    'precio_final' => $request->convenio_id ? ($prueba->precio - ($prueba->precio * ($convenio->descuento_porcentaje / 100))) : $prueba->precio,
                    'estado' => 'Pendiente'
                ]);
                
                // Agrupar por tipo de muestra se hace en Toma de Muestras después, pero lo dejaremos así.
            }

            DB::commit();
            
            return redirect()->route('ordenes.show', $orden->id)->with('success', 'Orden médica generada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar la orden: ' . $e->getMessage());
        }
    }

    public function show(Orden $orden)
    {
        $orden->load(['paciente', 'medico', 'convenio', 'detalles.prueba.area', 'facturas', 'user']);
        return view('ordenes.show', compact('orden'));
    }

    public function edit(Orden $orden)
    {
        if ($orden->estado !== 'Pendiente') {
            return back()->with('error', 'Solo se pueden editar órdenes en estado Pendiente.');
        }

        $pacientes = Paciente::all();
        $medicos = MedicoReferidor::all();
        $convenios = Convenio::all();
        $pruebas = Prueba::with('area')->get();
        $pruebasSeleccionadas = $orden->detalles->pluck('prueba_id')->toArray();
        
        return view('ordenes.edit', compact('orden', 'pacientes', 'medicos', 'convenios', 'pruebas', 'pruebasSeleccionadas'));
    }

    public function update(Request $request, Orden $orden)
    {
        if ($orden->estado !== 'Pendiente') {
            return back()->with('error', 'Solo se pueden editar órdenes en estado Pendiente.');
        }

        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'nullable|exists:medicos_referidores,id',
            'convenio_id' => 'nullable|exists:convenios,id',
            'prioridad' => 'required|in:Normal,Urgente,Emergencia',
            'pruebas' => 'required|array',
            'pruebas.*' => 'exists:pruebas,id'
        ]);

        try {
            DB::beginTransaction();

            $pruebasSeleccionadas = Prueba::whereIn('id', $request->pruebas)->get();
            $subtotal = $pruebasSeleccionadas->sum('precio');
            
            $descuento = 0;
            if ($request->convenio_id) {
                $convenio = Convenio::find($request->convenio_id);
                if ($convenio) {
                    $descuento = $subtotal * ($convenio->descuento_porcentaje / 100);
                }
            }

            $total = $subtotal - $descuento;

            $orden->update([
                'paciente_id' => $request->paciente_id,
                'medico_id' => $request->medico_id,
                'convenio_id' => $request->convenio_id,
                'diagnostico_presuntivo' => $request->diagnostico_presuntivo,
                'prioridad' => $request->prioridad,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
            ]);

            // Eliminar detalles anteriores y crear nuevos
            $orden->detalles()->delete();

            foreach ($pruebasSeleccionadas as $prueba) {
                OrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'prueba_id' => $prueba->id,
                    'precio_unitario' => $prueba->precio,
                    'descuento' => $request->convenio_id ? ($prueba->precio * ($convenio->descuento_porcentaje / 100)) : 0,
                    'precio_final' => $request->convenio_id ? ($prueba->precio - ($prueba->precio * ($convenio->descuento_porcentaje / 100))) : $prueba->precio,
                    'estado' => 'Pendiente'
                ]);
            }

            DB::commit();
            
            return redirect()->route('ordenes.show', $orden->id)->with('success', 'Orden médica actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function destroy(Orden $orden)
    {
        if (!in_array($orden->estado, ['Pendiente'])) {
            return back()->with('error', 'Solo se pueden cancelar órdenes en estado Pendiente.');
        }

        if ($orden->pagado) {
            return back()->with('error', 'No se puede cancelar una orden ya pagada.');
        }

        $orden->update(['estado' => 'Cancelado']);

        return redirect()->route('ordenes.index')->with('success', 'Orden cancelada correctamente.');
    }

    public function entregar(Orden $orden)
    {
        if ($orden->estado !== 'Completado') {
            return back()->with('error', 'Solo se pueden entregar órdenes con estado Completado.');
        }

        $orden->update(['estado' => 'Entregado']);

        return back()->with('success', 'Orden marcada como Entregada correctamente.');
    }
}
