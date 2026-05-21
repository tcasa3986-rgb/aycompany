<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'orden_id', 'numero_factura', 'tipo_comprobante', 'convenio_id',
        'subtotal', 'descuento', 'igv', 'total', 'estado', 'user_id', 'observaciones'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public static function generarNumero(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_factura, 4) + 1 : 1;
        return 'FAC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
