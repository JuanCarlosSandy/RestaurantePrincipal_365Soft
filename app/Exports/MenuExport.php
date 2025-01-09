<?php

namespace App\Exports;

use App\Menu;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;

class MenuExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    use Exportable;

    public function query()
    {
        return Menu::join('categoria_menu', 'menu.idcategoria_menu', '=', 'categoria_menu.id')
            ->select(
                'menu.codigo',
                'menu.nombre',
                'categoria_menu.nombre as categoria_menu',
                'menu.precio_venta',
                'menu.descripcion',
                \DB::raw('IF(menu.condicion = 1, "activo", "desactivado") as estado')
            )
            ->orderBy('menu.nombre', 'desc');
    }

    public function headings(): array
    {
        return [
            'Codigo',
            'Nombre',
            'Categoria',
            'Precio Venta',
            'Descripcion',
            'Condicion',
        ];
    }

    public function map($row): array
    {
        return [
            $row->codigo,
            $row->nombre,
            $row->categoria_menu,
            $row->precio_venta,
            $row->descripcion,
            $row->estado,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Añadir el título en la primera fila
                $sheet->setCellValue('A1', 'REPORTE DE PLATOS DEL MENU');
                
                // Unir las celdas de A1 a G1 para el título
                $sheet->mergeCells('A1:F1');

                // Aplicar estilo al título
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Aplicar estilo a la segunda fila (encabezados de columnas)
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'AEFFE3',
                        ],
                    ],
                ]);

                // Ajustar automáticamente el ancho de las columnas
                foreach (range('A', 'F') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}