<?php

namespace App\Livewire;

use App\Models\Atribuicao;
use App\Models\Colaborador;
use App\Models\DiaPublicado;
use App\Models\Localizacao;
use App\Models\Pep;
use App\Models\TipoTrabalho;
use App\Models\Veiculo;
use App\Support\PepOrdem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Gestão de Equipas')]
class GestaoEquipas extends Component
{
    public $data;

    public string $sortBy = 'predefinida';

    public string $sortDir = 'asc';

    public string $filterLocalizacao = '';

    public string $filterTipo = '';

    public bool $semDados = false;

    public ?string $ultimaDataComDados = null;

    public string $searchEstaleiro = '';

    // Ferramenta para pesquisar data histórica no banner
    public bool $showPesquisarData = false;

    public string $pesquisarDataInput = '';

    public ?string $erroPesquisarData = null;

    // Eliminar dia atual
    public bool $confirmarEliminar = false;

    public ?string $erroEliminar = null;

    // Confirmação de dia anterior
    public ?DiaPublicado $diaPendenteConfirmacao = null;

    public bool $modalConfirmacaoOpen = false;

    public string $firmaConfirmacao = '';

    public string $erroConfirmacao = '';

    // Publicar dia em TV
    public bool $prontoParaTv = false;

    // Modal para capturar dados extra após soltar em zonas com metadados
    public bool $modalOpen = false;

    public ?int $modalAtribuicaoId = null;

    public string $modalTipo = ''; // consulta_medica | formacao | reparacao | notas

    public string $modalDataHora = '';

    public string $modalDescricao = '';

    public string $modalNomeOficina = '';

    public string $modalDataEntradaOficina = '';

    public string $modalNotas = '';

    public function mount()
    {
        // Se hoje for fim de semana, avançar para o próximo dia útil
        $hoje = Carbon::today();
        while ($hoje->isWeekend()) {
            $hoje->addDay();
        }
        $this->data = $hoje->toDateString();
        $this->verificarDadosData();
        $this->prontoParaTv = DiaPublicado::where('fecha', $this->data)->exists();
        $this->diaPendenteConfirmacao = DiaPublicado::pendenteConfirmacao();
    }

    /** Ativa ou desativa a publicação do dia atual no painel TV. */
    public function toggleProntoParaTv(): void
    {
        $dia = DiaPublicado::firstOrNew(['fecha' => $this->data]);

        if ($dia->exists) {
            $dia->delete();
            $this->prontoParaTv = false;
        } else {
            // Bloquear publicação de novo dia se há um dia anterior por confirmar
            if ($this->diaPendenteConfirmacao) {
                return;
            }
            $dia->save();
            $this->prontoParaTv = true;
        }
    }

    /** Devolve o próximo dia útil (segunda–sexta) após a data fornecida. */
    private function nextWorkingDay(string $date): string
    {
        $d = Carbon::parse($date)->addDay();
        while ($d->isWeekend()) {
            $d->addDay();
        }

        return $d->toDateString();
    }

    /** Avança para o próximo dia útil. */
    public function irDiaSeguinte(): void
    {
        $this->data = $this->nextWorkingDay($this->data);
        $this->updatedData();
    }

    /** Retrocede para o dia útil anterior. */
    public function irDiaAnterior(): void
    {
        $d = Carbon::parse($this->data)->subDay();
        while ($d->isWeekend()) {
            $d->subDay();
        }
        $this->data = $d->toDateString();
        $this->updatedData();
    }

    /** Executa-se automaticamente sempre que o utilizador altera a data. */
    public function updatedData(): void
    {
        $this->confirmarEliminar = false;
        $this->erroEliminar = null;
        $this->verificarDadosData();
        $this->prontoParaTv = DiaPublicado::where('fecha', $this->data)->exists();
    }

    /** Verifica se a data selecionada tem atribuições guardadas.
     *  Se não as tiver, procura o último dia anterior com dados e ativa o banner. */
    private function verificarDadosData(): void
    {
        $temDados = Atribuicao::where('fecha', $this->data)->exists();

        if ($temDados) {
            $this->semDados = false;
            $this->ultimaDataComDados = null;
        } else {
            $ultimaAtrib = Atribuicao::where('fecha', '<', $this->data)
                ->whereRaw('DAYOFWEEK(fecha) NOT IN (1, 7)') // excluir sábado e domingo
                ->orderBy('fecha', 'desc')
                ->first();
            $this->ultimaDataComDados = $ultimaAtrib?->fecha;
            $this->semDados = true; // sempre mostrar o banner se não houver dados
        }
    }

