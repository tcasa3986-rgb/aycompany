<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    protected $table = 'grados';
    protected $fillable = ['nombre', 'nivel', 'descripcion'];

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class, 'grado_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'grado_id');
    }
}
