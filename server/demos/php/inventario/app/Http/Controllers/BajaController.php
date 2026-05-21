<?php

namespace App\Http\Controllers;

use App\Models\Baja;
use App\Models\Equipo;
use Illuminate\Http\Request;

class BajaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $bajas = Baja::with(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->whereHas('equipo', function ($q) use ($user) {
                    $q->where('id_sucursal', $user->id_sucursal);
                });
            })
            ->when($request->motivo, function ($query, $motivo) {
                $query->where('motivo', $motivo);
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('equipo', function ($q) use ($search) {
                    $q->where('codigo_inventario', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bajas.index', compact('bajas'));
    }

    public function create()
    {
        $user = auth()->user();

        // Solo equipos disponibles o en mal estado (no asignados activamente)
        $equipos = Equipo::with(['marca', 'modelo', 'tipoEquipo'])
            ->whereIn('estado', ['Disponible', 'En Reparacion'])
            ->when(!$user->isAdmin() && $user->id_sucursal, function ($query) use ($user) {
                $query->where('id_sucursal', $user->id_sucursal);
            })
            ->get();

        return view('bajas.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_equipo' => 'required|exists:equipos,id',
            'fecha_baja' => 'required|date',
            'motivo' => 'required|in:Obsolescencia,Daño Irreparable,Perdida,Robo,Otro',
            'descripcion' => 'required|string',
            'autorizado_por' => 'nullable|string|max:100',
        ]);

        $baja = Baja::create($validated);

        // Actualizar estado del equipo a "De Baja"
        $equipo = Equipo::find($validated['id_equipo']);
        $equipo->update(['estado' => 'De Baja']);

        return redirect()->route('bajas.index')->with('success', 'Baja registrada exitosamente.');
    }

    public function show(Baja $baja)
    {
        $baja->load(['equipo.marca', 'equipo.modelo', 'equipo.tipoEquipo', 'equipo.sucursal']);
        return view('bajas.show', compact('baja'));
    }

    public function edit(Baja $baja)
    {
        return view('bajas.edit', compact('baja'));
    }

    public function update(Request $request, Baja $baja)
    {
        $validated = $request->validate([
            'fecha_baja' => 'required|date',
            'motivo' => 'required|in:Obsolescencia,Daño Irreparable,Perdida,Robo,Otro',
            'descripcion' => 'required|string',
            'autorizado_por' => 'nullable|string|max:100',
        ]);

        $baja->update($validated);

        return redirect()->route('bajas.index')->with('success', 'Baja actualizada exitosamente.');
    }

    public function destroy(Baja $baja)
    {
        // Restaurar equipo a disponible si se elimina el registro de baja
        $baja->equipo->update(['estado' => 'Disponible']);

        $baja->delete();

        return redirect()->route('bajas.index')->with('success', 'Baja eliminada exitosamente. El equipo ha sido restaurado.');
    }
}
