<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\SucursalContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    use LogsActivity, SucursalContext;

    protected $table = 'compras';

    protected $fillable = [
        'codigo', 'proveedor_id', 'user_id', 'estado',
        'subtotal', 'impuesto', 'total',
        'fecha', 'fecha_recepcion', 'observaciones',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'fecha_recepcion' => 'date',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }
}