    public function toggleSortDir(): void
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    public function clearFilters(): void
    {
        $this->filterLocalizacao = '';
        $this->filterTipo = '';
        $this->sortBy = 'predefinida';
        $this->sortDir = 'asc';
    }

    public function render()
    {
        // 1. Obter Estaleiro fixo
        $pepEstaleiro = Pep::where('nombre', 'Estaleiro')->first();

        // 2. Construir query para os outros PEPs
        $pepsQuery = Pep::query()->with(['localizacao', 'tipoTrabalho'])
            ->where('nombre', '!=', 'Estaleiro');

        if ($this->filterLocalizacao !== '') {
            $pepsQuery->where('locacion_id', $this->filterLocalizacao);
        }
        if ($this->filterTipo !== '') {
            $pepsQuery->where('tipo_trabajo_id', $this->filterTipo);
        }

        $peps = $pepsQuery->get();

        // 3. Aplicar ordenação na coleção (mais flexível para a ordem 'predefinida')
        $desc = $this->sortDir === 'desc';
        $peps = match ($this->sortBy) {
            'localizacao' => $peps->sortBy(fn ($p) => $p->localizacao?->nombre ?? '', SORT_STRING, $desc),
            'tipo_trabalho' => $peps->sortBy(fn ($p) => $p->tipoTrabalho?->nombre ?? '', SORT_STRING, $desc),
            'predefinida' => $peps->sortBy(function ($p) {
                $pos = array_search($p->id, PepOrdem::PREDEFINIDA);

                return $pos === false ? PHP_INT_MAX : $pos;
            }, SORT_REGULAR, $desc),
            default => $peps->sortBy(fn ($p) => $p->nombre, SORT_STRING, $desc),
        };

        // Opções para os seletores de filtro
        $localizacoes = Localizacao::orderBy('nombre')->get(['id', 'nombre']);
        $tiposTrabalho = TipoTrabalho::orderBy('nombre')->get(['id', 'nombre', 'color']);

        // Carregar atribuições para a data atual
        $atribuicoes = Atribuicao::with(['colaboradores', 'veiculos'])
            ->where('fecha', $this->data)
            ->get();

        // Agrupar os IDs de recursos que já estão atribuídos
        $colaboradoresOcupadosIds = [];
        $veiculosOcupadosIds = [];

        $pepData = []; // Para mapear recursos por PEP
        $estadoData = []; // Para mapear recursos por Estado (baixa, licença, ...)

        $estadoValues = ['baixa', 'licenca', 'ferias', 'consulta_medica', 'formacao', 'reparacao'];

        foreach ($atribuicoes as $atrib) {
            // Os recursos atribuídos ao PEP Estaleiro continuam "livres" (estão na piscina)
            $esEstaleiro = $pepEstaleiro && $atrib->pep_id === $pepEstaleiro->id;

            foreach ($atrib->colaboradores as $col) {
                if (! $esEstaleiro) {
                    $colaboradoresOcupadosIds[] = $col->id;
                }

                $equipoTipo = $col->pivot->equipo_tipo ?? 'principal';
                $esJefe = $col->pivot->es_jefe ? true : false;
                $grupo = $esJefe ? 'jefes' : 'colaboradores';

                if ($atrib->pep_id && ! $esEstaleiro) {
                    $pepData[$atrib->pep_id][$equipoTipo][$grupo][] = $col;
                }
            }

            foreach ($atrib->veiculos as $veic) {
                if (! $esEstaleiro) {
                    $veiculosOcupadosIds[] = $veic->id;
                }

                $equipoTipo = $veic->pivot->equipo_tipo ?? 'principal';

                if ($atrib->pep_id && ! $esEstaleiro) {
                    $pepData[$atrib->pep_id][$equipoTipo]['veiculos'][] = $veic;
                }
            }

            if ($atrib->pep_id && ! $esEstaleiro) {
                // Guaranteed to save the general notes for this PEP assignment
                $pepData[$atrib->pep_id]['notas'] = $atrib->notas;
                // Save atribuicao ID for editing notes
                $pepData[$atrib->pep_id]['atribuicao_id'] = $atrib->id;
            }

            // Construir estadoData para zonas especiais (sem pep_id)
            if (! $atrib->pep_id && in_array($atrib->estado, $estadoValues)) {
                $estadoNormalizado = $atrib->estado;

                if (! isset($estadoData[$estadoNormalizado])) {
                    $estadoData[$estadoNormalizado] = [];
                }
                $estadoData[$estadoNormalizado][] = [
                    'atrib_id' => $atrib->id,
                    'colaboradores' => $atrib->colaboradores,
                    'veiculos' => $atrib->veiculos,
                    'fecha_hora_evento' => $atrib->fecha_hora_evento,
                    'descripcion_evento' => $atrib->descripcion_evento,
                    'nombre_taller' => $atrib->nombre_taller,
                    'fecha_entrada_taller' => $atrib->fecha_entrada_taller,
                ];
            }
        }

        // Obter recursos livres (não atribuídos hoje) — apenas os visíveis no dashboard
        $colaboradores_libres = Colaborador::whereNotIn('id', $colaboradoresOcupadosIds)
            ->where('visible_en_dashboard', true)
            ->where('activo', true)
            ->when($this->searchEstaleiro, function ($q) {
                $s = '%'.$this->searchEstaleiro.'%';
                $q->where(function ($q2) use ($s) {
                    $q2->where('nombre', 'like', $s)
                        ->orWhere('apellido', 'like', $s)
                        ->orWhere('numero_colaborador', 'like', $s)
                        ->orWhere('denominacion_cargo', 'like', $s);
                });
            })
            ->get();
        $veiculos_libres = Veiculo::whereNotIn('id', $veiculosOcupadosIds)
            ->where('activo', true)
            ->when($this->searchEstaleiro, function ($q) {
                $s = '%'.$this->searchEstaleiro.'%';
                $q->where(function ($q2) use ($s) {
                    $q2->where('matricula', 'like', $s)
                        ->orWhere('marca', 'like', $s)
                        ->orWhere('modelo', 'like', $s);
                });
            })
            ->get();

        // Próximo dia útil sugerido (para o banner)
        $nextWorkday = $this->ultimaDataComDados
            ? $this->nextWorkingDay($this->ultimaDataComDados)
            : $this->nextWorkingDay($this->data);

        // O dia atual tem dados? (para mostrar botão apagar)
        $diaAtualTemDados = Atribuicao::where('fecha', $this->data)->exists();

        return view('livewire.gestao-equipas', [
            'peps' => $peps,
            'pepEstaleiro' => $pepEstaleiro,
            'pepData' => $pepData,
            'estadoData' => $estadoData,
            'colaboradores_libres' => $colaboradores_libres,
            'veiculos_libres' => $veiculos_libres,
            'localizacoes' => $localizacoes,
            'tiposTrabalho' => $tiposTrabalho,
            'nextWorkday' => $nextWorkday,
            'diaAtualTemDados' => $diaAtualTemDados,
            'prontoParaTv' => $this->prontoParaTv,
            'diaPendenteConfirmacao' => $this->diaPendenteConfirmacao,
        ]);
    }

