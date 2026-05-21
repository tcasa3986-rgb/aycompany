<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ValorCritico extends Model
{
    use HasFactory;

    protected $table = 'valores_criticos';

    protected $fillable = [
        'resultado_id', 'orden_id', 'descripcion', 'notificado_a',
        'fecha_notificacion', 'estado', 'accion_tomada'
    ];

    protected $casts = ['fecha_notificacion' => 'datetime'];

    public function resultado()
    {
        return $this->belongsTo(Resultado::class);
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }
}
