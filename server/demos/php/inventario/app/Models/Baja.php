<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baja extends Model
{
    protected $table = 'bajas';

    protected $fillable = [
        'id_equipo',
        'fecha_baja',
        'motivo',
        'observaciones',
        'acta_baja_path',
        'descripcion_motivo',
        'id_usuario_responsable',
    ];

    protected $casts = [
        'fecha_baja' => 'date',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'id_equipo');
    }
}