    public function copiarDeUltimaData(): void
    {
        if (! $this->ultimaDataComDados) {
            return;
        }

        // Bloquear importação se há um dia anterior por confirmar
        if ($this->diaPendenteConfirmacao) {
            return;
        }

        DB::transaction(function () {
            // Eliminar atribuições existentes para a data atual (se houver)
            $atribAtuais = Atribuicao::where('fecha', $this->data)->get();
            foreach ($atribAtuais as $atrib) {
                $atrib->colaboradores()->detach();
                $atrib->veiculos()->detach();
                $atrib->delete();
            }

            // Carregar atribuições do último dia com dados (incluir pivots)
            $atribOrigem = Atribuicao::with(['colaboradores', 'veiculos'])
                ->where('fecha', $this->ultimaDataComDados)
                ->get();

            foreach ($atribOrigem as $atrib) {
                $nova = Atribuicao::create([
                    'fecha' => $this->data,
                    'pep_id' => $atrib->pep_id,
                    'estado' => $atrib->estado,
                    'fecha_hora_evento' => $atrib->fecha_hora_evento,
                    'descripcion_evento' => $atrib->descripcion_evento,
                    'nombre_taller' => $atrib->nombre_taller,
                    'fecha_entrada_taller' => $atrib->fecha_entrada_taller,
                ]);

                foreach ($atrib->colaboradores as $col) {
                    $nova->colaboradores()->attach($col->id, [
                        'equipo_tipo' => $col->pivot->equipo_tipo ?? 'principal',
                        'es_jefe' => $col->pivot->es_jefe ?? false,
                    ]);
                }

                foreach ($atrib->veiculos as $veh) {
                    $nova->veiculos()->attach($veh->id, [
                        'equipo_tipo' => $veh->pivot->equipo_tipo ?? 'principal',
                    ]);
                }
            }
        });

        $this->semDados = false;
        $this->ultimaDataComDados = null;
    }

    public function descartarBannerCopia(): void
    {
        if ($this->diaPendenteConfirmacao) {
            return;
        }

        $this->semDados = false;
        $this->ultimaDataComDados = null;
        $this->showPesquisarData = false;
        $this->pesquisarDataInput = '';
        $this->erroPesquisarData = null;
    }

