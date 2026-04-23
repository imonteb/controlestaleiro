<?php

namespace App\Livewire;

use App\Models\Atribuicao;
use App\Models\AvisoTv;
use App\Models\Colaborador;
use App\Models\DiaPublicado;
use App\Models\Pep;
use App\Models\Veiculo;
use App\Support\PepOrdem;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.tv')]
#[Title('CME — Painel de Equipas')]
#[Poll(60000)] // refresh each 60 seconds
class TvDisplay extends Component
{
    public function render()
    {
        $diaAtivo = DiaPublicado::ativoNaTv();

        if (! $diaAtivo) {
            return view('livewire.tv-display', [
                'diaAtivo' => null,
                'peps' => collect(),
                'pepData' => [],
                'estadoData' => [],
                'estaleiroCols' => collect(),
                'estaleiroVehs' => collect(),
                'avisos' => AvisoTv::ativos(),
                'agora' => now()->format('H:i'),
                'dataFormato' => '',
            ]);
        }

        $data = $diaAtivo->fecha->toDateString();

        // PEPs (sem Estaleiro)
        $allPeps = Pep::ativos()->with(['localizacao', 'tipoTrabalho'])->get();
        $pepEstaleiro = $allPeps->first(fn ($p) => strtolower($p->nombre) === 'estaleiro');
        $pepEstaleiroId = $pepEstaleiro?->id;
        $peps = $allPeps->filter(fn ($p) => strtolower($p->nombre) !== 'estaleiro');

        // Ordenar
        $peps = $peps->sortBy(function ($p) {
            $pos = array_search($p->id, PepOrdem::PREDEFINIDA);

            return $pos === false ? PHP_INT_MAX : $pos;
        })->values();

        // Atribuições do dia
        $atribuicoes = Atribuicao::with(['colaboradores', 'veiculos'])
            ->where('fecha', $data)
            ->get();

        $pepData = [];
        $estadoData = [];
        $estadoValues = ['baixa', 'licenca', 'ferias', 'consulta_medica', 'formacao', 'reparacao'];

        $excludedColIds = collect();
        $excludedVehIds = collect();

        foreach ($atribuicoes as $atrib) {
            $esRealPep = $atrib->pep_id && $atrib->pep_id !== $pepEstaleiroId;

            $estadoNormalizado = $atrib->estado;

            $esEspecial = ! $atrib->pep_id && in_array($estadoNormalizado, $estadoValues);

            if ($esRealPep) {
                $excludedColIds = $excludedColIds->merge($atrib->colaboradores->pluck('id'));
                $excludedVehIds = $excludedVehIds->merge($atrib->veiculos->pluck('id'));

                foreach ($atrib->colaboradores as $col) {
                    $equipoTipo = $col->pivot->equipo_tipo ?? 'principal';
                    $esJefe = $col->pivot->es_jefe ? true : false;
                    $grupo = $esJefe ? 'jefes' : 'colaboradores';
                    $pepData[$atrib->pep_id][$equipoTipo][$grupo][] = $col;
                }
                foreach ($atrib->veiculos as $veh) {
                    $equipoTipo = $veh->pivot->equipo_tipo ?? 'principal';
                    $pepData[$atrib->pep_id][$equipoTipo]['veiculos'][] = $veh;
                }
            } elseif ($esEspecial) {
                $excludedColIds = $excludedColIds->merge($atrib->colaboradores->pluck('id'));
                $excludedVehIds = $excludedVehIds->merge($atrib->veiculos->pluck('id'));

                $estadoData[$estadoNormalizado][] = [
                    'colaboradores' => $atrib->colaboradores,
                    'veiculos' => $atrib->veiculos,
                    'fecha_entrada_taller' => $atrib->fecha_entrada_taller,
                    'nombre_taller' => $atrib->nombre_taller,
                ];
            }
        }

        $pepsComDados = $peps->filter(fn ($p) => ! empty($pepData[$p->id]))->values();

        // Estaleiro: ativos não atribuídos a PEP real nem em estado especial
        $estaleiroCols = Colaborador::ativos()
            ->where('visible_en_dashboard', true)
            ->whereNotIn('id', $excludedColIds->unique()->values()->all())
            ->orderBy('nombre')->orderBy('apellido')
            ->get();

        $estaleiroVehs = Veiculo::ativos()
            ->whereNotIn('id', $excludedVehIds->unique()->values()->all())
            ->orderBy('matricula')
            ->get();

        return view('livewire.tv-display', [
            'diaAtivo' => $diaAtivo,
            'data' => $data,
            'peps' => $pepsComDados,
            'pepData' => $pepData,
            'estadoData' => $estadoData,
            'estaleiroCols' => $estaleiroCols,
            'estaleiroVehs' => $estaleiroVehs,
            'avisos' => AvisoTv::ativos(),
            'agora' => now()->format('H:i'),
            'dataFormato' => Carbon::parse($data)->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ]);
    }
}
