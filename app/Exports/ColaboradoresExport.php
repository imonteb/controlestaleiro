<?php

namespace App\Exports;

use App\Models\Colaborador;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColaboradoresExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Colaborador::query()->orderBy('numero_colaborador');
    }

    public function headings(): array
    {
        return ['numero_colaborador', 'nome', 'apelido', 'designacao_cargo', 'telefone'];
    }

    public function map($row): array
    {
        return [
            $row->numero_colaborador,
            $row->nombre,
            $row->apellido,
            $row->denominacion_cargo,
            $row->telefono,
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
