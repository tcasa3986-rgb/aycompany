<?php
use App\Models\Appointment;
use App\Notifications\AppointmentReminderNotification;
use App\Notifications\AppointmentCancelled;
use App\Notifications\AppointmentConfirmed;

$a = Appointment::first();

if ($a) {
    echo "Mandando reminder 24h...\n";
    $a->patient->user->notify(new AppointmentReminderNotification($a, '24h'));

    echo "Mandando reminder 1h...\n";
    $a->patient->user->notify(new AppointmentReminderNotification($a, '1h'));

    echo "Mandando cancelled...\n";
    $a->patient->user->notify(new AppointmentCancelled($a));

    echo "Mandando confirmed...\n";
    $a->patient->user->notify(new AppointmentConfirmed($a));
}

echo "Done.\n";
