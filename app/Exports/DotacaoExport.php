<?php

namespace App\Exports;

use App\Models\Colaborador;
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

class DotacaoExport implements WithEvents, WithStyles, WithTitle
{
    public function __construct(
        private readonly int $colaboradorId,
    ) {}

    public function title(): string
    {
        return 'Dotação EPI';
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->build($event->sheet->getDelegate());
            },
        ];
    }

    private function build(Worksheet $sheet): void
    {
        $colaborador = Colaborador::find($this->colaboradorId);

        if (! $colaborador) {
            $sheet->setCellValue('A1', 'Colaborador não encontrado.');

            return;
        }

        $entregas = EpiEntrega::with('epiItem')
            ->where('colaborador_id', $this->colaboradorId)
            ->orderBy('fecha_entrega')
            ->get();

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(35);  // EPI
        $sheet->getColumnDimension('B')->setWidth(10);  // Ref
        $sheet->getColumnDimension('C')->setWidth(14);  // Unidade
        $sheet->getColumnDimension('D')->setWidth(16);  // Última entrega
        $sheet->getColumnDimension('E')->setWidth(16);  // Total recebido
        $sheet->getColumnDimension('F')->setWidth(14);  // Devolvido
        $sheet->getColumnDimension('G')->setWidth(14);  // Em seu poder

        $row = 1;

        // Title row
        $sheet->setCellValue("A{$row}", 'DOTAÇÃO EPI — '.mb_strtoupper($colaborador->nombre.' '.$colaborador->apellido));
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // Colaborador info
        $sheet->setCellValue("A{$row}", 'Nº Mecanográfico:');
        $sheet->setCellValue("B{$row}", $colaborador->numero_colaborador ?? '—');
        $sheet->mergeCells("B{$row}:C{$row}");
        $sheet->setCellValue("D{$row}", 'Cargo:');
        $sheet->setCellValue("E{$row}", $colaborador->denominacion_cargo ?? '—');
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("D{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", 'Gerado em:');
        $sheet->setCellValue("B{$row}", Carbon::now()->format('d/m/Y H:i'));
        $sheet->mergeCells("B{$row}:G{$row}");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setItalic(false);
        $row++;

        // Spacer
        $row++;

        // Table header
        $headers = ['EPI', 'Ref.', 'Unidade', 'Última Entrega', 'Total Recebido', 'Devolvido', 'Em Seu Poder'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}{$row}", $h);
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $headerRow = $row;
        $row++;

        // Data rows grouped by EPI
        $dotacao = $entregas
            ->groupBy('epi_item_id')
            ->map(function ($rows) {
                $item = $rows->first()->epiItem;
                $totalRecibido = $rows->sum('cantidad');
                $totalDevuelto = $rows->whereNotNull('fecha_devolucion')->sum('cantidad');

                return [
                    'epi' => $item,
                    'total_recibido' => $totalRecibido,
                    'total_devuelto' => $totalDevuelto,
                    'en_poder' => $totalRecibido - $totalDevuelto,
                    'ultima_entrega' => $rows->max('fecha_entrega'),
                ];
            })
            ->sortByDesc('en_poder')
            ->values();

        foreach ($dotacao as $idx => $d) {
            $sheet->setCellValue("A{$row}", $d['epi']->nombre ?? '—');
            $sheet->setCellValue("B{$row}", $d['epi']->codigo ?? '');
            $sheet->setCellValue("C{$row}", $d['epi']->unidade ?? '');
            $sheet->setCellValue("D{$row}", $d['ultima_entrega'] ? Carbon::parse($d['ultima_entrega'])->format('d/m/Y') : '');
            $sheet->setCellValue("E{$row}", $d['total_recibido']);
            $sheet->setCellValue("F{$row}", $d['total_devuelto']);
            $sheet->setCellValue("G{$row}", $d['en_poder']);

            $rowStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];

            if ($d['en_poder'] > 0) {
                $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']];
            } elseif ($idx % 2 === 1) {
                $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']];
            }

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($rowStyle);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Highlight "Em Seu Poder" in red if > 0
            if ($d['en_poder'] > 0) {
                $sheet->getStyle("G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                ]);
            }

            $row++;
        }

        // Totals row
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("E{$row}", $dotacao->sum('total_recibido'));
        $sheet->setCellValue("F{$row}", $dotacao->sum('total_devuelto'));
        $sheet->setCellValue("G{$row}", $dotacao->sum('en_poder'));
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a8a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1e3a8a']]],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Freeze header row
        $sheet->freezePane('A'.($headerRow + 1));
    }
}
