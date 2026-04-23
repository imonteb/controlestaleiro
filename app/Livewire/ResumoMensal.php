<?php

namespace App\Livewire;

use App\Models\Atribuicao;
use App\Models\Localizacao;
use App\Models\Pep;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Resumo Mensal')]
class ResumoMensal extends Component
{
    public int $mes;

    public int $ano;

    public string $filterLocalizacao = '';

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

        // Días del mes: laborales siempre + fines de semana solo si tienen datos
        $fechasConDatos = Atribuicao::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f)->toDateString())
            ->unique()
            ->toArray();

        $diasLaborais = collect(CarbonPeriod::create($inicio, $fin))
            ->filter(fn (Carbon $d) => ! $d->isWeekend() || in_array($d->toDateString(), $fechasConDatos))
            ->values();

        // Obtener todos los PEPs (sin Estaleiro)
        $pepsQuery = Pep::with(['localizacao', 'tipoTrabalho'])
            ->ativos()
            ->whereRaw('LOWER(nombre) != ?', ['estaleiro']);
        if ($this->filterLocalizacao !== '') {
            $pepsQuery->where('locacion_id', $this->filterLocalizacao);
        }
        $peps = $pepsQuery->orderBy('nombre')->get();

        // Locaciones para el filtro
        $locaciones = Localizacao::orderBy('nombre')->get(['id', 'nombre']);

        // Obtener todas las asignaciones del mes (con conteos via sub-select)
        $asignacoes = Atribuicao::with(['colaboradores', 'veiculos'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->get();

        // Datos especiales de estado (sin pep_id)
        $estadoLabels = [
            'baixa' => 'Baixas',
            'licenca' => 'Licenças',
            'ferias' => 'Férias',
            'consulta_medica' => 'Consultas Médicas',
            'formacao' => 'Formação',
            'reparacao' => 'Reparações',
        ];

        // Matrix: [pep_id][fecha_string] = ['col' => n, 'veh' => n, 'asig_ids' => [...]]
        $matrix = [];
        // Estado matrix: [estado][fecha_string] = count_colaboradores
        $estadoMatrix = [];
        // Totales por día: [fecha_string] = count_colaboradores total
        $totalesDia = [];

        foreach ($asignacoes as $asig) {
            $fechaStr = $asig->fecha->toDateString();
            $numCol = $asig->colaboradores->count();
            $numVeh = $asig->veiculos->count();

            // Agrupar en totalesDia
            if (! isset($totalesDia[$fechaStr])) {
                $totalesDia[$fechaStr] = 0;
            }
            $totalesDia[$fechaStr] += $numCol;

            if ($asig->pep_id) {
                if (! isset($matrix[$asig->pep_id][$fechaStr])) {
                    $matrix[$asig->pep_id][$fechaStr] = ['col' => 0, 'veh' => 0];
                }
                $matrix[$asig->pep_id][$fechaStr]['col'] += $numCol;
                $matrix[$asig->pep_id][$fechaStr]['veh'] += $numVeh;
            } elseif ($asig->estado && isset($estadoLabels[$asig->estado])) {
                if (! isset($estadoMatrix[$asig->estado][$fechaStr])) {
                    $estadoMatrix[$asig->estado][$fechaStr] = 0;
                }
                $estadoMatrix[$asig->estado][$fechaStr] += $numCol;
            }
        }

        // ¿Qué estados tienen al menos un valor en el mes?
        $estadosActivos = array_keys(array_filter($estadoLabels, fn ($_, $key) => ! empty($estadoMatrix[$key]), ARRAY_FILTER_USE_BOTH));

        // Totales por PEP (suma del mes)
        $totalesPep = [];
        foreach ($peps as $pep) {
            $totalesPep[$pep->id] = collect($matrix[$pep->id] ?? [])->sum('col');
        }

        return view('livewire.resumo-mensal', [
            'diasLaborais' => $diasLaborais,
            'peps' => $peps,
            'locacoes' => $locaciones,
            'matrix' => $matrix,
            'estadoMatrix' => $estadoMatrix,
            'estadoLabels' => $estadoLabels,
            'estadosActivos' => $estadosActivos,
            'totalesDia' => $totalesDia,
            'totalesPep' => $totalesPep,
            'nombreMes' => Carbon::createFromDate($this->ano, $this->mes, 1)
                ->locale('pt')
                ->isoFormat('MMMM YYYY'),
        ]);
    }
}
