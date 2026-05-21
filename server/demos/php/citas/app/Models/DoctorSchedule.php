<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static array $days = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::$days[$this->day_of_week] ?? '—';
    }

    /**
     * Generate all time slots for this schedule block.
     * Returns array of strings like ['08:00', '08:30', '09:00', ...]
     */
    public function generateSlots(): array
    {
        $slots = [];
        $current = Carbon::createFromTimeString($this->start_time);
        $end = Carbon::createFromTimeString($this->end_time);
        $duration = (int) $this->slot_duration;

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($duration);
        }

        return $slots;
    }
}
