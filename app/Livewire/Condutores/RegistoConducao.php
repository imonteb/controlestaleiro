<?php

namespace App\Livewire\Condutores;

use App\Exports\RegistoConducaoExport;
use App\Models\Atribuicao;
use App\Models\VehicleDriverLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.app')]
#[Title('Registo de Condução')]
class RegistoConducao extends Component
{
    use WithPagination;

    public string $tab = 'estado';

    public string $searchColaborador = '';

    public string $searchVeiculo = '';

    public string $dataInicio = '';

    public string $dataFim = '';

    public string $filtroAberto = '';

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatedSearchColaborador(): void
    {
        $this->resetPage();
    }

    public function updatedSearchVeiculo(): void
    {
        $this->resetPage();
    }

    public function updatedDataInicio(): void
    {
        $this->resetPage();
    }

    public function updatedDataFim(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroAberto(): void
    {
        $this->resetPage();
    }

    public function exportarExcel(): BinaryFileResponse
    {
        return Excel::download(
            new RegistoConducaoExport(
                $this->searchColaborador,
                $this->searchVeiculo,
                $this->dataInicio,
                $this->dataFim,
                $this->filtroAberto,
            ),
            'registo-conducao-'.now()->format('Ymd-His').'.xlsx',
        );
    }

    public function fecharSessao(int $id): void
    {
        VehicleDriverLog::where('id', $id)->whereNull('ended_at')->update(['ended_at' => now()]);
    }

    public function render()
    {
        $totalAbertos = VehicleDriverLog::whereNull('ended_at')->count();

        // --- Estado Actual ---
        $estadoVeiculos = collect();

        if ($this->tab === 'estado') {
            $today = now()->toDateString();

            $atribuicoes = Atribuicao::with(['veiculos', 'colaboradores'])
                ->where('fecha', $today)
                ->get();

            $vehicleIds = $atribuicoes->flatMap(fn ($a) => $a->veiculos->pluck('id'))->unique();

            $activeLogs = VehicleDriverLog::whereIn('vehicle_id', $vehicleIds)
                ->whereNull('ended_at')
                ->with('colaborador')
                ->get()
                ->keyBy('vehicle_id');

            $seen = [];
            foreach ($atribuicoes as $atrib) {
                foreach ($atrib->veiculos as $veh) {
                    if (in_array($veh->id, $seen)) {
                        continue;
                    }
                    $seen[] = $veh->id;

                    $equipoTipo = $veh->pivot->equipo_tipo;
                    $log = $activeLogs->get($veh->id);

                    $estadoVeiculos->push([
                        'veiculo_id' => $veh->id,
                        'matricula' => $veh->matricula,
                        'equipo_tipo' => $equipoTipo ?? '—',
                        'tem_condutor' => $log !== null,
                        'condutor' => $log?->colaborador?->nombre,
                        'condutor_num' => $log?->colaborador?->numero_colaborador,
                        'desde' => $log?->started_at,
                        'log_id' => $log?->id,
                    ]);
                }
            }
        }

        // --- Histórico ---
        $logs = collect();

        if ($this->tab === 'historico') {
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

            $logs = $query->paginate(20);
        }

        return view('livewire.condutores.registo-conducao', [
            'estadoVeiculos' => $estadoVeiculos,
            'logs' => $logs,
            'totalAbertos' => $totalAbertos,
        ]);
    }
}
