<?php

namespace App\Livewire\Condutores;

use App\Exports\RegistoConducaoExport;
use App\Models\VehicleDriverLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Registo de Condução')]
class RegistoConducao extends Component
{
    use WithPagination;

    public string $searchColaborador = '';

    public string $searchVeiculo = '';

    public string $dataInicio = '';

    public string $dataFim = '';

    public string $filtroAberto = ''; // '' = todos, 'sim' = sessões abertas

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

    public function exportarExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
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

        return view('livewire.condutores.registo-conducao', [
            'logs' => $query->paginate(20),
            'totalAbertos' => VehicleDriverLog::whereNull('ended_at')->count(),
        ]);
    }
}
