<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorBlockedDate;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorScheduleController extends Controller
{
    /**
     * Show the weekly schedule management page for a doctor.
     */
    public function index(Doctor $doctor)
    {
        $doctor->load(['user', 'specialty', 'schedules', 'blockedDates']);

        // Group schedules by day_of_week for easy rendering
        $schedulesByDay = $doctor->schedules->groupBy('day_of_week');

        // Only show future or today blocked dates
        $blockedDates = $doctor->blockedDates()
            ->where('blocked_date', '>=', now()->toDateString())
            ->orderBy('blocked_date')
            ->get();

        $days = DoctorSchedule::$days;

        return view('doctors.schedule', compact('doctor', 'schedulesByDay', 'blockedDates', 'days'));
    }

    /**
     * Store a new schedule slot for a doctor.
     */
    public function store(Request $request, Doctor $doctor)
    {
        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|in:15,20,30,45,60',
        ]);

        // Check for overlapping slots same day
        $overlap = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })->exists();

        if ($overlap) {
            return back()->withInput()
                ->withErrors(['start_time' => 'El horario se superpone con una franja existente para ese día.']);
        }

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
            'is_active' => true,
        ]);

        return redirect()->route('doctors.schedule.index', $doctor)
            ->with('success', 'Franja horaria agregada correctamente.');
    }

    /**
     * Toggle active status of a schedule slot.
     */
    public function toggle(DoctorSchedule $schedule)
    {
        $schedule->update(['is_active' => !$schedule->is_active]);
        return back()->with('success', 'Estado de la franja actualizado.');
    }

    /**
     * Delete a schedule slot.
     */
    public function destroy(DoctorSchedule $schedule)
    {
        $doctorId = $schedule->doctor_id;
        $schedule->delete();
        return redirect()->route('doctors.schedule.index', $doctorId)
            ->with('success', 'Franja horaria eliminada.');
    }

    /**
     * Store a blocked date (vacation / day off).
     */
    public function storeBlocked(Request $request, Doctor $doctor)
    {
        $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        DoctorBlockedDate::updateOrCreate(
            ['doctor_id' => $doctor->id, 'blocked_date' => $request->blocked_date],
            ['reason' => $request->reason]
        );

        return redirect()->route('doctors.schedule.index', $doctor)
            ->with('success', 'Día bloqueado registrado.');
    }

    /**
     * Remove a blocked date.
     */
    public function destroyBlocked(DoctorBlockedDate $blocked)
    {
        $doctorId = $blocked->doctor_id;
        $blocked->delete();
        return redirect()->route('doctors.schedule.index', $doctorId)
            ->with('success', 'Día bloqueado eliminado.');
    }

    /**
     * AJAX – Return available time slots for a doctor on a given date.
     * GET /available-slots?doctor_id=X&date=YYYY-MM-DD
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'appointment_type_id' => 'nullable|exists:appointment_types,id',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0=Sunday … 6=Saturday

        // 1. Duration logic
        $durationMinutes = 30; // default fallout
        if ($request->filled('appointment_type_id')) {
            $type = \App\Models\AppointmentType::find($request->appointment_type_id);
            if ($type && $type->doctor_id == $request->doctor_id) {
                $durationMinutes = $type->duration_minutes;
            }
        }

        // 2. Check if date is blocked
        $isBlocked = DoctorBlockedDate::where('doctor_id', $request->doctor_id)
            ->where('blocked_date', $date->toDateString())
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'available' => false,
                'message' => 'El médico no está disponible en esta fecha (día bloqueado).',
                'slots' => [],
            ]);
        }

        // 3. Find active schedules for this day of week
        $schedules = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'available' => false,
                'message' => 'El médico no tiene horario configurado para este día.',
                'slots' => [],
            ]);
        }

        // 4. Gather all possible start times from all schedule blocks
        // Instead of strict sizes, we generate 15-minute intervals inside the schedule block
        $potentialStarts = [];
        foreach ($schedules as $schedule) {
            $current = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            while ($current->copy()->addMinutes($durationMinutes)->lte($end)) {
                $potentialStarts[] = $current->format('H:i');
                // We advance by the base slot_duration (e.g. 15 or 30 mins) configured by the doctor
                $current->addMinutes($schedule->slot_duration);
            }
        }
        $potentialStarts = array_unique($potentialStarts);
        sort($potentialStarts);

        // 5. Find already-booked appointments on that date for this doctor
        $bookedAppointments = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->get()
            ->map(function ($a) {
                // Determine end_time. Fallback to start + 30m if end_time is null for older data
                $start = Carbon::parse($a->date);
                $end = $a->end_time ? Carbon::parse($a->end_time) : $start->copy()->addMinutes(30);
                return [
                    'start' => $start,
                    'end' => $end,
                ];
            });

        // 6. Filter slots that overlap with booked appointments
        $availableSlots = [];
        foreach ($potentialStarts as $startStr) {
            $slotStart = Carbon::parse($date->toDateString() . ' ' . $startStr);
            $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

            $hasOverlap = false;
            foreach ($bookedAppointments as $booked) {
                // Overlap condition: (StartA < EndB) and (EndA > StartB)
                if ($slotStart->lt($booked['end']) && $slotEnd->gt($booked['start'])) {
                    $hasOverlap = true;
                    break;
                }
            }

            $availableSlots[] = [
                'time' => $startStr,
                'available' => !$hasOverlap,
            ];
        }

        return response()->json([
            'available' => true,
            'message' => '',
            'slots' => $availableSlots,
        ]);
    }
}
