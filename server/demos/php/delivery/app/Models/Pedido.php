<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero', 'cliente_id', 'user_id', 'repartidor_id',
        'direccion_entrega', 'referencia_entrega', 'distrito_entrega',
        'estado', 'tipo_pago', 'estado_pago',
        'subtotal', 'costo_delivery', 'descuento', 'total',
        'notas', 'motivo_cancelacion', 'fecha_programada', 'fecha_entrega',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'costo_delivery' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_programada' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    public static function generarNumero(): string
    {
        $ultimo = static::withTrashed()->latest('id')->first();
        $numero = $ultimo ? ($ultimo->id + 1) : 1;
        return 'PED-' . date('Ymd') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function repartidor()
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'pendiente'   => 'warning',
            'confirmado'  => 'info',
            'preparando'  => 'primary',
            'listo'       => 'secondary',
            'en_camino'   => 'info',
            'entregado'   => 'success',
            'cancelado'   => 'danger',
            'devuelto'    => 'dark',
            default       => 'secondary',
        };
    }

    public function getEstadoTextoAttribute(): string
    {
        return match($this->estado) {
            'pendiente'   => 'Pendiente',
            'confirmado'  => 'Confirmado',
            'preparando'  => 'En Preparación',
            'listo'       => 'Listo para recoger',
            'en_camino'   => 'En Camino',
            'entregado'   => 'Entregado',
            'cancelado'   => 'Cancelado',
            'devuelto'    => 'Devuelto',
            default       => ucfirst($this->estado),
        };
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }
}
