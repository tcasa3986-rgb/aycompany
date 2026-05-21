<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prueba extends Model
{
    use HasFactory;

    protected $table = 'pruebas';

    protected $fillable = [
        'area_id', 'codigo', 'nombre', 'descripcion', 'muestra_tipo',
        'tiempo_resultado', 'precio', 'unidad', 'valores_referencia', 'activo'
    ];

    protected $casts = ['activo' => 'boolean', 'precio' => 'decimal:2'];

    public function area()
    {
        return $this->belongsTo(AreaLaboratorio::class, 'area_id');
    }

    public function ordenDetalles()
    {
        return $this->hasMany(OrdenDetalle::class);
    }
}
