<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AreaLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'areas_laboratorio';

    protected $fillable = ['nombre', 'codigo', 'descripcion', 'color', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function pruebas()
    {
        return $this->hasMany(Prueba::class, 'area_id');
    }

    public function reactivos()
    {
        return $this->hasMany(Reactivo::class, 'area_id');
    }
}
