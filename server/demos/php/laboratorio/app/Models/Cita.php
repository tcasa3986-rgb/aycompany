<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id', 'medico_id', 'fecha_hora', 'tipo_atencion', 'estado', 'motivo', 'observaciones'
    ];

    protected $casts = ['fecha_hora' => 'datetime'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(MedicoReferidor::class, 'medico_id');
    }
}
