<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorBlockedDate extends Model
{
    protected $fillable = [
        'doctor_id',
        'blocked_date',
        'reason',
    ];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
