<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reparacion extends Model
{
    protected $table = 'reparaciones';

    protected $fillable = [
        'id_equipo',
        'fecha_ingreso',
        'fecha_salida',
        'descripcion_problema',
        'descripcion_solucion',
        'tecnico_asignado',
        'costo_estimado',
        'costo_real',
        'estado_reparacion',
        'proveedor_servicio',
        'observaciones_salida',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_salida' => 'date',
        'costo_estimado' => 'decimal:2',
        'costo_real' => 'decimal:2',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo');
    }
}
