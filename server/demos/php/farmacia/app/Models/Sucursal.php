<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = ['nombre', 'direccion', 'telefono', 'es_principal', 'activo'];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sucursal_user')
            ->withPivot('es_predeterminada')
            ->withTimestamps();
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'sucursal_producto')
            ->withPivot(['stock', 'stock_minimo', 'ubicacion'])
            ->withTimestamps();
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
