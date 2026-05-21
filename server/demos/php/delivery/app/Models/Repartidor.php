<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repartidor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'repartidores';

    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'dni', 'telefono', 'telefono_alt',
        'email', 'foto', 'tipo_vehiculo', 'placa', 'zona_asignada',
        'estado', 'calificacion', 'total_entregas', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'calificacion' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return "https://ui-avatars.com/api/?name={$this->nombre}+{$this->apellido}&background=198754&color=fff&size=128";
    }

    public function getVehiculoIconoAttribute(): string
    {
        return match($this->tipo_vehiculo) {
            'moto' => 'bi-bicycle',
            'bicicleta' => 'bi-bicycle',
            'auto' => 'bi-car-front',
            'pie' => 'bi-person-walking',
            default => 'bi-truck',
        };
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible')->where('activo', true);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
