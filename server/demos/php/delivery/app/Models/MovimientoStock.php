<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock';

    protected $fillable = [
        'producto_id', 'user_id', 'pedido_id', 'tipo',
        'cantidad', 'stock_anterior', 'stock_nuevo',
        'costo_unitario', 'motivo',
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'stock_anterior'  => 'integer',
        'stock_nuevo'     => 'integer',
        'costo_unitario'  => 'decimal:2',
    ];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function usuario()  { return $this->belongsTo(User::class, 'user_id'); }
    public function pedido()   { return $this->belongsTo(Pedido::class); }

    public function tipoTexto(): string
    {
        return [
            'entrada' => 'Entrada',
            'salida'  => 'Salida (venta)',
            'ajuste'  => 'Ajuste manual',
            'merma'   => 'Merma',
        ][$this->tipo] ?? $this->tipo;
    }

    public function tipoBadge(): string
    {
        return [
            'entrada' => 'success',
            'salida'  => 'primary',
            'ajuste'  => 'warning',
            'merma'   => 'danger',
        ][$this->tipo] ?? 'secondary';
    }
}
