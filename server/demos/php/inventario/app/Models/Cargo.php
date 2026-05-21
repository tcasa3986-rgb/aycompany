<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = [
        'id_area',
        'nombre',
        'estado',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'id_cargo');
    }
}
