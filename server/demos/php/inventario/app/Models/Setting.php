<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_nombre',
        'empresa_direccion',
        'empresa_telefono',
        'empresa_ruc',
        'empresa_logo',
        'currency_symbol',
    ];
}
