<?php

namespace App\Models;

use App\Traits\SucursalContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use SucursalContext;
    protected $table = 'cajas';

    protected $fillable = [
        'user_id', 'monto_apertura', 'monto_cierre', 'total_ventas',
        'apertura', 'cierre', 'estado', 'observaciones',
    ];

    protected $casts = [
        'apertura' => 'datetime',
        'cierre'   => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function getEsperadoAttribute(): float
    {
        $ingresos = (float) $this->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $egresos  = (float) $this->movimientos()->where('tipo', 'egreso')->sum('monto');
        $ventas   = $this->estado === 'cerrada'
            ? (float) $this->total_ventas
            : (float) $this->ventas()->where('estado', 'emitida')->sum('total');
        return (float) $this->monto_apertura + $ingresos - $egresos + $ventas;
    }
}
