<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'codigo','descripcion','tipo','valor','monto_minimo','descuento_maximo',
        'usos_maximos','usos_actuales','valido_desde','valido_hasta',
        'solo_primer_pedido','activo',
    ];

    protected $casts = [
        'valor'             => 'decimal:2',
        'monto_minimo'      => 'decimal:2',
        'descuento_maximo'  => 'decimal:2',
        'valido_desde'      => 'date',
        'valido_hasta'      => 'date',
        'solo_primer_pedido'=> 'boolean',
        'activo'            => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Validar si el cupón puede aplicarse a un subtotal y cliente.
     * Devuelve descuento aplicable o lanza Exception con motivo.
     */
    public function calcularDescuento(float $subtotal, ?Cliente $cliente = null): float
    {
        if (!$this->activo) {
            throw new \Exception('El cupón no está activo.');
        }
        $hoy = Carbon::today();
        if ($this->valido_desde && $hoy->lt($this->valido_desde)) {
            throw new \Exception('El cupón aún no es válido.');
        }
        if ($this->valido_hasta && $hoy->gt($this->valido_hasta)) {
            throw new \Exception('El cupón ha expirado.');
        }
        if ($this->usos_maximos !== null && $this->usos_actuales >= $this->usos_maximos) {
            throw new \Exception('Cupón ya alcanzó su límite de usos.');
        }
        if ($subtotal < (float) $this->monto_minimo) {
            throw new \Exception("Pedido mínimo S/ {$this->monto_minimo} para usar este cupón.");
        }
        if ($this->solo_primer_pedido && $cliente && $cliente->pedidos()->exists()) {
            throw new \Exception('Cupón solo válido para primer pedido.');
        }

        $descuento = $this->tipo === 'porcentaje'
            ? round($subtotal * ((float) $this->valor / 100), 2)
            : (float) $this->valor;

        if ($this->descuento_maximo) {
            $descuento = min($descuento, (float) $this->descuento_maximo);
        }
        return min($descuento, $subtotal);
    }
}
