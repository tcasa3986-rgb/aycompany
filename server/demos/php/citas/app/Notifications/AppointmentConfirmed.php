<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appt = $this->appointment->load(['doctor.user', 'specialty']);
        $clinic = config('settings.clinic_name', 'CitasMédicas');

        $template = EmailTemplate::where('name', 'appointment_confirmed')->first();

        if (!$template) {
            // Fallback
            return (new MailMessage)
                ->subject("✅ Cita Confirmada — {$clinic}")
                ->view('emails.appointment_confirmed', [
                    'appointment' => $appt,
                    'patientName' => $notifiable->name,
                ]);
        }

        $subject = str_replace(
            ['{paciente}', '{medico}', '{especialidad}', '{fecha}', '{hora}'],
            [$notifiable->name, $appt->doctor->user->name ?? '', $appt->specialty->name ?? '', $appt->date->format('d/m/Y'), $appt->date->format('H:i')],
            $template->subject
        );

        $body = str_replace(
            ['{paciente}', '{medico}', '{especialidad}', '{fecha}', '{hora}'],
            [$notifiable->name, $appt->doctor->user->name ?? '', $appt->specialty->name ?? '', $appt->date->format('d/m/Y'), $appt->date->format('H:i')],
            $template->body
        );

        return (new MailMessage)
            ->subject($subject)
            ->line(new \Illuminate\Support\HtmlString(nl2br(e($body))));
    }

    public function toDatabase(object $notifiable): array
    {
        $appt = $this->appointment;
        return [
            'type' => 'appointment_confirmed',
            'title' => 'Cita confirmada',
            'message' => "Tu cita del " . $appt->date->format('d/m/Y H:i') . " con Dr. " . ($appt->doctor->name ?? '—') . " ha sido confirmada.",
            'url' => route('portal.appointments'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
