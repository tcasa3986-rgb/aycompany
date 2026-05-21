<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'appointment_confirmed',
                'subject' => 'Confirmación de su cita médica',
                'body' => "Hola {paciente},\n\nLe confirmamos que su cita médica ha sido agendada exitosamente.\n\nDetalles de la cita:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nGracias por confiar en nosotros.",
                'variables' => '{paciente}, {medico}, {especialidad}, {fecha}, {hora}',
            ],
            [
                'name' => 'appointment_cancelled',
                'subject' => 'Cancelación de su cita médica',
                'body' => "Hola {paciente},\n\nLe informamos que su cita médica programada para el {fecha} a las {hora} con el Dr. {medico} ha sido cancelada.\n\nMotivo: {motivo}\n\nSi desea reagendar, por favor contáctenos.\n\nSaludos.",
                'variables' => '{paciente}, {fecha}, {hora}, {medico}, {motivo}',
            ],
            [
                'name' => 'appointment_reminder_24h',
                'subject' => 'Recordatorio: Cita médica mañana',
                'body' => "Hola {paciente},\n\nEste es un recordatorio de su próxima cita médica mañana.\n\nDetalles:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nPor favor, llegue 10 minutos antes.\n\n¡Le esperamos!",
                'variables' => '{paciente}, {medico}, {especialidad}, {fecha}, {hora}',
            ],
            [
                'name' => 'appointment_reminder_1h',
                'subject' => 'Recordatorio Urgente: Su cita médica en 1 hora',
                'body' => "Hola {paciente},\n\nEste es un recordatorio urgente. Su cita médica es dentro de 1 hora.\n\nDetalles:\n- Médico: {medico}\n- Especialidad: {especialidad}\n- Fecha: {fecha}\n- Hora: {hora}\n\nPor favor, llegue 10 minutos antes.\n\n¡Le esperamos!",
                'variables' => '{paciente}, {medico}, {especialidad}, {fecha}, {hora}',
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
