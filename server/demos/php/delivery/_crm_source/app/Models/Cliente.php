<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre', 'apellido', 'email', 'telefono', 'telefono_alt',
        'direccion', 'referencia', 'ciudad', 'distrito',
        'latitud', 'longitud', 'tipo', 'notas', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    public function getTotalPedidosAttribute(): int
    {
        return $this->pedidos()->count();
    }

    public function getTotalGastadoAttribute(): float
    {
        return $this->pedidos()
            ->where('estado', 'entregado')
            ->sum('total');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
