<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
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
        $appt = $this->appointment->load(['doctor', 'specialty']);
        $clinic = config('settings.clinic_name', 'CitasMédicas');
        $address = config('settings.clinic_address', '');
        $phone = config('settings.clinic_phone', '');

        $mail = (new MailMessage)
            ->subject("⏰ Recordatorio: Cita Mañana — {$clinic}")
            ->greeting("Hola, {$notifiable->name}!")
            ->line('Este es un recordatorio de tu cita médica **mañana**:')
            ->line("📅 **Fecha y hora:** " . $appt->date->format('l, d \d\e F \d\e Y \a \l\a\s H:i'))
            ->line("👨‍⚕️ **Médico:** Dr. " . ($appt->doctor->name ?? '—'))
            ->line("🏥 **Especialidad:** " . ($appt->specialty->name ?? '—'));

        if ($address) {
            $mail->line("📍 **Dirección:** {$address}");
        }
        if ($phone) {
            $mail->line("📞 **Teléfono:** {$phone}");
        }

        return $mail
            ->line('**Recomendaciones:**')
            ->line('• Llega 10 minutos antes de tu cita')
            ->line('• Trae tu documento de identidad')
            ->line('• Si tomas medicamentos, trae la lista actualizada')
            ->action('Ver mi Portal de Paciente', url('/portal'))
            ->salutation("Hasta mañana,\n{$clinic}");
    }

    public function toDatabase(object $notifiable): array
    {
        $appt = $this->appointment;
        return [
            'type' => 'appointment_reminder',
            'title' => 'Recordatorio de cita',
            'message' => "Recuerda que mañana tienes cita con Dr. " . ($appt->doctor->name ?? '—') . " a las " . $appt->date->format('H:i') . ".",
            'url' => route('portal.appointments'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
