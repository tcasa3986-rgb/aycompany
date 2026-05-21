<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = [
        'name',
        'rnc_or_code',
        'contact_phone',
        'contact_email',
        'coverage_percentage',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
