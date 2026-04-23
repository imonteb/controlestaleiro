<?php

namespace App\Livewire\Epis;

use App\Exports\FichaEpiExport;
use App\Models\EpiEntrega;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Historico extends Component
{
    public int $mes;

    public int $ano;

    public string $search = '';

    public function mount(): void
    {
        $this->mes = (int) now()->format('m');
        $this->ano = (int) now()->format('Y');
    }

    public function mesAnterior(): void
    {
        $d = Carbon::createFromDate($this->ano, $this->mes, 1)->subMonth();
        $this->mes = $d->month;
        $this->ano = $d->year;
    }

    public function mesSeguinte(): void
    {
        $d = Carbon::createFromDate($this->ano, $this->mes, 1)->addMonth();
        $this->mes = $d->month;
        $this->ano = $d->year;
    }

    public function exportarExcel()
    {
        $nombreMes = Carbon::createFromDate($this->ano, $this->mes, 1)
            ->locale('pt')
            ->isoFormat('MMMM_YYYY');

        return Excel::download(
            new FichaEpiExport($this->mes, $this->ano),
            "fichas_epi_{$nombreMes}.xlsx"
        );
    }

    public function render()
    {
        $inicio = Carbon::createFromDate($this->ano, $this->mes, 1)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();
        $nombreMes = $inicio->locale('pt')->isoFormat('MMMM YYYY');

        // Get collaborators who had deliveries this month
        $entregas = EpiEntrega::with(['epiItem', 'colaborador'])
            ->whereBetween('fecha_entrega', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha_entrega')
            ->get();

        $colaboradoresConEntregas = $entregas->groupBy('colaborador_id');

        // Filter by search
        if ($this->search) {
            $s = mb_strtolower($this->search);
            $colaboradoresConEntregas = $colaboradoresConEntregas->filter(function ($entregas) use ($s) {
                $c = $entregas->first()->colaborador;

                return $c && (
                    str_contains(mb_strtolower($c->nombre ?? ''), $s) ||
                    str_contains(mb_strtolower($c->apellido ?? ''), $s)
                );
            });
        }

        return view('livewire.epis.historico', [
            'nombreMes' => $nombreMes,
            'colaboradoresConEntregas' => $colaboradoresConEntregas,
        ])->title('Histórico Mensal EPI');
    }
}
