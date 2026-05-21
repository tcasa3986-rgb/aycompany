<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function modelos(): HasMany
    {
        return $this->hasMany(Modelo::class, 'id_marca');
    }

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'id_marca');
    }
}
