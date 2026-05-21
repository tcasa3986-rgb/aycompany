<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders {--dry-run : List appointments without sending}';
    protected $description = 'Send exact 24h and 1h reminders to patients';

    public function handle(): int
    {
        if (config('settings.notify_reminder_24h', '1') !== '1') {
            $this->info('Reminders are disabled in system settings. Skipping.');
            return self::SUCCESS;
        }

        $now = Carbon::now()->startOfMinute();

        // 24 Hours in the future (matching exactly to the minute)
        $in24Hours = $now->copy()->addDay();

        // 1 Hour in the future (matching exactly to the minute)
        $in1Hour = $now->copy()->addHour();

        // 1. Process 24h Reminders
        $appointments24h = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date', $in24Hours->toDateString())
            ->whereTime('date', $in24Hours->toTimeString())
            ->where('status', 'confirmed')
            ->where('reminded_24h', false)
            ->get();

        foreach ($appointments24h as $appointment) {
            $this->sendReminder($appointment, 24);
        }

        // 2. Process 1h Reminders
        $appointments1h = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date', $in1Hour->toDateString())
            ->whereTime('date', $in1Hour->toTimeString())
            ->where('status', 'confirmed')
            ->where('reminded_1h', false)
            ->get();

        foreach ($appointments1h as $appointment) {
            $this->sendReminder($appointment, 1);
        }

        $total = $appointments24h->count() + $appointments1h->count();
        if ($total > 0) {
            $this->info("Processed {$total} total reminders.");
        }

        return self::SUCCESS;
    }

    private function sendReminder(Appointment $appointment, int $hours)
    {
        $patient = $appointment->patient;
        $user = $patient?->user;

        if (!$user || !$user->email) {
            $this->warn("Skipped Appointment #{$appointment->id} - No email.");
            return;
        }

        if ($this->option('dry-run')) {
            $this->line("[DRY-RUN] Will send {$hours}h reminder to: {$user->email} for apt #{$appointment->id}");
            return;
        }

        try {
            $user->notify(new AppointmentReminderNotification($appointment, $hours));

            // Mark as reminded
            if ($hours == 24) {
                $appointment->reminded_24h = true;
            } else {
                $appointment->reminded_1h = true;
            }
            $appointment->save();

            $this->info("Sent {$hours}h reminder to {$user->email} for apt #{$appointment->id}");
        } catch (\Exception $e) {
            $this->error("Failed to send reminder to {$user->email}: " . $e->getMessage());
        }
    }
}
