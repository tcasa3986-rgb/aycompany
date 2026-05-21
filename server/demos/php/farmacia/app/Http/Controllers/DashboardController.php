<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::count();
        $totalClientes  = Cliente::count();
        $ventasHoy      = (float) Venta::whereDate('fecha', today())->where('estado', 'emitida')->sum('total');
        $ventasMes      = (float) Venta::whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->where('estado', 'emitida')->sum('total');

        $sucursalId = auth()->user()->current_sucursal_id;

        $stockBajo = DB::table('sucursal_producto')
            ->where('sucursal_id', $sucursalId)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();

        // Ventas últimos 12 meses (para el gráfico de área)
        $serieVentas = collect();
        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $monto = (float) Venta::whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->where('estado', 'emitida')
                ->sum('total');
            $serieVentas->push([
                'mes'   => $fecha->locale('es')->isoFormat('MMM YY'),
                'monto' => $monto,
            ]);
        }

        // Top 6 productos más vendidos para gráfico de barras
        $topProductos = DB::table('detalle_venta')
            ->join('ventas', 'ventas.id', '=', 'detalle_venta.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_venta.producto_id')
            ->where('ventas.sucursal_id', $sucursalId)
            ->where('ventas.estado', 'emitida')
            ->select('productos.nombre', DB::raw('SUM(detalle_venta.cantidad) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Ventas por categoría (Top 5)
        $ventasPorCategoria = DB::table('detalle_venta')
            ->join('ventas', 'ventas.id', '=', 'detalle_venta.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_venta.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->where('ventas.sucursal_id', $sucursalId)
            ->where('ventas.estado', 'emitida')
            ->select('categorias.nombre', DB::raw('SUM(detalle_venta.subtotal) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Compras vs Ventas (Últimos 6 meses)
        $comprasVsVentas = collect();
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $ventas = (float) Venta::whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->where('estado', 'emitida')
                ->sum('total');
            $compras = (float) Compra::whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->where('estado', 'recibida')
                ->sum('total');
            $comprasVsVentas->push([
                'mes' => $fecha->locale('es')->isoFormat('MMM YY'),
                'ventas' => $ventas,
                'compras' => $compras
            ]);
        }

        // Productos próximos a vencer (90 días)
        $proximosVencer = DB::table('lotes')
            ->join('productos', 'productos.id', '=', 'lotes.producto_id')
            ->select('productos.nombre', 'lotes.numero_lote', 'lotes.fecha_vencimiento', 'lotes.cantidad')
            ->whereBetween('lotes.fecha_vencimiento', [now(), now()->addDays(90)])
            ->orderBy('lotes.fecha_vencimiento')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'totalProductos',
            'totalClientes',
            'ventasHoy',
            'ventasMes',
            'stockBajo',
            'serieVentas',
            'topProductos',
            'ventasPorCategoria',
            'comprasVsVentas',
            'proximosVencer'
        ));
    }
}
