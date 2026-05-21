<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelled extends Notification implements ShouldQueue
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

        $template = EmailTemplate::where('name', 'appointment_cancelled')->first();

        if (!$template) {
            // Fallback
            $mail = (new MailMessage)
                ->subject("❌ Cita Cancelada — {$clinic}")
                ->greeting("Hola, {$notifiable->name}.")
                ->line('Lamentamos informarte que tu cita médica ha sido **cancelada**.')
                ->line("📅 **Fecha original:** " . $appt->date->format('d/m/Y H:i'))
                ->line("👨‍⚕️ **Médico:** Dr. " . ($appt->doctor->name ?? '—'));

            if ($appt->cancellation_reason) {
                $mail->line("📝 **Motivo:** {$appt->cancellation_reason}");
            }

            return $mail
                ->action('Agendar nueva cita', url('/portal/appointments'))
                ->line('Si tienes dudas, comunícate con nosotros.')
                ->salutation("Atentamente,\n{$clinic}");
        }

        $subject = str_replace(
            ['{paciente}', '{fecha}', '{hora}', '{medico}', '{motivo}'],
            [$notifiable->name, $appt->date->format('d/m/Y'), $appt->date->format('H:i'), $appt->doctor->user->name ?? '', $appt->cancellation_reason ?? 'No especificado'],
            $template->subject
        );

        $body = str_replace(
            ['{paciente}', '{fecha}', '{hora}', '{medico}', '{motivo}'],
            [$notifiable->name, $appt->date->format('d/m/Y'), $appt->date->format('H:i'), $appt->doctor->user->name ?? '', $appt->cancellation_reason ?? 'No especificado'],
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
            'type' => 'appointment_cancelled',
            'title' => 'Cita cancelada',
            'message' => "Tu cita del " . $appt->date->format('d/m/Y H:i') . " fue cancelada." .
                ($appt->cancellation_reason ? " Motivo: {$appt->cancellation_reason}" : ''),
            'url' => route('portal.appointments'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
