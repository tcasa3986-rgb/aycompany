<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Appointment $appointment, public string $type = '24h')
    {
        // type should be '24h' or '1h'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appt = $this->appointment->load(['doctor.user', 'specialty', 'patient.user']);
        $clinic = config('settings.clinic_name', 'CitasMédicas');

        $templateName = $this->type === '24h' ? 'appointment_reminder_24h' : 'appointment_reminder_1h';
        $template = EmailTemplate::where('name', $templateName)->first();

        // Standard variables replacement for our templates
        $subject = 'Recordatorio de Cita Médica';
        $body = "Hola {$notifiable->name},\n\nTienes una cita próxima programada.\n\nFecha: " . $appt->date->format('d/m/Y') . "\nHora: " . $appt->date->format('H:i');

        if ($template) {
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
        }

        return (new MailMessage)
            ->subject($subject . " — {$clinic}")
            ->line(new \Illuminate\Support\HtmlString(nl2br(e($body))));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
