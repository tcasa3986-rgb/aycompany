<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = ['user_id', 'specialty_id', 'collegiate_number', 'biography'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class)->orderBy('day_of_week')->orderBy('start_time');
    }

    public function blockedDates()
    {
        return $this->hasMany(DoctorBlockedDate::class)->orderBy('blocked_date');
    }

    public function offices()
    {
        return $this->hasMany(Office::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function appointmentTypes()
    {
        return $this->hasMany(AppointmentType::class);
    }
}
