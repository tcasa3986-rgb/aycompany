<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use LogsActivity;

    protected $table = 'productos';

    protected $fillable = [
        'codigo', 'nombre', 'principio_activo', 'presentacion', 'concentracion',
        'codigo_atc', 'imagen',
        'categoria_id', 'proveedor_id', 'tipo',
        'precio_compra', 'precio_venta',
        'requiere_receta', 'activo',
    ];

    protected $casts = [
        'precio_compra'    => 'decimal:2',
        'precio_venta'     => 'decimal:2',
        'requiere_receta'  => 'boolean',
        'activo'           => 'boolean',
    ];

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_producto')
            ->withPivot(['stock', 'stock_minimo', 'ubicacion'])
            ->withTimestamps();
    }

    public function getStockActualAttribute(): int
    {
        $sucursalId = auth()->user()?->current_sucursal_id;
        if (!$sucursalId) return 0;

        return (int) $this->sucursales()->where('sucursal_id', $sucursalId)->first()?->pivot->stock ?? 0;
    }

    public function getEsBajoStockAttribute(): bool
    {
        $sucursalId = auth()->user()?->current_sucursal_id;
        if (!$sucursalId) return false;

        $inv = $this->sucursales()->where('sucursal_id', $sucursalId)->first();
        if (!$inv) return false;

        return $inv->pivot->stock <= $inv->pivot->stock_minimo;
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }
}
