<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'codigo', 'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'genero',
        'direccion', 'telefono', 'email', 'foto',
        'apoderado_nombre', 'apoderado_dni', 'apoderado_telefono',
        'apoderado_email', 'apoderado_parentesco', 'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'alumno_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'alumno_id');
    }

    public function matriculaActiva()
    {
        return $this->matriculas()->where('estado', 'activo')->latest()->first();
    }

    public function totalDeuda(): float
    {
        return $this->pagos()->where('estado', 'pendiente')->sum('monto');
    }

    public static function generarCodigo(): string
    {
        $ultimo = static::orderBy('id', 'desc')->first();
        $numero = $ultimo ? ((int) substr($ultimo->codigo, 3)) + 1 : 1;
        return 'ALU' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}
