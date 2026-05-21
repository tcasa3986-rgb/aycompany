<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'repartidor_id', 'asignado_por', 'estado',
        'fecha_asignacion', 'fecha_recogida', 'fecha_entrega_estimada',
        'fecha_entrega_real', 'distancia_km', 'tiempo_minutos',
        'observaciones', 'firma_cliente', 'foto_evidencia',
        'calificacion', 'comentario_cliente',
    ];

    protected $casts = [
        'fecha_asignacion'      => 'datetime',
        'fecha_recogida'        => 'datetime',
        'fecha_entrega_estimada'=> 'datetime',
        'fecha_entrega_real'    => 'datetime',
        'distancia_km'          => 'decimal:2',
        'calificacion'          => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function repartidor()
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'asignado'  => 'warning',
            'recogido'  => 'info',
            'en_camino' => 'primary',
            'entregado' => 'success',
            'fallido'   => 'danger',
            'devuelto'  => 'dark',
            default     => 'secondary',
        };
    }
}
