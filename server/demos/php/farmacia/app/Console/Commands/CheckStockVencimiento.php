<?php

namespace App\Console\Commands;

use App\Mail\AlertasMailer;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckStockVencimiento extends Command
{
    protected $signature = 'check:stock';
    protected $description = 'Revisa stock bajo y lotes por vencer y envía alertas por email';

    public function handle()
    {
        $this->info('Iniciando chequeo de alertas...');

        $bajoStock = Producto::join('sucursal_producto', 'productos.id', '=', 'sucursal_producto.producto_id')
            ->join('sucursales', 'sucursales.id', '=', 'sucursal_producto.sucursal_id')
            ->where('productos.activo', true)
            ->whereColumn('sucursal_producto.stock', '<=', 'sucursal_producto.stock_minimo')
            ->select('productos.*', 'sucursal_producto.stock', 'sucursal_producto.stock_minimo', 'sucursales.nombre as sucursal_nombre')
            ->get();

        $porVencer = Lote::with('producto')
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('stock', '>', 0)
            ->get();

        if ($bajoStock->count() > 0 || $porVencer->count() > 0) {
            $adminEmail = Setting::where('key', 'admin_email')->value('value') ?? config('mail.from.address');
            
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new AlertasMailer($bajoStock, $porVencer));
                $this->info("Alertas enviadas a {$adminEmail}");
            } else {
                $this->warn('No se ha configurado un email de administrador para alertas.');
            }
        } else {
            $this->info('No se encontraron alertas para enviar.');
        }

        return Command::SUCCESS;
    }
}
