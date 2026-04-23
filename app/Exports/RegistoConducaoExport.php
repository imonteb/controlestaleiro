<?php

namespace App\Exports;

use App\Models\VehicleDriverLog;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistoConducaoExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private string $searchColaborador = '',
        private string $searchVeiculo = '',
        private string $dataInicio = '',
        private string $dataFim = '',
        private string $filtroAberto = '',
    ) {}

    public function query()
    {
        $query = VehicleDriverLog::with(['colaborador', 'veiculo'])
            ->orderBy('started_at', 'desc');

        if ($this->searchColaborador !== '') {
            $s = '%'.$this->searchColaborador.'%';
            $query->whereHas('colaborador', fn ($q) => $q->where('nombre', 'like', $s)->orWhere('numero_colaborador', 'like', $s));
        }

        if ($this->searchVeiculo !== '') {
            $s = '%'.$this->searchVeiculo.'%';
            $query->whereHas('veiculo', fn ($q) => $q->where('matricula', 'like', $s));
        }

        if ($this->dataInicio !== '') {
            $query->where('started_at', '>=', Carbon::parse($this->dataInicio)->startOfDay());
        }

        if ($this->dataFim !== '') {
            $query->where('started_at', '<=', Carbon::parse($this->dataFim)->endOfDay());
        }

        if ($this->filtroAberto === 'sim') {
            $query->whereNull('ended_at');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Viatura',
            'Condutor',
            'Nº Colaborador',
            'Início',
            'Fim',
            'Duração',
            'Takeover de',
            'Hora Takeover',
        ];
    }

    public function map($log): array
    {
        $aberto = is_null($log->ended_at);

        if ($aberto) {
            $duracao = 'Em curso';
        } else {
            $diff = $log->started_at->diff($log->ended_at);
            $duracao = $diff->format('%hh %im');
        }

        return [
            $log->veiculo?->matricula ?? '—',
            $log->colaborador?->nombre ?? '—',
            $log->colaborador?->numero_colaborador ?? '—',
            $log->started_at->format('d/m/Y H:i'),
            $aberto ? 'Em curso' : $log->ended_at->format('d/m/Y H:i'),
            $duracao,
            $log->takeover_from_name ?? '',
            $log->takeover_at ? $log->takeover_at->format('d/m/Y H:i') : '',
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

    public function title(): string
    {
        return 'Registo de Condução';
    }
}
