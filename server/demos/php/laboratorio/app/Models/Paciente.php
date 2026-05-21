<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'historia_clinica', 'tipo_documento', 'numero_documento',
        'nombres', 'apellido_paterno', 'apellido_materno',
        'fecha_nacimiento', 'sexo', 'telefono', 'email',
        'direccion', 'distrito', 'ciudad', 'tipo_sangre',
        'alergias', 'antecedentes', 'activo'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean'
    ];

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
