<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waitlist extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_type_id',
        'requested_date_from',
        'requested_date_to',
        'status',
        'notes',
    ];

    protected $casts = [
        'requested_date_from' => 'date',
        'requested_date_to' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'En Espera',
            'notified' => 'Notificado',
            'resolved' => 'Resuelto (Agendado)',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'yellow',
            'notified' => 'blue',
            'resolved' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}
