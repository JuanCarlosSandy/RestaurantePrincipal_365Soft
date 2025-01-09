<?php

namespace App\Exports;

use App\Inventario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;

class ProductosBajoStockExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    use Exportable;

    public function query()
    {
        return Inventario::join('articulos', 'inventarios.idarticulo', '=', 'articulos.id')
            ->join('almacens', 'inventarios.idalmacen', '=', 'almacens.id')
            ->join('proveedores', 'articulos.idproveedor', '=', 'proveedores.id')
            ->join('personas', 'proveedores.id', '=', 'personas.id')
            ->select(
                'articulos.codigo',
                'articulos.nombre as nombre_producto',
                'articulos.unidad_paquete',
                'inventarios.saldo_stock',
                'articulos.stockmin',
                'almacens.nombre_almacen',
                'personas.nombre as nombre_proveedor',
            )
            ->whereRaw('articulos.stockmin > inventarios.saldo_stock')
            ->orderBy('inventarios.id', 'desc');
    }

    public function headings(): array
    {
        return [
            'Codigo',
            'Producto',
            'Unidad X Paq.',
            'Saldo Stock',
            'Stock minimo',
            'Almancen',
            'Proveedor',
        ];
    }

    public function map($row): array
    {
        return [
            $row->codigo,
            $row->nombre_producto,
            $row->unidad_paquete,
            $row->saldo_stock,
            $row->stockmin,
            $row->nombre_almacen,
            $row->nombre_proveedor,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Añadir el título en la primera fila
                $sheet->setCellValue('A1', 'REPORTE DE PRODUCTOS BAJO STOCK');

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

                // Configurar la orientación de la hoja en horizontal
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}