<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenDetalle extends Model
{
    use HasFactory;

    protected $table = 'orden_detalles';

    protected $fillable = [
        'orden_id', 'prueba_id', 'precio_unitario', 'descuento', 'precio_final', 'estado'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'precio_final' => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function prueba()
    {
        return $this->belongsTo(Prueba::class);
    }

    public function resultado()
    {
        return $this->hasOne(Resultado::class);
    }
}
