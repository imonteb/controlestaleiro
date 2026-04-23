<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FerramentasExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Referência',
            'Designação',
            'Marca',
            'Modelo',
            'Nº Série',
            'Família',
            'Periodicidade (Meses)',
            'Tipo Documentação',
            'Estado Operacional',
            'Localização',
            'Próxima Verificação',
            'Status Preventivo',
        ];
    }

    public function map($row): array
    {
        $ultimoLog = $row->ultimoLog;

        return [
            $row->referencia,
            $row->designacao,
            $row->marca,
            $row->modelo,
            $row->num_serie,
            $row->familia,
            $row->periodicidade_meses,
            $row->tipo_documentacao,
            $row->estado_operacional,
            $row->localizacao ?: '—',
            $ultimoLog && $ultimoLog->proxima_verificacao ? $ultimoLog->proxima_verificacao->format('d/m/Y') : '—',
            $row->status_preventivo,
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
