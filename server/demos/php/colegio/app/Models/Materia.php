<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    protected $table = 'materias';
    protected $fillable = ['nombre', 'codigo', 'nivel', 'horas_semanales', 'color', 'activo'];
    protected $casts   = ['activo' => 'boolean'];

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'materia_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'materia_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
