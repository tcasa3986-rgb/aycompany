<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\SucursalContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use LogsActivity, SucursalContext;

    protected $table = 'ventas';

    protected $fillable = [
        'codigo', 'cliente_id', 'user_id', 'caja_id', 'tipo_comprobante', 'serie', 'numero',
        'subtotal', 'descuento', 'impuesto', 'total',
        'forma_pago', 'pago_recibido', 'cambio', 'puntos_canjeados',
        'estado', 'observaciones', 'fecha',
        'motivo_anulacion', 'anulada_at', 'anulada_por',
    ];

    protected $casts = [
        'fecha'      => 'datetime',
        'anulada_at' => 'datetime',
        'subtotal'   => 'decimal:2',
        'descuento'  => 'decimal:2',
        'impuesto'   => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }
}
