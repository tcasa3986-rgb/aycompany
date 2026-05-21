<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receta extends Model
{
    protected $table = 'recetas';

    protected $fillable = [
        'codigo', 'cliente_id', 'user_id',
        'medico', 'especialidad', 'cmp',
        'fecha', 'retenida', 'diagnostico', 'observaciones',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'retenida' => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function farmaceutico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleReceta::class);
    }
}
