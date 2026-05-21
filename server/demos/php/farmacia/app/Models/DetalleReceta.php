<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleReceta extends Model
{
    protected $table = 'detalle_receta';

    protected $fillable = ['receta_id', 'producto_id', 'cantidad', 'indicaciones'];

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
