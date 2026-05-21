<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'remitente_id', 'destinatario_id', 'asunto',
        'cuerpo', 'leido', 'leido_en', 'archivado',
    ];

    protected $casts = [
        'leido'    => 'boolean',
        'archivado'=> 'boolean',
        'leido_en' => 'datetime',
    ];

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function marcarLeido(): void
    {
        if (!$this->leido) {
            $this->update(['leido' => true, 'leido_en' => now()]);
        }
    }
}
