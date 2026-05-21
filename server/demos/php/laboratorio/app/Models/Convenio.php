<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Convenio extends Model
{
    use HasFactory;

    protected $table = 'convenios';

    protected $fillable = [
        'nombre', 'ruc', 'tipo', 'descuento_porcentaje', 'condiciones',
        'contacto_nombre', 'contacto_telefono', 'activo'
    ];

    protected $casts = ['activo' => 'boolean', 'descuento_porcentaje' => 'decimal:2'];

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
