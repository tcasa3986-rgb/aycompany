<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'repartidor', 'operador']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('numero', 'like', "%{$b}%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nombre', 'like', "%{$b}%")->orWhere('telefono', 'like', "%{$b}%"));
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }
        if ($request->filled('repartidor_id')) {
            $query->where('repartidor_id', $request->repartidor_id);
        }

        $pedidos      = $query->latest()->paginate(20)->withQueryString();
        $repartidores = Repartidor::activos()->get();

        return view('pedidos.index', compact('pedidos', 'repartidores'));
    }

    public function create()
    {
        $clientes     = Cliente::activos()->orderBy('nombre')->get();
        $productos    = Producto::disponibles()->with('categoria')->orderBy('nombre')->get();
        $repartidores = Repartidor::disponibles()->get();

        return view('pedidos.create', compact('clientes', 'productos', 'repartidores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'direccion_entrega' => 'required|string',
            'tipo_pago'         => 'required|in:efectivo,tarjeta,transferencia,yape,plin',
            'costo_delivery'    => 'required|numeric|min:0',
            'descuento'         => 'nullable|numeric|min:0',
            'items'             => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $precio   = $producto->precio_final;
                $sub      = $precio * $item['cantidad'];
                $subtotal += $sub;
                $itemsData[] = [
                    'producto_id'    => $producto->id,
                    'nombre_producto'=> $producto->nombre,
                    'precio_unitario'=> $precio,
                    'cantidad'       => $item['cantidad'],
                    'subtotal'       => $sub,
                    'notas'          => $item['notas'] ?? null,
                ];
            }

            $descuento = $request->descuento ?? 0;
            $total     = $subtotal + $request->costo_delivery - $descuento;

            $pedido = Pedido::create([
                'numero'             => Pedido::generarNumero(),
                'cliente_id'         => $request->cliente_id,
                'user_id'            => auth()->id(),
                'repartidor_id'      => $request->repartidor_id ?: null,
                'direccion_entrega'  => $request->direccion_entrega,
                'referencia_entrega' => $request->referencia_entrega,
                'distrito_entrega'   => $request->distrito_entrega,
                'tipo_pago'          => $request->tipo_pago,
                'estado_pago'        => 'pendiente',
                'costo_delivery'     => $request->costo_delivery,
                'descuento'          => $descuento,
                'subtotal'           => $subtotal,
                'total'              => $total,
                'notas'              => $request->notas,
                'fecha_programada'   => $request->fecha_programada,
            ]);

            foreach ($itemsData as $item) {
                $item['pedido_id'] = $pedido->id;
                PedidoItem::create($item);
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', "Pedido {$pedido->numero} creado exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'repartidor', 'operador', 'items.producto', 'entregas.repartidor', 'pagos']);
        $repartidores = Repartidor::disponibles()->get();
        return view('pedidos.show', compact('pedido', 'repartidores'));
    }

    public function edit(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['entregado', 'cancelado'])) {
            return back()->with('error', 'No se puede editar un pedido finalizado.');
        }
        $clientes     = Cliente::activos()->orderBy('nombre')->get();
        $productos    = Producto::disponibles()->with('categoria')->get();
        $repartidores = Repartidor::where('activo', true)->get();
        return view('pedidos.edit', compact('pedido', 'clientes', 'productos', 'repartidores'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado'    => 'required|in:pendiente,confirmado,preparando,listo,en_camino,entregado,cancelado,devuelto',
            'notas'     => 'nullable|string',
            'motivo_cancelacion' => 'nullable|string',
        ]);

        $pedido->update($request->only(['estado', 'notas', 'motivo_cancelacion', 'repartidor_id', 'fecha_programada']));

        if ($request->estado === 'entregado' && !$pedido->fecha_entrega) {
            $pedido->update(['fecha_entrega' => now()]);
        }

        return back()->with('success', 'Pedido actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmado,preparando,listo,en_camino,entregado,cancelado,devuelto']);
        $pedido->update(['estado' => $request->estado]);

        if ($request->estado === 'entregado') {
            $pedido->update(['fecha_entrega' => now()]);
            // Actualizar estado de entrega
            $pedido->entregas()->where('estado', 'en_camino')->update(['estado' => 'entregado', 'fecha_entrega_real' => now()]);
            // Marcar repartidor como disponible
            if ($pedido->repartidor) {
                $pedido->repartidor->update(['estado' => 'disponible']);
            }
        }

        return response()->json(['success' => true, 'estado' => $pedido->estado_texto]);
    }

    public function destroy(Pedido $pedido)
    {
        if ($pedido->estado === 'en_camino') {
            return back()->with('error', 'No se puede eliminar un pedido en camino.');
        }
        $pedido->update(['estado' => 'cancelado']);
        return redirect()->route('pedidos.index')->with('success', 'Pedido cancelado.');
    }
}
