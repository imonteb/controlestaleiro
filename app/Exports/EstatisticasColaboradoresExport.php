<?php

namespace App\Exports;

use App\Models\Atribuicao;
use App\Models\Colaborador;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EstatisticasColaboradoresExport implements WithEvents, WithStyles, WithTitle
{
    private Carbon $inicio;

    private Carbon $fin;

    private string $nombreMes;

    public function __construct(
        public int $mes,
        public int $ano,
    ) {
        $this->inicio = Carbon::createFromDate($ano, $mes, 1)->startOfMonth();
        $this->fin = $this->inicio->copy()->endOfMonth();
        $this->nombreMes = $this->inicio->locale('pt')->isoFormat('MMMM YYYY');
    }

    public function title(): string
    {
        return 'Colaboradores';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildMatrix($sheet);
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    private function buildMatrix(Worksheet $sheet): void
    {
        $fechasConDatos = Atribuicao::whereBetween('fecha', [$this->inicio->toDateString(), $this->fin->toDateString()])
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f)->toDateString())
            ->unique()
            ->toArray();

        $diasLaborais = collect(CarbonPeriod::create($this->inicio, $this->fin))
            ->filter(fn (Carbon $d) => ! $d->isWeekend() || in_array($d->toDateString(), $fechasConDatos))
            ->values();

        $colaboradores = Colaborador::ativos()->orderBy('apellido')->orderBy('nombre')->get();

        $estadoLabels = [
            'baixa' => 'Baixa', 'licenca' => 'Licença', 'ferias' => 'Férias',
            'consulta_medica' => 'Consulta', 'formacao' => 'Formação', 'reparacao' => 'Reparação',
        ];

        $rows = DB::table('asignacion_colaborador')
            ->join('asignaciones', 'asignaciones.id', '=', 'asignacion_colaborador.asignacion_id')
            ->leftJoin('peps', 'peps.id', '=', 'asignaciones.pep_id')
            ->whereBetween('asignaciones.fecha', [$this->inicio->toDateString(), $this->fin->toDateString()])
            ->select(
                'asignacion_colaborador.colaborador_id',
                'asignaciones.fecha',
                'peps.nombre as pep_nombre',
                'asignaciones.estado'
            )
            ->get();

        $matrix = [];
        foreach ($rows as $row) {
            $fechaStr = Carbon::parse($row->fecha)->toDateString();
            $label = $row->pep_nombre ?: ($estadoLabels[$row->estado] ?? $row->estado);
            if ($label) {
                $existing = $matrix[$row->colaborador_id][$fechaStr] ?? '';
                $matrix[$row->colaborador_id][$fechaStr] = $existing ? $existing.' / '.$label : $label;
            }
        }

        $numDias = $diasLaborais->count();

        // Row 1: Title
        $sheet->setCellValue('A1', 'Estatísticas de Colaboradores — '.ucfirst($this->nombreMes));
        $lastCol = $this->colLetter($numDias + 2);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Row 3: Headers
        $headerRow = 3;
        $sheet->setCellValue('A'.$headerRow, 'Colaborador');
        $sheet->getColumnDimension('A')->setWidth(30);

        foreach ($diasLaborais as $i => $dia) {
            $col = $this->colLetter($i + 2);
            $diaSemana = $dia->isWeekend()
                ? ($dia->isSaturday() ? 'Sáb' : 'Dom')
                : (['Seg', 'Ter', 'Qua', 'Qui', 'Sex'][$dia->dayOfWeekIso - 1] ?? '');
            $sheet->setCellValue($col.$headerRow, $diaSemana.' '.$dia->format('d'));
            $sheet->getColumnDimension($col)->setWidth(14);

            if ($dia->isWeekend()) {
                $sheet->getStyle($col.$headerRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('7f1d1d');
            }
        }

        $totalCol = $this->colLetter($numDias + 2);
        $sheet->setCellValue($totalCol.$headerRow, 'TOTAL');
        $sheet->getColumnDimension($totalCol)->setWidth(10);

        $sheet->getStyle("A{$headerRow}:{$totalCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Data rows
        $dataRow = $headerRow + 1;
        foreach ($colaboradores as $col) {
            $sheet->setCellValue('A'.$dataRow, $col->apellido.', '.$col->nombre);
            $totalDias = 0;

            foreach ($diasLaborais as $i => $dia) {
                $fechaStr = $dia->toDateString();
                $cellCol = $this->colLetter($i + 2);
                $value = $matrix[$col->id][$fechaStr] ?? '';

                if ($value) {
                    $sheet->setCellValue($cellCol.$dataRow, $value);
                    $totalDias++;
                    $sheet->getStyle($cellCol.$dataRow)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                        'font' => ['size' => 8, 'color' => ['rgb' => '1e40af']],
                    ]);
                } else {
                    $sheet->setCellValue($cellCol.$dataRow, '–');
                    $sheet->getStyle($cellCol.$dataRow)->getFont()->getColor()->setRGB('d1d5db');
                }

                if ($dia->isWeekend() && ! $value) {
                    $sheet->getStyle($cellCol.$dataRow)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF2F2');
                }
            }

            $sheet->setCellValue($totalCol.$dataRow, $totalDias > 0 ? $totalDias : '–');
            $sheet->getStyle($totalCol.$dataRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1e3a8a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            if (($dataRow - $headerRow) % 2 === 0) {
                $sheet->getStyle("A{$dataRow}:{$totalCol}{$dataRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            $dataRow++;
        }

        // Footer: totals per day
        $sheet->setCellValue('A'.$dataRow, 'TOTAL / DIA');
        foreach ($diasLaborais as $i => $dia) {
            $fechaStr = $dia->toDateString();
            $count = 0;
            foreach ($colaboradores as $col) {
                if (! empty($matrix[$col->id][$fechaStr])) {
                    $count++;
                }
            }
            $cellCol = $this->colLetter($i + 2);
            $sheet->setCellValue($cellCol.$dataRow, $count > 0 ? $count : '–');
        }
        $sheet->setCellValue($totalCol.$dataRow, $colaboradores->count());

        $sheet->getStyle("A{$dataRow}:{$totalCol}{$dataRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Borders
        $sheet->getStyle("A{$headerRow}:{$totalCol}{$dataRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Alignment for data cells
        $sheet->getStyle("A{$headerRow}:{$totalCol}{$dataRow}")->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle('A'.($headerRow + 1).":A{$dataRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->freezePane('B'.($headerRow + 1));
    }

    private function colLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $num--;
            $letter = chr(65 + ($num % 26)).$letter;
            $num = intdiv($num, 26);
        }

        return $letter;
    }
}
