<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'id_sucursal',
        'codigo_inventario',
        'id_tipo_equipo',
        'id_marca',
        'id_modelo',
        'numero_serie',
        'caracteristicas',
        'tipo_adquisicion',
        'fecha_adquisicion',
        'costo',
        'numero_guia',
        'archivo_guia',
        'proveedor',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function tipoEquipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class, 'id_tipo_equipo');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'id_modelo');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'id_equipo');
    }

    public function reparaciones(): HasMany
    {
        return $this->hasMany(Reparacion::class, 'id_equipo');
    }

    public function bajas(): HasMany
    {
        return $this->hasMany(Baja::class, 'id_equipo');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'Disponible');
    }

    public function scopeAsignados($query)
    {
        return $query->where('estado', 'Asignado');
    }
}
