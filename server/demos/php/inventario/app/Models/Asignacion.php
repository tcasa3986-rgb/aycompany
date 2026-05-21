<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'id_equipo',
        'id_empleado',
        'fecha_entrega',
        'fecha_devolucion',
        'observaciones_entrega',
        'observaciones_devolucion',
        'motivo_anulacion',
        'estado_asignacion',
        'acta_firmada_path',
        'acta_devolucion_path',
        'imagen_devolucion_1',
        'imagen_devolucion_2',
        'imagen_devolucion_3',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado_asignacion', 'Activa');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado_asignacion', 'Finalizada');
    }
}
