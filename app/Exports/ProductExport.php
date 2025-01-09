<?php

namespace App\Exports;

use App\Articulo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    use Exportable;

    public function query()
    {
        return Articulo::join('categoria_producto', 'articulos.idcategoria_producto', '=', 'categoria_producto.id')
            ->select(
                'articulos.codigo',
                'articulos.nombre',
                'categoria_producto.nombre as nombre_categoria',
                'articulos.precio_venta',
                'articulos.stockmin',
                'articulos.descripcion',
                \DB::raw('IF(articulos.condicion = 1, "activo", "desactivado") as estado')
            )
            ->orderBy('articulos.nombre', 'desc');
    }

    public function headings(): array
    {
        return [
            'Codigo',
            'Nombre',
            'Categoria',
            'Precio Venta',
            'Stock',
            'Descripcion',
            'Condicion',
        ];
    }

    public function map($row): array
    {
        return [
            $row->codigo,
            $row->nombre,
            $row->nombre_categoria,
            $row->precio_venta,
            $row->stockmin,
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
                $sheet->setCellValue('A1', 'REPORTE DE PRODUCTOS DE INVENTARIO');
                
                // Unir las celdas de A1 a G1 para el título
                $sheet->mergeCells('A1:G1');

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
                $sheet->getStyle('A2:G2')->applyFromArray([
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
                foreach (range('A', 'G') as $columnID) {
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
