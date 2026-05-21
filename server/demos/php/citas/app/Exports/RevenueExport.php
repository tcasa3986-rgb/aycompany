<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RevenueExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Invoice::with(['appointment.patient', 'appointment.doctor']);

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Paciente', 'Médico', 'Fecha Cita', 'Monto (S/)', 'Método de Pago', 'Estado', 'Registrado'];
    }

    public function map($row): array
    {
        $methodMap = ['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia'];
        $statusMap = ['pending' => 'Pendiente', 'paid' => 'Pagado', 'cancelled' => 'Cancelado'];

        return [
            $row->id,
            $row->appointment->patient->name ?? '—',
            'Dr. ' . ($row->appointment->doctor->name ?? '—'),
            $row->appointment?->date?->format('d/m/Y H:i') ?? '—',
            number_format($row->amount, 2),
            $methodMap[$row->payment_method] ?? $row->payment_method ?? '—',
            $statusMap[$row->status] ?? $row->status,
            $row->created_at->format('d/m/Y'),
        ];
    }
}
