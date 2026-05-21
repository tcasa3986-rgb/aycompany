<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoPago extends Model
{
    protected $table = 'conceptos_pago';
    protected $fillable = ['nombre', 'descripcion', 'monto', 'tipo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'concepto_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
