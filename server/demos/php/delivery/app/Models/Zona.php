<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'distrito', 'costo_delivery', 'tiempo_estimado_min',
        'monto_minimo_pedido', 'descripcion', 'activo',
    ];

    protected $casts = [
        'costo_delivery'      => 'decimal:2',
        'monto_minimo_pedido' => 'decimal:2',
        'activo'              => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function scopeActivas($q)
    {
        return $q->where('activo', true)->orderBy('distrito')->orderBy('nombre');
    }
}
