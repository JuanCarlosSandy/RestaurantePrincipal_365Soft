<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;


class UserExport implements FromQuery, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    use Exportable;

    public function query()
{
    return User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->join('sucursales','users.idsucursal','=','sucursales.id')
            ->select(
                'personas.id',
                'personas.nombre',
                DB::raw("COALESCE(personas.tipo_documento, 'Sin Tipo de Documento') as tipo_documento"),
                DB::raw("COALESCE(personas.num_documento, 'Sin Número de Documento') as num_documento"),
                DB::raw("COALESCE(personas.direccion, 'Sin Dirección') as direccion"),
                DB::raw("COALESCE(personas.telefono, 'Sin Teléfono') as telefono"),
                DB::raw("COALESCE(personas.email, 'Sin Email') as email"),
                'users.usuario',
                DB::raw("COALESCE(roles.nombre, 'Sin Rol') as rol"),
                DB::raw("COALESCE(sucursales.nombre, 'Sin Sucursal') as sucursal")
            )
            ->orderBy('personas.id', 'desc');
}

    public function headings(): array
    {
        return [
            'Nombre',
            'Nro Documento',
            'Direccion',
            'Telefono',
            'Email',
            'Usuario',
            'Rol',
            'Sucursal',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nombre,
            $row->num_documento,
            $row->direccion,
            $row->telefono,
            $row->email,
            $row->usuario,
            $row->rol,
            $row->sucursal,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Añadir el título en la primera fila
                $sheet->setCellValue('A1', 'REPORTE DE USUARIOS');
                
                // Unir las celdas de A1 a G1 para el título
                $sheet->mergeCells('A1:H1');

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
                $sheet->getStyle('A2:H2')->applyFromArray([
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
                foreach (range('A', 'J') as $columnID) {
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




                

