<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reactivo extends Model
{
    use HasFactory;

    protected $table = 'reactivos';

    protected $fillable = [
        'area_id', 'codigo', 'nombre', 'marca', 'proveedor', 'unidad_medida',
        'stock_actual', 'stock_minimo', 'precio_unitario', 'fecha_vencimiento',
        'lote', 'estado', 'activo'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'precio_unitario' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(AreaLaboratorio::class, 'area_id');
    }

    public function actualizarEstado(): void
    {
        if ($this->stock_actual <= 0) {
            $this->estado = 'Sin stock';
        } elseif ($this->stock_actual <= $this->stock_minimo) {
            $this->estado = 'Stock bajo';
        } elseif ($this->fecha_vencimiento && $this->fecha_vencimiento->isPast()) {
            $this->estado = 'Vencido';
        } else {
            $this->estado = 'Disponible';
        }
        $this->save();
    }
}
