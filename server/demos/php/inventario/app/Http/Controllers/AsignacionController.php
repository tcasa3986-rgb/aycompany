<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Equipo;
use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Exports\AsignacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Calcular estadísticas
        $statsQuery = Asignacion::query();

        if (!$user->isAdmin() && $user->id_sucursal) {
            $statsQuery->whereHas('equipo', function ($q) use ($user) {
                $q->where('id_sucursal', $user->id_sucursal);
            });
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'activas' => (clone $statsQuery)->where('estado_asignacion', 'Activa')->count(),
            'finalizadas' => (clone $statsQuery)->where('estado_asignacion', 'Finalizada')->count(),
            'anuladas' => (clone $statsQuery)->where('estado_asignacion', 'Anulada')->count(),
        ];

        $asignaciones = Asignacion::with(['equipo.marca', 'equipo.modelo', 'empleado'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->whereHas('equipo', function ($q) use ($user) {
                    $q->where('id_sucursal', $user->id_sucursal);
                });
            })
            ->when($request->estado, function ($query, $estado) {
                $query->where('estado_asignacion', $estado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('asignaciones.index', compact('asignaciones', 'stats'));
    }

    public function create()
    {
        $user = auth()->user();

        // Solo mostrar equipos disponibles
        $equipos = Equipo::where('estado', 'Disponible')
            ->with(['marca', 'modelo', 'tipoEquipo'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        $empleados = Empleado::where('estado', 'Activo')
            ->with(['cargo', 'sucursal'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        return view('asignaciones.create', compact('equipos', 'empleados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_equipo' => 'required|exists:equipos,id',
            'id_empleado' => 'required|exists:empleados,id',
            'fecha_entrega' => 'required|date',
            'observaciones_entrega' => 'nullable|string',
        ]);

        $validated['estado_asignacion'] = 'Activa';
        $asignacion = Asignacion::create($validated);

        // Actualizar estado del equipo
        Equipo::find($validated['id_equipo'])->update(['estado' => 'Asignado']);

        return redirect()->route('asignaciones.index')->with('success', 'Asignación registrada exitosamente.');
    }

    public function show(Asignacion $asignacione)
    {
        $asignacione->load(['equipo.marca', 'equipo.modelo', 'empleado']);

        return view('asignaciones.show', compact('asignacione'));
    }

    public function edit(Asignacion $asignacione)
    {
        $user = auth()->user();

        $equipos = Equipo::with(['marca', 'modelo'])
            ->whereIn('estado', ['Disponible', 'Asignado'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        $empleados = Empleado::where('estado', 'Activo')
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        return view('asignaciones.edit', compact('asignacione', 'equipos', 'empleados'));
    }

    public function update(Request $request, Asignacion $asignacione)
    {
        $validated = $request->validate([
            'fecha_devolucion' => 'nullable|date',
            'estado_asignacion' => 'required|in:Activa,Finalizada',
            'observaciones_devolucion' => 'nullable|string',
        ]);

        $estadoAnterior = $asignacione->estado_asignacion;
        $asignacione->update($validated);

        // Si se finaliza la asignación, cambiar estado del equipo a Disponible
        if ($validated['estado_asignacion'] === 'Finalizada' && $estadoAnterior === 'Activa') {
            $asignacione->equipo->update(['estado' => 'Disponible']);
        }

        return redirect()->route('asignaciones.index')->with('success', 'Asignación actualizada exitosamente.');
    }

    public function destroy(Asignacion $asignacione)
    {
        // El método destroy original ya no se usará desde la UI, pero se mantiene por si acaso
        // Si está activa, liberar el equipo
        if ($asignacione->estado_asignacion === 'Activa') {
            $asignacione->equipo->update(['estado' => 'Disponible']);
        }

        $asignacione->delete();

        return redirect()->route('asignaciones.index')->with('success', 'Asignación eliminada exitosamente.');
    }

    public function annul(Request $request, Asignacion $asignacion)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|max:500',
        ]);

        // Si la asignación está activa, liberar el equipo
        if ($asignacion->estado_asignacion === 'Activa') {
            $asignacion->equipo->update(['estado' => 'Disponible']);
        }

        // Actualizar estado y guardar motivo
        $asignacion->update([
            'estado_asignacion' => 'Anulada',
            'motivo_anulacion' => $request->motivo_anulacion,
            'fecha_devolucion' => null // Opcional: limpiar fecha devolución si se considera que nunca existió devolución válida
        ]);

        return redirect()->route('asignaciones.index')
            ->with('success', 'La asignación ha sido anulada correctamente.');
    }

    public function return(Request $request, Asignacion $asignacion)
    {
        $validated = $request->validate([
            'fecha_devolucion' => 'required|date|before_or_equal:today',
            'observaciones_devolucion' => 'nullable|string|max:1000',
        ]);

        // Verificar que la asignación esté activa
        if ($asignacion->estado_asignacion !== 'Activa') {
            return redirect()->back()->with('error', 'Solo se pueden devolver asignaciones activas.');
        }

        // Actualizar estado de la asignación
        $asignacion->update([
            'estado_asignacion' => 'Finalizada',
            'fecha_devolucion' => $validated['fecha_devolucion'],
            'observaciones_devolucion' => $validated['observaciones_devolucion'],
        ]);

        // Liberar el equipo
        $asignacion->equipo->update(['estado' => 'Disponible']);

        return redirect()->route('asignaciones.index')
            ->with('success', 'Equipo devuelto y asignación finalizada correctamente.');
    }

    /**
     * Export asignaciones to Excel
     */
    public function export()
    {
        $user = auth()->user();
        return Excel::download(new AsignacionesExport($user), 'asignaciones_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Generate PDF for Handover Act
     */
    public function downloadActa(Asignacion $asignacion)
    {
        $asignacion->load(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo', 'empleado.area', 'empleado.cargo', 'empleado.sucursal']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.acta_entrega', compact('asignacion'));

        return $pdf->download('acta_entrega_' . $asignacion->id . '_' . $asignacion->empleado->dni . '.pdf');
    }

    /**
     * Upload Signed Handover Act
     */
    public function uploadActa(Request $request, Asignacion $asignacion)
    {
        $request->validate([
            'acta_firmada' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('acta_firmada')) {
            // Delete old file if exists
            if ($asignacion->acta_firmada_path) {
                Storage::disk('public')->delete($asignacion->acta_firmada_path);
            }

            $path = $request->file('acta_firmada')->store('actas_firmadas', 'public');

            $asignacion->update([
                'acta_firmada_path' => $path
            ]);

            return redirect()->back()->with('success', 'Acta firmada subida correctamente.');
        }

        return redirect()->back()->with('error', 'No se ha podido subir el archivo.');
    }

    /**
     * Generate PDF for Return Act
     */
    public function downloadActaDevolucion(Asignacion $asignacion)
    {
        $asignacion->load(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo', 'empleado.area', 'empleado.cargo', 'empleado.sucursal']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.acta_devolucion', compact('asignacion'));

        return $pdf->download('acta_devolucion_' . $asignacion->id . '_' . $asignacion->empleado->dni . '.pdf');
    }

    /**
     * Upload Signed Return Act
     */
    public function uploadActaDevolucion(Request $request, Asignacion $asignacion)
    {
        $request->validate([
            'acta_devolucion' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('acta_devolucion')) {
            // Delete old file if exists
            if ($asignacion->acta_devolucion_path) {
                Storage::disk('public')->delete($asignacion->acta_devolucion_path);
            }

            $path = $request->file('acta_devolucion')->store('actas_devolucion', 'public');

            $asignacion->update([
                'acta_devolucion_path' => $path
            ]);

            return redirect()->back()->with('success', 'Acta de devolución subida correctamente.');
        }

        return redirect()->back()->with('error', 'No se ha podido subir el archivo.');
    }
}
