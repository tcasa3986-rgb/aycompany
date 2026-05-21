<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categoria_id', 'codigo', 'nombre', 'descripcion',
        'precio', 'precio_delivery', 'imagen', 'unidad',
        'stock', 'disponible', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_delivery' => 'decimal:2',
        'disponible' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return asset('img/producto-default.png');
    }

    public function getPrecioFinalAttribute(): float
    {
        return $this->precio_delivery ?? $this->precio;
    }

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)->where('activo', true);
    }
}
