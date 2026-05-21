<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'numero_recibo', 'alumno_id', 'concepto_id', 'anio_escolar', 'mes',
        'monto', 'descuento', 'monto_pagado', 'fecha_pago', 'fecha_vencimiento',
        'metodo_pago', 'estado', 'comprobante', 'observaciones', 'registrado_por',
    ];

    protected $casts = [
        'fecha_pago'        => 'date',
        'fecha_vencimiento' => 'date',
    ];

    protected static $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getNombreMesAttribute(): string
    {
        return $this->mes ? (self::$meses[$this->mes] ?? '') : '';
    }

    public static function generarNumeroRecibo(): string
    {
        $anio = date('Y');
        $ultimo = static::whereYear('created_at', $anio)->orderBy('id', 'desc')->first();
        $numero = $ultimo ? ((int) substr($ultimo->numero_recibo, -5)) + 1 : 1;
        return 'REC' . $anio . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'pagado'   => 'success',
            'pendiente'=> 'warning',
            'vencido'  => 'danger',
            'anulado'  => 'secondary',
            default    => 'info',
        };
    }
}
