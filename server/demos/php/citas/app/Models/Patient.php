<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'dob',
        'gender',
        'blood_type',
        'allergies',
        'phone',
        'address',
        'primary_doctor_id',
        'insurance_id',
        'policy_number',
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function primaryDoctor()
    {
        return $this->belongsTo(Doctor::class, 'primary_doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
