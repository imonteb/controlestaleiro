<?php

namespace App\Exports;

use App\Models\Atribuicao;
use App\Models\Veiculo;
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

class EstatisticasVeiculosExport implements WithEvents, WithStyles, WithTitle
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
        return 'Veículos';
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

        $vehiculos = Veiculo::ativos()->orderBy('matricula')->get();

        $estadoLabels = [
            'reparacao' => 'Reparação', 'baixa' => 'Baixa', 'licenca' => 'Licença',
            'ferias' => 'Férias', 'consulta_medica' => 'Consulta', 'formacao' => 'Formação',
        ];

        $rows = DB::table('asignacion_vehiculo')
            ->join('asignaciones', 'asignaciones.id', '=', 'asignacion_vehiculo.asignacion_id')
            ->leftJoin('peps', 'peps.id', '=', 'asignaciones.pep_id')
            ->whereBetween('asignaciones.fecha', [$this->inicio->toDateString(), $this->fin->toDateString()])
            ->select(
                'asignacion_vehiculo.vehiculo_id',
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
                $existing = $matrix[$row->vehiculo_id][$fechaStr] ?? '';
                $matrix[$row->vehiculo_id][$fechaStr] = $existing ? $existing.' / '.$label : $label;
            }
        }

        $numDias = $diasLaborais->count();

        // Row 1: Title
        $sheet->setCellValue('A1', 'Estatísticas de Veículos — '.ucfirst($this->nombreMes));
        $lastCol = $this->colLetter($numDias + 2);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Row 3: Headers
        $headerRow = 3;
        $sheet->setCellValue('A'.$headerRow, 'Veículo');
        $sheet->getColumnDimension('A')->setWidth(25);

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
        foreach ($vehiculos as $veh) {
            $label = $veh->matricula;
            if ($veh->marca || $veh->modelo) {
                $label .= ' — '.trim($veh->marca.' '.$veh->modelo);
            }
            $sheet->setCellValue('A'.$dataRow, $label);
            $totalDias = 0;

            foreach ($diasLaborais as $i => $dia) {
                $fechaStr = $dia->toDateString();
                $cellCol = $this->colLetter($i + 2);
                $value = $matrix[$veh->id][$fechaStr] ?? '';

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
            foreach ($vehiculos as $veh) {
                if (! empty($matrix[$veh->id][$fechaStr])) {
                    $count++;
                }
            }
            $cellCol = $this->colLetter($i + 2);
            $sheet->setCellValue($cellCol.$dataRow, $count > 0 ? $count : '–');
        }
        $sheet->setCellValue($totalCol.$dataRow, $vehiculos->count());

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
