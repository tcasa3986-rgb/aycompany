<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'numero_orden', 'paciente_id', 'medico_id', 'convenio_id', 'user_id',
        'fecha_registro', 'diagnostico_presuntivo', 'estado', 'prioridad',
        'subtotal', 'descuento', 'total', 'pagado', 'observaciones'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'pagado' => 'boolean',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(MedicoReferidor::class, 'medico_id');
    }

    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(OrdenDetalle::class);
    }

    public function muestras()
    {
        return $this->hasMany(Muestra::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function valoresCriticos()
    {
        return $this->hasMany(ValorCritico::class);
    }

    public static function generarNumero(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_orden, 4) + 1 : 1;
        return 'ORD-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
