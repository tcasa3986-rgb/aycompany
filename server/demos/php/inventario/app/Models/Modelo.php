<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = [
        'id_marca',
        'nombre',
        'estado',
    ];

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'id_modelo');
    }
}
