<?php

namespace App\Exports;

use App\Models\Equipo;
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

class EquiposExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Equipo::with(['marca', 'modelo', 'tipoEquipo', 'sucursal']);

        if (!$this->user->isAdmin()) {
            $query->where('id_sucursal', $this->user->id_sucursal);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE INVENTARIO - SISTEMA TI'], // Título principal
            ['Generado por: ' . $this->user->name . ' | Fecha: ' . date('d/m/Y H:i')], // Subtítulo
            [], // Espacio en blanco
            [
                'CÓDIGO',
                'N° SERIE',
                'TIPO',
                'MARCA',
                'MODELO',
                'ESTADO',
                'SUCURSAL',
                'FECHA COMPRA',
                'PRECIO',
                'PROVEEDOR',
                'OBSERVACIONES'
            ]
        ];
    }

    public function map($equipo): array
    {
        return [
            $equipo->codigo_inventario,
            $equipo->numero_serie,
            $equipo->tipoEquipo->nombre ?? 'N/A',
            $equipo->marca->nombre ?? 'N/A',
            $equipo->modelo->nombre ?? 'N/A',
            $equipo->estado,
            $equipo->sucursal->nombre ?? 'N/A',
            $equipo->fecha_adquisicion ? $equipo->fecha_adquisicion->format('d/m/Y') : 'N/A',
            $equipo->precio_compra ? '$' . number_format($equipo->precio_compra, 2) : 'N/A',
            $equipo->proveedor ?? 'N/A',
            $equipo->observaciones ?? '',
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
                $columnCount = 'K'; // Última columna (11 columnas)
    
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

                // 4. Center specific columns (e.g., Date, Status)
                // H: Fecha, I: Precio (Right aligned usually), F: Estado
                $sheet->getStyle('H5:H' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F5:F' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I5:I' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }

    public function title(): string
    {
        return 'Inventario ' . date('Y-m-d');
    }
}
