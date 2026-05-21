<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEquipo extends Model
{
    protected $table = 'tipos_equipo';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'id_tipo_equipo');
    }
}
