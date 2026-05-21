<?php

namespace App\Exports;

use App\Models\Asignacion;
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

class AsignacionesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Asignacion::with(['equipo.marca', 'equipo.modelo', 'empleado.sucursal', 'empleado.area']);

        if (!$this->user->isAdmin() && $this->user->id_sucursal) {
            $query->whereHas('equipo', function ($q) {
                $q->where('id_sucursal', $this->user->id_sucursal);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE ASIGNACIONES - SISTEMA TI'], // Título principal
            ['Generado por: ' . $this->user->name . ' | Fecha: ' . date('d/m/Y H:i')], // Subtítulo
            [], // Espacio en blanco
            [
                'CÓDIGO EQUIPO',
                'TIPO',
                'MARCA / MODELO',
                'EMPLEADO',
                'DNI',
                'ÁREA',
                'SUCURSAL',
                'FECHA ENTREGA',
                'FECHA DEVOLUCIÓN',
                'ESTADO',
                'OBSERVACIONES ENTREGA',
                'OBSERVACIONES DEVOLUCIÓN'
            ]
        ];
    }

    public function map($asignacion): array
    {
        return [
            $asignacion->equipo->codigo_inventario,
            $asignacion->equipo->tipoEquipo->nombre ?? 'N/A',
            ($asignacion->equipo->marca->nombre ?? '') . ' ' . ($asignacion->equipo->modelo->nombre ?? ''),
            $asignacion->empleado->nombreCompleto(),
            $asignacion->empleado->dni,
            $asignacion->empleado->area->nombre ?? 'N/A',
            $asignacion->empleado->sucursal->nombre ?? 'N/A',
            $asignacion->fecha_entrega ? $asignacion->fecha_entrega->format('d/m/Y') : 'N/A',
            $asignacion->fecha_devolucion ? $asignacion->fecha_devolucion->format('d/m/Y') : '-',
            $asignacion->estado_asignacion,
            $asignacion->observaciones_entrega ?? '',
            $asignacion->observaciones_devolucion ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados de columna (Fila 4)
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']], // Dark slate
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
                $columnCount = 'L'; // Última columna (12 columnas)
    
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

                // 4. Center specific columns
                // H: Fecha Entrega, I: Fecha Devolución, J: Estado
                $sheet->getStyle('H5:J' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function title(): string
    {
        return 'Asignaciones ' . date('Y-m-d');
    }
}
