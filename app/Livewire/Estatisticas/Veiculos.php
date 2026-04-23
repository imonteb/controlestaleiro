<?php

namespace App\Livewire\Estatisticas;

use App\Models\Atribuicao;
use App\Models\Veiculo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Estatísticas de Veículos')]
class Veiculos extends Component
{
    public int $mes;

    public int $ano;

    public bool $apenasAtivos = true;

    public string $buscar = '';

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

    public function proximoMes(): void
    {
        $d = Carbon::createFromDate($this->ano, $this->mes, 1)->addMonth();
        $this->mes = $d->month;
        $this->ano = $d->year;
    }

    public function render()
    {
        $inicio = Carbon::createFromDate($this->ano, $this->mes, 1)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $fechasConDatos = Atribuicao::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f)->toDateString())
            ->unique()
            ->toArray();

        $diasLaborais = collect(CarbonPeriod::create($inicio, $fin))
            ->filter(fn (Carbon $d) => ! $d->isWeekend() || in_array($d->toDateString(), $fechasConDatos))
            ->values();

        $vehQuery = Veiculo::query();
        if ($this->apenasAtivos) {
            $vehQuery->ativos();
        }
        if ($this->buscar !== '') {
            $term = '%'.$this->buscar.'%';
            $vehQuery->where(function ($q) use ($term) {
                $q->where('matricula', 'like', $term)
                    ->orWhere('marca', 'like', $term)
                    ->orWhere('modelo', 'like', $term);
            });
        }
        $veiculos = $vehQuery->orderBy('matricula')->get();

        $estadoLabels = [
            'reparacao' => 'Reparação',
            'baixa' => 'Baixa',
            'licenca' => 'Licença',
            'ferias' => 'Férias',
            'consulta_medica' => 'Consulta',
            'formacao' => 'Formação',
        ];

        $rows = DB::table('asignacion_vehiculo')
            ->join('asignaciones', 'asignaciones.id', '=', 'asignacion_vehiculo.asignacion_id')
            ->leftJoin('peps', 'peps.id', '=', 'asignaciones.pep_id')
            ->whereBetween('asignaciones.fecha', [$inicio->toDateString(), $fin->toDateString()])
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
            if ($row->pep_nombre) {
                $matrix[$row->vehiculo_id][$fechaStr][] = [
                    'label' => $row->pep_nombre,
                    'tipo' => 'pep',
                ];
            } elseif ($row->estado && isset($estadoLabels[$row->estado])) {
                $matrix[$row->vehiculo_id][$fechaStr][] = [
                    'label' => $estadoLabels[$row->estado],
                    'tipo' => 'estado',
                ];
            }
        }

        $totalesVeiculos = [];
        foreach ($veiculos as $veiculo) {
            $totalesVeiculos[$veiculo->id] = count($matrix[$veiculo->id] ?? []);
        }

        $totalesDia = [];
        foreach ($diasLaborais as $dia) {
            /** @var \Carbon\Carbon $dia */
            $dataStr = $dia->toDateString();
            $count = 0;
            foreach ($veiculos as $veiculo) {
                if (! empty($matrix[$veiculo->id][$dataStr])) {
                    $count++;
                }
            }
            $totalesDia[$dataStr] = $count;
        }

        return view('livewire.estatisticas.veiculos', [
            'veiculos' => $veiculos,
            'diasLaborais' => $diasLaborais,
            'matrix' => $matrix,
            'totalesVeiculos' => $totalesVeiculos,
            'totalesDia' => $totalesDia,
            'nomeMes' => Carbon::createFromDate($this->ano, $this->mes, 1)
                ->locale('pt')
                ->isoFormat('MMMM YYYY'),
        ]);
    }
}
