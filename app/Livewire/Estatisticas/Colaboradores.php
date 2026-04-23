<?php

namespace App\Livewire\Estatisticas;

use App\Models\Atribuicao;
use App\Models\Colaborador;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Estatísticas de Colaboradores')]
class Colaboradores extends Component
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

        $colQuery = Colaborador::query();
        if ($this->apenasAtivos) {
            $colQuery->ativos();
        }
        if ($this->buscar !== '') {
            $term = '%'.$this->buscar.'%';
            $colQuery->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('apellido', 'like', $term)
                    ->orWhere('numero_colaborador', 'like', $term);
            });
        }
        $colaboradores = $colQuery->orderBy('apellido')->orderBy('nombre')->get();

        $estadoLabels = [
            'baixa' => 'Baixa',
            'licenca' => 'Licença',
            'ferias' => 'Férias',
            'consulta_medica' => 'Consulta',
            'formacion' => '📚 Formação',
            'reparacao' => 'Reparação',
        ];

        $rows = DB::table('asignacion_colaborador')
            ->join('asignaciones', 'asignaciones.id', '=', 'asignacion_colaborador.asignacion_id')
            ->leftJoin('peps', 'peps.id', '=', 'asignaciones.pep_id')
            ->whereBetween('asignaciones.fecha', [$inicio->toDateString(), $fin->toDateString()])
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
            if ($row->pep_nombre) {
                $matrix[$row->colaborador_id][$fechaStr][] = [
                    'label' => $row->pep_nombre,
                    'tipo' => 'pep',
                ];
            } elseif ($row->estado && isset($estadoLabels[$row->estado])) {
                $matrix[$row->colaborador_id][$fechaStr][] = [
                    'label' => $estadoLabels[$row->estado],
                    'tipo' => 'estado',
                ];
            }
        }

        $totalesColab = [];
        foreach ($colaboradores as $col) {
            $totalesColab[$col->id] = count($matrix[$col->id] ?? []);
        }

        $totalesDia = [];
        foreach ($diasLaborais as $dia) {
            /** @var \Carbon\Carbon $dia */
            $fechaStr = $dia->toDateString();
            $count = 0;
            foreach ($colaboradores as $col) {
                if (! empty($matrix[$col->id][$fechaStr])) {
                    $count++;
                }
            }
            $totalesDia[$fechaStr] = $count;
        }

        return view('livewire.estatisticas.colaboradores', [
            'colaboradores' => $colaboradores,
            'diasLaborais' => $diasLaborais,
            'matrix' => $matrix,
            'totalesColab' => $totalesColab,
            'totalesDia' => $totalesDia,
            'nomeMes' => Carbon::createFromDate($this->ano, $this->mes, 1)
                ->locale('pt')
                ->isoFormat('MMMM YYYY'),
        ]);
    }
}
