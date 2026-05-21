<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicoReferidor extends Model
{
    use HasFactory;

    protected $table = 'medicos_referidores';

    protected $fillable = [
        'cmp', 'nombres', 'apellidos', 'especialidad', 'telefono', 'email', 'institucion', 'activo'
    ];

    protected $casts = ['activo' => 'boolean'];

    public function getNombreCompletoAttribute(): string
    {
        return "Dr. {$this->nombres} {$this->apellidos}";
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'medico_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }
}
