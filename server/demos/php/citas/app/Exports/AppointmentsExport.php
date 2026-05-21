<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Appointment::with(['patient', 'doctor', 'specialty']);

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['doctor_id'])) {
            $query->where('doctor_id', $this->filters['doctor_id']);
        }
        if (!empty($this->filters['specialty_id'])) {
            $query->where('specialty_id', $this->filters['specialty_id']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderByDesc('date')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Paciente', 'Médico', 'Especialidad', 'Fecha', 'Estado', 'Motivo'];
    }

    public function map($row): array
    {
        $statusMap = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'in_progress' => 'En Atención',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No Asistió',
        ];

        return [
            $row->id,
            $row->patient->name ?? '—',
            'Dr. ' . ($row->doctor->name ?? '—'),
            $row->specialty->name ?? '—',
            $row->date?->format('d/m/Y H:i') ?? '—',
            $statusMap[$row->status] ?? $row->status,
            $row->reason ?? '—',
        ];
    }
}
