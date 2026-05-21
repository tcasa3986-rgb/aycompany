<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use App\Models\Producto;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    /* ----------------- Ventas por periodo ----------------- */
    public function ventas(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);

        $ventas = Venta::with(['cliente', 'cajero'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->where('estado', 'emitida')
            ->orderBy('fecha')
            ->get();

        $totales = [
            'count'     => $ventas->count(),
            'subtotal'  => (float) $ventas->sum('subtotal'),
            'descuento' => (float) $ventas->sum('descuento'),
            'impuesto'  => (float) $ventas->sum('impuesto'),
            'total'     => (float) $ventas->sum('total'),
        ];

        if ($request->get('export') === 'pdf') {
            return Pdf::loadView('reportes.pdf.ventas', compact('ventas', 'desde', 'hasta', 'totales'))
                ->download("ventas_{$desde->format('Ymd')}_{$hasta->format('Ymd')}.pdf");
        }
        if ($request->get('export') === 'excel') {
            $rows = $ventas->map(fn($v) => [
                'Código'    => $v->codigo,
                'Fecha'     => $v->fecha?->format('d/m/Y H:i'),
                'Cliente'   => $v->cliente?->nombre_completo ?? 'Genérico',
                'Cajero'    => $v->cajero?->name,
                'Pago'      => ucfirst($v->forma_pago),
                'Subtotal'  => (float) $v->subtotal,
                'Descuento' => (float) $v->descuento,
                'IGV'       => (float) $v->impuesto,
                'Total'     => (float) $v->total,
            ])->all();
            return Excel::download(new ReporteExport($rows, 'Ventas'), "ventas_{$desde->format('Ymd')}_{$hasta->format('Ymd')}.xlsx");
        }

        return view('reportes.ventas', compact('ventas', 'desde', 'hasta', 'totales'));
    }

    /* ----------------- Top productos ----------------- */
    public function topProductos(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);

        $top = DB::table('detalle_venta')
            ->join('ventas', 'ventas.id', '=', 'detalle_venta.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_venta.producto_id')
            ->whereBetween('ventas.fecha', [$desde, $hasta])
            ->where('ventas.estado', 'emitida')
            ->where('ventas.sucursal_id', auth()->user()->current_sucursal_id)
            ->select(
                'productos.codigo',
                'productos.nombre',
                DB::raw('SUM(detalle_venta.cantidad) as cantidad'),
                DB::raw('SUM(detalle_venta.subtotal) as importe')
            )
            ->groupBy('productos.id', 'productos.codigo', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(50)
            ->get();

        if ($request->get('export') === 'pdf') {
            return Pdf::loadView('reportes.pdf.top', compact('top', 'desde', 'hasta'))
                ->download("top_productos_{$desde->format('Ymd')}_{$hasta->format('Ymd')}.pdf");
        }
        if ($request->get('export') === 'excel') {
            $rows = $top->map(fn($p) => [
                'Código'   => $p->codigo,
                'Producto' => $p->nombre,
                'Cantidad' => (int) $p->cantidad,
                'Importe'  => (float) $p->importe,
            ])->all();
            return Excel::download(new ReporteExport($rows, 'Top productos'), "top_productos_{$desde->format('Ymd')}_{$hasta->format('Ymd')}.xlsx");
        }

        return view('reportes.top', compact('top', 'desde', 'hasta'));
    }

    /* ----------------- Stock crítico ----------------- */
        $sucursalId = auth()->user()->current_sucursal_id;

        $productos = Producto::with('categoria')
            ->join('sucursal_producto', 'productos.id', '=', 'sucursal_producto.producto_id')
            ->where('sucursal_producto.sucursal_id', $sucursalId)
            ->whereColumn('sucursal_producto.stock', '<=', 'sucursal_producto.stock_minimo')
            ->where('productos.activo', true)
            ->select('productos.*', 'sucursal_producto.stock', 'sucursal_producto.stock_minimo', 'sucursal_producto.ubicacion')
            ->orderBy('sucursal_producto.stock')
            ->get();

        if ($request->get('export') === 'pdf') {
            return Pdf::loadView('reportes.pdf.stock', compact('productos'))
                ->download("stock_critico_" . now()->format('Ymd') . ".pdf");
        }
        if ($request->get('export') === 'excel') {
            $rows = $productos->map(fn($p) => [
                'Código'       => $p->codigo,
                'Producto'     => $p->nombre,
                'Categoría'    => $p->categoria?->nombre,
                'Stock'        => $p->stock,
                'Stock mínimo' => $p->stock_minimo,
                'Diferencia'   => $p->stock - $p->stock_minimo,
            ])->all();
            return Excel::download(new ReporteExport($rows, 'Stock crítico'), "stock_critico_" . now()->format('Ymd') . ".xlsx");
        }

        return view('reportes.stock', compact('productos'));
    }

    /* ----------------- Próximos a vencer ----------------- */
    public function porVencer(Request $request)
    {
        $dias = (int) $request->get('dias', 90);

        $lotes = DB::table('lotes')
            ->join('productos', 'productos.id', '=', 'lotes.producto_id')
            ->select('productos.codigo', 'productos.nombre', 'lotes.numero_lote',
                     'lotes.fecha_vencimiento', 'lotes.cantidad')
            ->where('lotes.fecha_vencimiento', '<=', now()->addDays($dias))
            ->orderBy('lotes.fecha_vencimiento')
            ->get();

        if ($request->get('export') === 'pdf') {
            return Pdf::loadView('reportes.pdf.vencer', compact('lotes', 'dias'))
                ->download("por_vencer_{$dias}d_" . now()->format('Ymd') . ".pdf");
        }
        if ($request->get('export') === 'excel') {
            $rows = $lotes->map(fn($l) => [
                'Código'       => $l->codigo,
                'Producto'     => $l->nombre,
                'Lote'         => $l->numero_lote,
                'Vencimiento'  => Carbon::parse($l->fecha_vencimiento)->format('d/m/Y'),
                'Cantidad'     => $l->cantidad,
            ])->all();
            return Excel::download(new ReporteExport($rows, "Por vencer ({$dias}d)"), "por_vencer_{$dias}d_" . now()->format('Ymd') . ".xlsx");
        }

        return view('reportes.vencer', compact('lotes', 'dias'));
    }

    /* ----------------- Helper ----------------- */
    protected function rango(Request $request): array
    {
        $desde = $request->get('desde')
            ? Carbon::parse($request->get('desde'))->startOfDay()
            : now()->startOfMonth();
        $hasta = $request->get('hasta')
            ? Carbon::parse($request->get('hasta'))->endOfDay()
            : now()->endOfDay();
        return [$desde, $hasta];
    }
}
