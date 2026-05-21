<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'record_date',
        'chief_complaint',
        'diagnosis',
        'treatment',
        'notes',
        'prescriptions',
        'referred_to',
        'blood_pressure',
        'heart_rate',
        'temperature',
        'weight',
        'height',
        'oxygen_saturation',
        'is_private',
        'attachments',
    ];

    protected $casts = [
        'record_date' => 'date',
        'is_private' => 'boolean',
        'attachments' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
