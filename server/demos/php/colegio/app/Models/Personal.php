<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personal extends Model
{
    protected $table = 'personal';

    protected $fillable = [
        'user_id', 'dni', 'nombres', 'apellidos', 'tipo',
        'especialidad', 'telefono', 'email', 'direccion',
        'fecha_ingreso', 'salario', 'estado', 'foto',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'salario'       => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function getTipoBadgeAttribute(): string
    {
        return match($this->tipo) {
            'docente'        => 'primary',
            'administrativo' => 'info',
            'directivo'      => 'danger',
            'auxiliar'       => 'warning',
            default          => 'secondary',
        };
    }
}
