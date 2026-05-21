<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $table = 'notas';

    protected $fillable = [
        'alumno_id', 'materia_id', 'seccion_id', 'anio_escolar',
        'bimestre', 'nota', 'promedio_bimestral', 'estado',
        'observacion', 'registrado_por',
    ];

    protected $casts = ['nota' => 'decimal:2', 'promedio_bimestral' => 'decimal:2'];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public static function calcularEstado(float $nota, float $minima = 11): string
    {
        return $nota >= $minima ? 'aprobado' : 'desaprobado';
    }

    public static function calcularPromedio(int $alumnoId, int $materiaId, int $seccionId, int $anio): ?float
    {
        $notas = static::where('alumno_id', $alumnoId)
            ->where('materia_id', $materiaId)
            ->where('seccion_id', $seccionId)
            ->where('anio_escolar', $anio)
            ->whereNotNull('nota')
            ->pluck('nota');

        return $notas->count() > 0 ? round($notas->avg(), 2) : null;
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'aprobado'     => 'badge-success',
            'desaprobado'  => 'badge-danger',
            default        => 'badge-secondary',
        };
    }
}
