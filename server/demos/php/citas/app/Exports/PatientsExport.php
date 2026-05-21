<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PatientsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection()
    {
        $query = Patient::with('user');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas(
                'user',
                fn($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            )->orWhere('phone', 'like', "%{$search}%");
        }

        if (!empty($this->filters['gender'])) {
            $query->where('gender', $this->filters['gender']);
        }

        if (!empty($this->filters['blood_type'])) {
            $query->where('blood_type', $this->filters['blood_type']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Email', 'Teléfono', 'Género', 'Fecha de Nacimiento', 'Tipo de Sangre', 'Alergias', 'Registrado'];
    }

    public function map($row): array
    {
        $genderMap = ['male' => 'Masculino', 'female' => 'Femenino', 'other' => 'Otro'];

        return [
            $row->id,
            $row->user->name ?? '—',
            $row->user->email ?? '—',
            $row->phone ?? '—',
            $genderMap[$row->gender] ?? '—',
            $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d/m/Y') : '—',
            $row->blood_type ?? '—',
            $row->allergies ?? '—',
            $row->created_at->format('d/m/Y'),
        ];
    }
}
