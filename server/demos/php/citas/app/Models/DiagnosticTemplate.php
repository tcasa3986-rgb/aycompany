<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticTemplate extends Model
{
    protected $fillable = [
        'doctor_id',
        'name',
        'icd_code',
        'diagnosis_text',
        'treatment_text',
        'prescriptions_text',
        'notes_text',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
