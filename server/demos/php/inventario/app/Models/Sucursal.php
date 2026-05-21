<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'estado',
    ];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'id_sucursal');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'id_sucursal');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_sucursal');
    }
}