    /** Procura uma data específica na BD para utilizá-la como origem da cópia. */
    public function pesquisarDiaHistorico(): void
    {
        $this->erroPesquisarData = null;
        if (! $this->pesquisarDataInput) {
            return;
        }

        if (! Atribuicao::where('fecha', $this->pesquisarDataInput)->exists()) {
            $this->erroPesquisarData = 'Não há dados registados para essa data.';

            return;
        }

        $this->ultimaDataComDados = $this->pesquisarDataInput;
        $this->semDados = true;
        $this->showPesquisarData = false;
        $this->pesquisarDataInput = '';
    }

    /**
     * Apaga todas as atribuições do dia atual.
     * Só é permitido se houver 2 ou menos dias úteis posteriores com dados na BD.
     */
    public function eliminarDia(): void
    {
        $this->erroEliminar = null;

        $diasPosteriores = Atribuicao::where('fecha', '>', $this->data)
            ->distinct()
            ->pluck('fecha')
            ->filter(fn ($f) => ! Carbon::parse($f)->isWeekend())
            ->count();

        if ($diasPosteriores > 2) {
            $this->erroEliminar = 'Não é possível apagar: já existem mais de 2 dias de trabalho posteriores na base de dados.';
            $this->confirmarEliminar = false;

            return;
        }

        DB::transaction(function () {
            $atribs = Atribuicao::where('fecha', $this->data)->get();
            foreach ($atribs as $atrib) {
                $atrib->colaboradores()->detach();
                $atrib->veiculos()->detach();
                $atrib->delete();
            }
            // Eliminar também a publicação em TV se existir
            DiaPublicado::where('fecha', $this->data)->delete();
        });

        $this->confirmarEliminar = false;
        $this->prontoParaTv = false;
        $this->verificarDadosData();
    }

    public function abrirModalConfirmacao(): void
    {
        $this->firmaConfirmacao = '';
        $this->erroConfirmacao = '';
        $this->modalConfirmacaoOpen = true;
    }

    public function fecharModalConfirmacao(): void
    {
        $this->modalConfirmacaoOpen = false;
        $this->firmaConfirmacao = '';
        $this->erroConfirmacao = '';
    }

    public function irParaDiaPendente(): void
    {
        if ($this->diaPendenteConfirmacao) {
            $this->data = $this->diaPendenteConfirmacao->fecha->toDateString();
            $this->updatedData();
        }
    }

    public function confirmarDia(): void
    {
        if (! $this->diaPendenteConfirmacao) {
            return;
        }

        $user = Auth::user();

        if (empty($user->signature)) {
            $this->erroConfirmacao = 'Não tem assinatura guardada. Crie a sua assinatura primeiro.';

            return;
        }

        $this->diaPendenteConfirmacao->update([
            'confirmado_at' => now(),
            'confirmado_por' => $user->id,
            'firma_confirmacao' => $user->signature,
        ]);

        $this->modalConfirmacaoOpen = false;
        $this->erroConfirmacao = '';
        $this->diaPendenteConfirmacao = DiaPublicado::pendenteConfirmacao();
    }

