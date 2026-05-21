<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'appointment_id',
        'amount',
        'status',
        'payment_method',
        'notes',
        'insurance_id',
        'insurance_coverage_amount',
        'patient_copay_amount',
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            default => $this->payment_method ?? '—',
        };
    }
}

