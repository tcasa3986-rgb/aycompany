<?php

namespace App\Http\Controllers;

use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RepartidorController extends Controller
{
    public function index(Request $request)
    {
        $query = Repartidor::with('user');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%{$b}%")
                  ->orWhere('apellido', 'like', "%{$b}%")
                  ->orWhere('telefono', 'like', "%{$b}%")
                  ->orWhere('dni', 'like', "%{$b}%");
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_vehiculo')) {
            $query->where('tipo_vehiculo', $request->tipo_vehiculo);
        }

        $repartidores = $query->withCount(['entregas' => fn($q) => $q->where('estado', 'entregado')])
            ->latest()->paginate(20)->withQueryString();

        return view('repartidores.index', compact('repartidores'));
    }

    public function create()
    {
        $usuarios = User::role('repartidor')->whereDoesntHave('repartidor')->get();
        return view('repartidores.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'dni'           => 'required|string|max:15|unique:repartidores,dni',
            'telefono'      => 'required|string|max:20',
            'telefono_alt'  => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'tipo_vehiculo' => 'required|in:moto,bicicleta,auto,pie',
            'placa'         => 'nullable|string|max:10',
            'zona_asignada' => 'nullable|string|max:100',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('repartidores', 'public');
        }
        $data['estado'] = 'disponible';
        $data['activo'] = true;

        $repartidor = Repartidor::create($data);

        return redirect()->route('repartidores.show', $repartidor)
            ->with('success', "Repartidor {$repartidor->nombre_completo} registrado.");
    }

    public function show(Repartidor $repartidor)
    {
        $entregas = $repartidor->entregas()->with('pedido.cliente')->latest()->limit(20)->get();
        $estadisticas = [
            'total_entregas'    => $repartidor->entregas()->where('estado', 'entregado')->count(),
            'entregas_mes'      => $repartidor->entregas()->whereMonth('created_at', now()->month)->where('estado', 'entregado')->count(),
            'calificacion_prom' => $repartidor->entregas()->whereNotNull('calificacion')->avg('calificacion'),
            'tiempo_promedio'   => $repartidor->entregas()->whereNotNull('tiempo_minutos')->avg('tiempo_minutos'),
        ];
        return view('repartidores.show', compact('repartidor', 'entregas', 'estadisticas'));
    }

    public function edit(Repartidor $repartidor)
    {
        $usuarios = User::role('repartidor')->get();
        return view('repartidores.edit', compact('repartidor', 'usuarios'));
    }

    public function update(Request $request, Repartidor $repartidor)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'dni'           => "required|string|max:15|unique:repartidores,dni,{$repartidor->id}",
            'telefono'      => 'required|string|max:20',
            'tipo_vehiculo' => 'required|in:moto,bicicleta,auto,pie',
            'placa'         => 'nullable|string|max:10',
            'zona_asignada' => 'nullable|string|max:100',
            'estado'        => 'required|in:disponible,ocupado,inactivo,descanso',
            'activo'        => 'boolean',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($repartidor->foto) Storage::disk('public')->delete($repartidor->foto);
            $data['foto'] = $request->file('foto')->store('repartidores', 'public');
        }
        $data['activo'] = $request->boolean('activo');
        $repartidor->update($data);

        return redirect()->route('repartidores.show', $repartidor)
            ->with('success', 'Repartidor actualizado.');
    }

    public function destroy(Repartidor $repartidor)
    {
        $repartidor->update(['activo' => false, 'estado' => 'inactivo']);
        return redirect()->route('repartidores.index')
            ->with('success', 'Repartidor desactivado.');
    }

    public function cambiarEstado(Request $request, Repartidor $repartidor)
    {
        $request->validate(['estado' => 'required|in:disponible,ocupado,inactivo,descanso']);
        $repartidor->update(['estado' => $request->estado]);
        return response()->json(['success' => true]);
    }
}