    public function atribuirRecurso($recursoType, $recursoId, $destType, $destId, $equipoTipo = 'principal', $esJefe = false)
    {
        // Validar combinações não permitidas
        $soloColaboradores = ['baixa', 'licenca', 'ferias', 'consulta_medica', 'formacao'];
        if ($recursoType === 'veiculo' && in_array($destId, $soloColaboradores)) {
            return;
        }
        if ($recursoType === 'colaborador' && $destId === 'reparacao') {
            return;
        }

        $atribuicaoDestino = null;

        DB::transaction(function () use ($recursoType, $recursoId, $destType, $destId, $equipoTipo, $esJefe, &$atribuicaoDestino) {

            // 1. Remover o recurso de qualquer atribuição prévia nesta data
            $atribuicoesPrevias = Atribuicao::where('fecha', $this->data)->get();
            foreach ($atribuicoesPrevias as $atrib) {
                if ($recursoType === 'colaborador') {
                    $atrib->colaboradores()->detach($recursoId);
                } else {
                    $atrib->veiculos()->detach($recursoId);
                }

                if ($atrib->colaboradores()->count() == 0 && $atrib->veiculos()->count() == 0) {
                    $atrib->delete();
                }
            }

            // Se o destino for 'estaleiro', apenas queríamos desatribuí-lo.
            if ($destId === 'estaleiro' || $destType === 'estaleiro') {
                return;
            }

            // 2. Procurar ou criar a atribuição destino
            // Zonas com dados adicionais criam sempre uma atribuição nova (uma por recurso)
            $estadosComModal = ['consulta_medica', 'formacao', 'reparacao'];
            $necessitaModal = $destType === 'estado' && in_array($destId, $estadosComModal);

            if ($necessitaModal) {
                $atribuicaoDestino = Atribuicao::create([
                    'fecha' => $this->data,
                    'pep_id' => null,
                    'estado' => $destId,
                ]);
            } else {
                $atribQuery = Atribuicao::where('fecha', $this->data);

                if ($destType === 'pep') {
                    $atribQuery->where('pep_id', $destId)->where('estado', 'pep');
                } else {
                    $atribQuery->where('estado', $destId)->whereNull('pep_id');
                }

                $atribuicaoDestino = $atribQuery->first();

                if (! $atribuicaoDestino) {
                    $atribuicaoDestino = Atribuicao::create([
                        'fecha' => $this->data,
                        'pep_id' => $destType === 'pep' ? $destId : null,
                        'estado' => $destType === 'estado' ? $destId : ($destType === 'pep' ? 'pep' : 'estaleiro'),
                    ]);
                }
            }

            // 3. Adicionar o recurso à nova atribuição
            if ($recursoType === 'colaborador') {
                $atribuicaoDestino->colaboradores()->attach($recursoId, [
                    'equipo_tipo' => $equipoTipo,
                    'es_jefe' => $esJefe,
                ]);
            } else {
                $atribuicaoDestino->veiculos()->attach($recursoId, [
                    'equipo_tipo' => $equipoTipo,
                ]);
            }
        });

        // Abrir modal para capturar dados adicionais
        if ($atribuicaoDestino && in_array($destId, ['consulta_medica', 'formacao', 'reparacao'])) {
            $this->modalAtribuicaoId = $atribuicaoDestino->id;
            $this->modalTipo = $destId;
            $this->modalDataHora = '';
            $this->modalDescricao = '';
            $this->modalNomeOficina = '';
            $this->modalDataEntradaOficina = '';
            $this->modalOpen = true;
        }
    }

    public function guardarMetadadosEvento(): void
    {
        if (! $this->modalAtribuicaoId) {
            return;
        }

        $rules = [];
        if (in_array($this->modalTipo, ['consulta_medica', 'formacao'])) {
            $rules['modalDataHora'] = 'required|date';
        }
        if ($this->modalTipo === 'formacao') {
            $rules['modalDescricao'] = 'nullable|string|max:255';
        }
        if ($this->modalTipo === 'reparacao') {
            $rules['modalNomeOficina'] = 'nullable|string|max:150';
            $rules['modalDataEntradaOficina'] = 'nullable|date';
        }
        if ($rules) {
            $this->validate($rules, [
                'modalDataHora.required' => 'A data e hora são obrigatórias.',
                'modalDataHora.date' => 'Formato de data inválido.',
            ]);
        }

        /** @var Atribuicao $atrib */
        $atrib = Atribuicao::findOrFail($this->modalAtribuicaoId);

        if ($this->modalTipo === 'notas') {
            $atrib->notas = $this->modalNotas ?: null;
        }
        if (in_array($this->modalTipo, ['consulta_medica', 'formacao'])) {
            $atrib->fecha_hora_evento = $this->modalDataHora ?: null;
        }
        if ($this->modalTipo === 'formacao') {
            $atrib->descripcion_evento = $this->modalDescricao ?: null;
        }
        if ($this->modalTipo === 'reparacao') {
            $atrib->nombre_taller = $this->modalNomeOficina ?: null;
            $atrib->fecha_entrada_taller = $this->modalDataEntradaOficina ?: null;
        }
        $atrib->save();

        $this->fecharModalEvento();
    }

    public function fecharModalEvento(): void
    {
        $this->modalOpen = false;
        $this->modalAtribuicaoId = null;
        $this->modalTipo = '';
        $this->modalDataHora = '';
        $this->modalDescricao = '';
        $this->modalNomeOficina = '';
        $this->modalDataEntradaOficina = '';
        $this->modalNotas = '';
    }

    public function abrirModalNotas(int $pepId)
    {
        // Encontrar o criar la atribución de este PEP para hoy
        $atrib = Atribuicao::firstOrCreate(
            ['fecha' => $this->data, 'pep_id' => $pepId, 'estado' => 'pep']
        );

        $this->modalAtribuicaoId = $atrib->id;
        $this->modalTipo = 'notas';
        $this->modalNotas = $atrib->notas ?? '';
        $this->modalOpen = true;
    }
}
