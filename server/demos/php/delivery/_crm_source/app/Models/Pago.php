<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'registrado_por', 'referencia', 'metodo',
        'monto', 'vuelto', 'estado', 'comprobante_tipo',
        'comprobante_numero', 'notas', 'fecha_pago',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'vuelto'     => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getMetodoIconoAttribute(): string
    {
        return match($this->metodo) {
            'efectivo'     => 'bi-cash-coin',
            'tarjeta'      => 'bi-credit-card',
            'transferencia'=> 'bi-bank',
            'yape'         => 'bi-phone',
            'plin'         => 'bi-phone-fill',
            default        => 'bi-wallet2',
        };
    }
}
