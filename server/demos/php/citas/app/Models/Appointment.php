<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'specialty_id',
        'office_id',
        'appointment_type_id',
        'date',
        'end_time',
        'status',
        'notes',
        'reason',
        'cancellation_reason'
    ];

    protected $casts = [
        'date' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'in_progress' => 'En Atención',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No Asistió',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'in_progress' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
            'no_show' => 'gray',
            default => 'gray',
        };
    }

    public function type()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id');
    }
}
