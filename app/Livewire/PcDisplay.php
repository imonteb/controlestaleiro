<?php

namespace App\Livewire;

use App\Models\Atribuicao;
use App\Models\Colaborador;
use App\Models\DiaPublicado;
use App\Models\Pep;
use App\Models\Veiculo;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.tv')] // Reusing TV layout for clean full-screen look
#[Title('CME — Monitor de Equipas')]
#[Poll(60000)]
class PcDisplay extends Component
{
    public function render()
    {
        $diaAtivo = DiaPublicado::ativoNaTv();

        if (! $diaAtivo) {
            return view('livewire.pc-display', [
                'diaAtivo' => null,
                'peps' => collect(),
                'pepData' => [],
                'estadoData' => [],
                'estaleiroCols' => collect(),
                'estaleiroVehs' => collect(),
                'agora' => now()->format('H:i'),
                'dataFormato' => '',
            ]);
        }

        $data = $diaAtivo->fecha->toDateString();

        // PEPs (Sem Estaleiro)
        $allPeps = Pep::ativos()->with(['localizacao', 'tipoTrabalho'])->get();
        $pepEstaleiroId = $allPeps->first(fn ($p) => strtolower($p->nombre) === 'estaleiro')?->id;
        $peps = $allPeps->filter(fn ($p) => strtolower($p->nombre) !== 'estaleiro');

        // Ordenação padrão
        $peps = $peps->sortBy(fn ($p) => $p->nombre)->values();

        // Atribuições do dia
        $atribuicoes = Atribuicao::with(['colaboradores', 'veiculos'])
            ->where('fecha', $data)
            ->get();

        $pepData = [];
        $estadoData = [];
        // Normalização de estados (PT e ES)
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
                    'descripcion_evento' => $atrib->descripcion_evento,
                ];
            }
        }

        $pepsComDados = $peps->filter(fn ($p) => ! empty($pepData[$p->id]))->values();

        // Estaleiro (Disponíveis)
        $estaleiroCols = Colaborador::ativos()
            ->where('visible_en_dashboard', true)
            ->whereNotIn('id', $excludedColIds->unique()->values()->all())
            ->orderBy('nombre')->orderBy('apellido')
            ->get();

        $estaleiroVehs = Veiculo::ativos()
            ->whereNotIn('id', $excludedVehIds->unique()->values()->all())
            ->orderBy('matricula')
            ->get();

        return view('livewire.pc-display', [
            'diaAtivo' => $diaAtivo,
            'data' => $data,
            'peps' => $pepsComDados,
            'pepData' => $pepData,
            'estadoData' => $estadoData,
            'estaleiroCols' => $estaleiroCols,
            'estaleiroVehs' => $estaleiroVehs,
            'agora' => now()->format('H:i'),
            'dataFormato' => Carbon::parse($data)->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ]);
    }
}
