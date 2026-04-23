<?php

namespace App\Exports;

use App\Models\EpiEntrega;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FichaEpiExport implements WithEvents, WithStyles, WithTitle
{
    private Carbon $inicio;

    private Carbon $fin;

    private string $nombreMes;

    public function __construct(
        public int $mes,
        public int $anio,
    ) {
        $this->inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $this->fin = $this->inicio->copy()->endOfMonth();
        $this->nombreMes = $this->inicio->locale('pt')->isoFormat('MMMM YYYY');
    }

    public function title(): string
    {
        return 'Fichas EPI';
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildFichas($sheet);
            },
        ];
    }

    private function buildFichas(Worksheet $sheet): void
    {
        $entregas = EpiEntrega::with(['epiItem', 'colaborador'])
            ->whereBetween('fecha_entrega', [$this->inicio->toDateString(), $this->fin->toDateString()])
            ->orderBy('fecha_entrega')
            ->get();

        $grouped = $entregas->groupBy('colaborador_id');

        if ($grouped->isEmpty()) {
            $sheet->setCellValue('A1', 'Nenhuma entrega de EPI registada em '.ucfirst($this->nombreMes));

            return;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);   // Nº
        $sheet->getColumnDimension('B')->setWidth(35);  // EPI
        $sheet->getColumnDimension('C')->setWidth(14);  // CA
        $sheet->getColumnDimension('D')->setWidth(8);   // Qtd
        $sheet->getColumnDimension('E')->setWidth(16);  // Data Entrega
        $sheet->getColumnDimension('F')->setWidth(10);  // Ass.
        $sheet->getColumnDimension('G')->setWidth(16);  // Data Devolução

        $row = 1;

        foreach ($grouped as $colabId => $entregasColab) {
            $colab = $entregasColab->first()->colaborador;
            $riscosUnion = $entregasColab->pluck('epiItem.riscos')->flatten()->unique()->filter()->values();

            // Collaborator header
            $sheet->setCellValue("A{$row}", 'FICHA DE ENTREGA DE EPI — '.ucfirst($this->nombreMes));
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $row++;

            $sheet->setCellValue("A{$row}", 'Colaborador:');
            $sheet->setCellValue("B{$row}", ($colab->nombre ?? '').' '.($colab->apellido ?? ''));
            $sheet->mergeCells("B{$row}:E{$row}");
            $sheet->setCellValue("F{$row}", 'Nº:');
            $sheet->setCellValue("G{$row}", $colab->numero_colaborador ?? '');
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            ]);
            $row++;

            // Table headers
            $headers = ['Nº', 'EPI', 'CA', 'Qtd', 'Data Entrega', 'Ass.', 'Data Devolução'];
            foreach ($headers as $i => $h) {
                $col = chr(65 + $i);
                $sheet->setCellValue("{$col}{$row}", $h);
            }
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '475569']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
            ]);
            $row++;

            // Data rows
            foreach ($entregasColab->values() as $idx => $e) {
                $sheet->setCellValue("A{$row}", $idx + 1);
                $epiName = ($e->epiItem->nombre ?? '—');
                if ($e->talla) {
                    $epiName .= ' ('.$e->talla.')';
                }
                $sheet->setCellValue("B{$row}", $epiName);
                $sheet->setCellValue("C{$row}", $e->epiItem->ca_numero ?? '');
                $sheet->setCellValue("D{$row}", $e->cantidad);
                $sheet->setCellValue("E{$row}", Carbon::parse($e->fecha_entrega)->format('d/m/Y'));
                $sheet->setCellValue("F{$row}", $e->firma ? '✓' : '');
                $sheet->setCellValue("G{$row}", $e->fecha_devolucion ? Carbon::parse($e->fecha_devolucion)->format('d/m/Y') : '');

                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                if ($idx % 2 === 1) {
                    $sheet->getStyle("A{$row}:G{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                }
                $row++;
            }

            // Riscos footer
            if ($riscosUnion->isNotEmpty()) {
                $sheet->setCellValue("A{$row}", 'RISCOS: '.$riscosUnion->implode(', '));
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '991B1B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                ]);
                $row++;
            }

            // Spacer between fichas
            $row += 2;
        }
    }
}
