<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    protected $table = 'secciones';
    protected $fillable = ['grado_id', 'nombre', 'turno', 'capacidad'];

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'seccion_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->grado->nombre . ' - Sección ' . $this->nombre;
    }
}
