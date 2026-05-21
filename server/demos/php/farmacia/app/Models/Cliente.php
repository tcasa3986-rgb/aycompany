<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use LogsActivity;

    protected $table = 'clientes';

    protected $fillable = [
        'documento', 'nombres', 'apellidos', 'telefono', 'email',
        'direccion', 'fecha_nacimiento', 'genero',
        'alergias', 'enfermedades_cronicas', 'puntos_fidelidad', 'activo',
        'limite_credito', 'saldo_deudor',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo'           => 'boolean',
        'limite_credito'   => 'decimal:2',
        'saldo_deudor'     => 'decimal:2',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
