<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resultado extends Model
{
    use HasFactory;

    protected $table = 'resultados';

    protected $fillable = [
        'orden_detalle_id', 'muestra_id', 'valor', 'unidad', 'valores_referencia',
        'interpretacion', 'metodo', 'equipo', 'validado_por',
        'fecha_validacion', 'valor_critico', 'notificado', 'observaciones'
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
        'valor_critico' => 'boolean',
        'notificado' => 'boolean',
    ];

    public function ordenDetalle()
    {
        return $this->belongsTo(OrdenDetalle::class);
    }

    public function muestra()
    {
        return $this->belongsTo(Muestra::class);
    }

    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    public function valorCritico()
    {
        return $this->hasOne(ValorCritico::class);
    }
}
