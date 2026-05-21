<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'alumno_id', 'seccion_id', 'fecha',
        'estado', 'observacion', 'registrado_por',
    ];

    protected $casts = ['fecha' => 'date'];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'presente'    => 'badge-success',
            'tardanza'    => 'badge-warning',
            'falta'       => 'badge-danger',
            'justificado' => 'badge-info',
            default       => 'badge-secondary',
        };
    }

    public function getEstadoIconAttribute(): string
    {
        return match($this->estado) {
            'presente'    => 'fas fa-check-circle',
            'tardanza'    => 'fas fa-clock',
            'falta'       => 'fas fa-times-circle',
            'justificado' => 'fas fa-file-medical',
            default       => 'fas fa-question-circle',
        };
    }

    public static function resumenMensual(int $alumnoId, int $seccionId, int $anio, int $mes): array
    {
        $registros = static::where('alumno_id', $alumnoId)
            ->where('seccion_id', $seccionId)
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->get();

        return [
            'presentes'    => $registros->where('estado', 'presente')->count(),
            'tardanzas'    => $registros->where('estado', 'tardanza')->count(),
            'faltas'       => $registros->where('estado', 'falta')->count(),
            'justificados' => $registros->where('estado', 'justificado')->count(),
            'total'        => $registros->count(),
        ];
    }
}
