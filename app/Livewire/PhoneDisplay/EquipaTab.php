<?php

namespace App\Livewire\PhoneDisplay;

use App\Models\Atribuicao;
use App\Models\Colaborador;
use App\Models\DiaPublicado;
use App\Models\Pep;
use App\Models\VehicleDriverLog;
use App\Models\Veiculo;
use App\Services\WebPushService;
use App\Support\PepOrdem;
use Carbon\Carbon;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Reactive;
use Livewire\Component;

#[Poll(20000)]
class EquipaTab extends Component
{
    #[Reactive]
    public ?int $colaboradorId = null;

    public bool $verTodasEquipas = false;

    public bool $hideUltimaGuia = false;

    /** @var list<string> */
    public array $dismissedAlerts = [];

    public function openTab(string $tab): void
    {
        $this->dispatch('open-tab', tab: $tab);
    }

    public function requestPinChange(): void
    {
        $this->dispatch('show-pin-change');
    }

    public function dismissUltimaGuia(): void
    {
        $this->hideUltimaGuia = true;
    }

    public function dismissAlert(string $type, mixed $id = null): void
    {
        $key = $id ? "{$type}:{$id}" : $type;
        if (! in_array($key, $this->dismissedAlerts)) {
            $this->dismissedAlerts[] = $key;
        }
    }

    public function registrarCondutor(int $vehicleId): void
    {
        if (! $this->colaboradorId) {
            $this->dispatch('open-login');

            return;
        }

        $diaAtivo = DiaPublicado::ativoNaTv();
        if (! $diaAtivo) {
            return;
        }

        $atribuido = Atribuicao::where('fecha', $diaAtivo->fecha->toDateString())
            ->whereHas('colaboradores', fn ($q) => $q->where('colaborador_id', $this->colaboradorId))
            ->whereHas('veiculos', fn ($q) => $q->where('vehiculo_id', $vehicleId))
            ->exists();

        if (! $atribuido) {
            return;
        }

        // Verificar se o colaborador já está a conduzir outro veículo
        $jaAConduzir = VehicleDriverLog::ativoParaColaborador($this->colaboradorId, $vehicleId);
        if ($jaAConduzir) {
            $this->dispatch('condutor-ja-a-conduzir', [
                'matricula' => $jaAConduzir->veiculo?->matricula ?? '—',
                'desde' => $jaAConduzir->started_at->format('H:i'),
            ]);

            return;
        }

        // Verificar se há outro condutor ativo
        $logAtivo = VehicleDriverLog::ativoParaVeiculo($vehicleId);

        if ($logAtivo && $logAtivo->colaborador_id !== $this->colaboradorId) {
            // Conflito — avisar via modal Alpine
            $this->dispatch('condutor-conflito', [
                'vehicleId' => $vehicleId,
                'condutorNome' => $logAtivo->colaborador->nombre,
                'desde' => $logAtivo->started_at->format('H:i'),
            ]);

            return;
        }

        // Sem conflito — registar normalmente
        if ($logAtivo) {
            $logAtivo->update(['ended_at' => now()]);
        }

        VehicleDriverLog::create([
            'vehicle_id' => $vehicleId,
            'colaborador_id' => $this->colaboradorId,
            'started_at' => now(),
        ]);
    }

    public function assumirConducao(int $vehicleId): void
    {
        if (! $this->colaboradorId) {
            return;
        }

        // Verificar se o colaborador já está a conduzir outro veículo
        $jaAConduzir = VehicleDriverLog::ativoParaColaborador($this->colaboradorId, $vehicleId);
        if ($jaAConduzir) {
            $this->dispatch('condutor-ja-a-conduzir', [
                'matricula' => $jaAConduzir->veiculo?->matricula ?? '—',
                'desde' => $jaAConduzir->started_at->format('H:i'),
            ]);

            return;
        }

        $logAtivo = VehicleDriverLog::ativoParaVeiculo($vehicleId)?->load('colaborador');

        $nomeAnterior = $logAtivo?->colaborador->nombre ?? '—';
        $condutorAnteriorId = $logAtivo?->colaborador_id;

        // Fechar sessão anterior
        if ($logAtivo) {
            $logAtivo->update(['ended_at' => now()]);
        }

        // Abrir nova sessão com registo de takeover
        $novoCondutor = \App\Models\Colaborador::find($this->colaboradorId);

        VehicleDriverLog::create([
            'vehicle_id' => $vehicleId,
            'colaborador_id' => $this->colaboradorId,
            'started_at' => now(),
            'takeover_from_name' => $nomeAnterior,
            'takeover_at' => now(),
        ]);

        // Notificar o condutor anterior via push
        if ($condutorAnteriorId) {
            $veiculo = \App\Models\Veiculo::find($vehicleId);
            $matricula = $veiculo?->matricula ?? 'veículo';
            $novoNome = $novoCondutor?->nombre ?? 'Outro colaborador';

            app(WebPushService::class)->sendToColaborador($condutorAnteriorId, [
                'title' => '🚗 Veículo assumido',
                'body' => "{$novoNome} assumiu a condução do {$matricula} às ".now()->format('H:i').'. Se não foi autorizado, contacte o responsável.',
                'tag' => "condutor-takeover-{$vehicleId}",
                'url' => '/phone',
            ]);
        }
    }

    public function liberarVeiculo(int $vehicleId): void
    {
        if (! $this->colaboradorId) {
            return;
        }

        $diaAtivo = DiaPublicado::ativoNaTv();
        if (! $diaAtivo) {
            return;
        }

        $atribuido = Atribuicao::where('fecha', $diaAtivo->fecha->toDateString())
            ->whereHas('colaboradores', fn ($q) => $q->where('colaborador_id', $this->colaboradorId))
            ->whereHas('veiculos', fn ($q) => $q->where('vehiculo_id', $vehicleId))
            ->exists();

        if (! $atribuido) {
            session()->flash('error', 'Não tem permissão para libertar este veículo.');

            return;
        }

        VehicleDriverLog::where('vehicle_id', $vehicleId)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        session()->flash('success', 'Veículo liberto.');
    }

