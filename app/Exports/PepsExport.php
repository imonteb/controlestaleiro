<?php

namespace App\Exports;

use App\Models\Pep;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PepsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Pep::query()->with(['locacion', 'tipoTrabajo'])->orderBy('nombre');
    }

    public function headings(): array
    {
        return ['nome', 'localizacao', 'tipo_trabalho'];
    }

    public function map($row): array
    {
        return [
            $row->nombre,
            $row->locacion->nombre ?? '',
            $row->tipoTrabajo->nombre ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '09143b']],
            ],
        ];
    }
}
