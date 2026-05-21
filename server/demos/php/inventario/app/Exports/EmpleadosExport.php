<?php

namespace App\Exports;

use App\Models\Empleado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmpleadosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Empleado::with(['sucursal', 'area', 'cargo']);

        if (!$this->user->isAdmin()) {
            $query->where('id_sucursal', $this->user->id_sucursal);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE EMPLEADOS - SISTEMA TI'], // Título principal
            ['Generado por: ' . $this->user->name . ' | Fecha: ' . date('d/m/Y H:i')], // Subtítulo
            [], // Espacio en blanco
            [
                'DNI',
                'NOMBRES',
                'APELLIDOS',
                'SUCURSAL',
                'ÁREA',
                'CARGO',
                'ESTADO',
                'FECHA REGISTRO'
            ]
        ];
    }

    public function map($empleado): array
    {
        return [
            $empleado->dni,
            $empleado->nombres,
            $empleado->apellidos,
            $empleado->sucursal->nombre ?? 'N/A',
            $empleado->area->nombre ?? 'N/A',
            $empleado->cargo->nombre ?? 'N/A',
            $empleado->estado,
            $empleado->created_at ? $empleado->created_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados de columna (Fila 4)
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']], // Dark slate (Tailwind gray-800 equivalent)
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Estilo para el Título Principal (Fila 1)
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Estilo para el Subtítulo (Fila 2)
            2 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '64748B']], // Slate-500
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $sheet->getHighestRow();
                $columnCount = 'H'; // Última columna (8 columnas)
    
                // 1. Merge cells for Title and Subtitle
                $sheet->mergeCells('A1:' . $columnCount . '1');
                $sheet->mergeCells('A2:' . $columnCount . '2');

                // 2. Borders for the data table (from row 4 down)
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCBD5E1'], // Slate-300
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                // Aplicar bordes solo si hay datos (filas > 4)
                if ($rowCount >= 4) {
                    $sheet->getStyle('A4:' . $columnCount . $rowCount)->applyFromArray($styleArray);
                }

                // 3. Zebra striping for data rows (starting from row 5)
                for ($row = 5; $row <= $rowCount; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $columnCount . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'], // Slate-50
                            ],
                        ]);
                    }
                }

                // 4. Center specific columns (e.g., Dates, Status, DNI)
                // A: DNI, G: Estado, H: Fecha
                $sheet->getStyle('A5:A' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // DNI left
                $sheet->getStyle('G5:G' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H5:H' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function title(): string
    {
        return 'Empleados ' . date('Y-m-d');
    }
}