    public function render(): mixed
    {
        $diaAtivo = DiaPublicado::ativoNaTv();

        if (! $diaAtivo || ! $this->colaboradorId) {
            return view('livewire.phone-display.equipa-tab', [
                'peps' => collect(),
                'todasEquipas' => collect(),
                'pepData' => [],
                'estadoData' => [],
                'estaleiroCols' => collect(),
                'estaleiroVehs' => collect(),
                'agora' => now()->format('H:i'),
                'dataFormato' => '',
                'ultimaGuia' => null,
                'equipamentosPendentes' => collect(),
                'activeColaboradorId' => $this->colaboradorId,
            ]);
        }

        $data = $diaAtivo->fecha->toDateString();

        $allPeps = Pep::ativos()->with(['localizacao', 'tipoTrabalho'])->get();
        $pepEstaleiroId = $allPeps->first(fn ($p) => strtolower($p->nombre) === 'estaleiro')?->id;

        $peps = $allPeps
            ->filter(fn ($p) => strtolower($p->nombre) !== 'estaleiro')
            ->sortBy(function ($p) {
                $pos = array_search($p->id, PepOrdem::PREDEFINIDA);

                return $pos === false ? PHP_INT_MAX : $pos;
            })->values();

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
                    $pepData[$atrib->pep_id][$equipoTipo]['notas'] = $atrib->notas;
                }
                foreach ($atrib->veiculos as $veh) {
                    $equipoTipo = $veh->pivot->equipo_tipo ?? 'principal';
                    $pepData[$atrib->pep_id][$equipoTipo]['veiculos'][] = $veh;
                    $pepData[$atrib->pep_id][$equipoTipo]['notas'] = $atrib->notas;
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

        $equipamentosPendentes = collect();
        $minhaAtrib = Atribuicao::where('fecha', $data)
            ->whereHas('colaboradores', fn ($q) => $q->where('colaborador_id', $this->colaboradorId))
            ->first();

        if ($minhaAtrib && $minhaAtrib->veiculos->count() > 0) {
            $minhaAtrib->load(['veiculos.extintores', 'veiculos.kits']);
            $dataLimite = now()->subDays(30);

            foreach ($minhaAtrib->veiculos as $v) {
                $extintores = $v->extintores->filter(fn ($e) => ! $e->data_verificacao || $e->data_verificacao < $dataLimite);
                $kits = $v->kits->filter(fn ($k) => ! $k->data_verificacao || $k->data_verificacao < $dataLimite);

                foreach ($extintores as $e) {
                    $equipamentosPendentes->push(['tipo' => 'Extintor', 'nome' => $e->numero_serie ?? 'Extintor s/n', 'veiculo' => $v->matricula]);
                }
                foreach ($kits as $k) {
                    $equipamentosPendentes->push(['tipo' => 'Kit 1ºs Socorros', 'nome' => 'Kit Veículo', 'veiculo' => $v->matricula]);
                }
            }
        }

        $estaleiroCols = Colaborador::ativos()
            ->where('visible_en_dashboard', true)
            ->whereNotIn('id', $excludedColIds->unique()->values()->all())
            ->orderBy('nombre')->orderBy('apellido')
            ->get();

        $estaleiroVehs = Veiculo::ativos()
            ->whereNotIn('id', $excludedVehIds->unique()->values()->all())
            ->orderBy('matricula')
            ->get();

        $todasEquipas = $pepsComDados;

        $myPepIds = collect();
        foreach ($pepData as $pepId => $dataGroups) {
            $allColsInPep = collect();
            foreach ($dataGroups as $group) {
                if (isset($group['jefes'])) {
                    $allColsInPep = $allColsInPep->merge(collect($group['jefes'])->pluck('id'));
                }
                if (isset($group['colaboradores'])) {
                    $allColsInPep = $allColsInPep->merge(collect($group['colaboradores'])->pluck('id'));
                }
            }
            if ($allColsInPep->contains($this->colaboradorId)) {
                $myPepIds->push($pepId);
            }
        }

        $pepsComDados = $pepsComDados->filter(fn ($p) => $myPepIds->contains($p->id))->values();

        $isAtEstaleiro = $estaleiroCols->pluck('id')->contains($this->colaboradorId);
        if (! $isAtEstaleiro) {
            $estaleiroCols = collect();
            $estaleiroVehs = collect();
        } else {
            $pepsComDados = collect();
        }

        $estadoData = [];

        $ultimaGuia = \App\Models\GuiaTransporte::where('requerente_id', $this->colaboradorId)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('livewire.phone-display.equipa-tab', [
            'diaAtivo' => $diaAtivo,
            'data' => $data,
            'peps' => $pepsComDados,
            'todasEquipas' => $todasEquipas,
            'pepData' => $pepData,
            'estadoData' => $estadoData,
            'estaleiroCols' => $estaleiroCols,
            'estaleiroVehs' => $estaleiroVehs,
            'agora' => now()->format('H:i'),
            'dataFormato' => Carbon::parse($data)->locale('pt')->translatedFormat('d F (l)'),
            'ultimaGuia' => $ultimaGuia,
            'equipamentosPendentes' => in_array('equipamento', $this->dismissedAlerts) ? collect() : $equipamentosPendentes,
            'activeColaboradorId' => $this->colaboradorId,
        ]);
    }
}
