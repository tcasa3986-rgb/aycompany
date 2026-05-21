<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Muestra extends Model
{
    use HasFactory;

    protected $table = 'muestras';

    protected $fillable = [
        'orden_id', 'codigo_muestra', 'tipo_muestra', 'fecha_toma', 'tomado_por', 'estado', 'observaciones'
    ];

    protected $casts = ['fecha_toma' => 'datetime'];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function tomadoPor()
    {
        return $this->belongsTo(User::class, 'tomado_por');
    }

    public function resultados()
    {
        return $this->hasMany(Resultado::class);
    }
}
