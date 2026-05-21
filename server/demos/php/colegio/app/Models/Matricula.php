<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matricula extends Model
{
    protected $table = 'matriculas';

    protected $fillable = [
        'numero', 'alumno_id', 'grado_id', 'seccion_id',
        'anio_escolar', 'fecha_matricula', 'estado',
        'observaciones', 'registrado_por',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public static function generarNumero(): string
    {
        $anio = date('Y');
        $ultimo = static::whereYear('created_at', $anio)->orderBy('id', 'desc')->first();
        $numero = $ultimo ? ((int) substr($ultimo->numero, -4)) + 1 : 1;
        return 'MAT' . $anio . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
