<?php

namespace App\Livewire;

use App\Models\AppNotification;
use App\Models\CtConcelho;
use App\Models\CtDistrito;
use App\Models\GuiaItem;
use App\Models\GuiaTransporte;
use App\Models\LocalFrequente;
use App\Models\Material;
use App\Models\NominatimCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoGuias extends Component
{
    use WithPagination;

    public bool $isModalOpen = false;

    public string $modalMode = 'edit'; // 'edit' | 'view'

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public $guia_id;

    public string $filtroEstado = 'todas';

    // Form fields
    public string $tipo = 'normal';

    public string $local_carga_nome = 'ESTALEIRO CME';

    public string $local_carga_morada = '';

    public string $local_carga_localidade = 'CANICAL';

    public string $local_carga_cpostal = '';

    public $data_inicio;

    public $hora_inicio;

    public string $matricula = '';

    public string $destino_nome = '';

    public string $destino_morada = '';

    public string $destino_localidade = '';

    public string $destino_cpostal = '';

    public $data_fim;

    public $hora_fim;

    public string $estado = 'pendente';

    public string $numero_at = '';

    public string $motivo_recusa = '';

    public $requerente_id = null;

    public array $items = [];

    public array $itemSuggestions = [];

    public array $materiaisResults = [];

    // Location UI state
    public string $originTab = 'confirmado'; // 'escolher' | 'confirmado'

    public string $originModo = 'pesquisa';  // 'frequente' | 'pesquisa' | 'manual'

    public string $destinoTab = 'escolher';  // 'escolher' | 'confirmado'

    public string $destinoModo = 'pesquisa'; // 'frequente' | 'pesquisa' | 'manual'

    public string $originDD = '';

    public string $originCC = '';

    public string $destinoDD = '';

    public string $destinoCC = '';

    protected $rules = [
        'tipo' => 'required|in:normal,global',
        'local_carga_nome' => 'required|string|max:255',
        'data_inicio' => 'required|date',
        'hora_inicio' => 'required',
        'matricula' => 'required|string|max:20',
        'items.*.descricao' => 'required|string|max:255',
        'items.*.quantidade' => 'required|numeric|min:0.01',
        'items.*.unidade' => 'required|string|max:20',
    ];

    public function mount(): void
    {
        $this->data_inicio = now()->format('Y-m-d');
        $this->hora_inicio = now()->format('H:i');
        $this->data_fim = now()->format('Y-m-d');
        $this->hora_fim = now()->addHour()->format('H:i');
        $this->loadSuggestions();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function updatedOriginDD(): void
    {
        $this->originCC = '';
    }

    public function updatedDestinoDD(): void
    {
        $this->destinoCC = '';
    }

    public function loadSuggestions(): void
    {
        $this->itemSuggestions = GuiaItem::select('descricao')
            ->distinct()
            ->orderBy('descricao')
            ->limit(50)
            ->pluck('descricao')
            ->toArray();
    }

    // ── Location ──────────────────────────────────────────────

    public function alterarOrigem(): void
    {
        $this->originTab = 'escolher';
        $this->originModo = 'pesquisa';
    }

    public function alterarDestino(): void
    {
        $this->destinoTab = 'escolher';
        $this->destinoModo = 'pesquisa';
    }

    public function searchLocaisFrequentes(string $q): array
    {
        if (strlen($q) < 1) {
            return [];
        }

        return LocalFrequente::activos()
            ->where(function ($query) use ($q) {
                $query->where('nome', 'like', "%{$q}%")
                    ->orWhere('localidade', 'like', "%{$q}%");
            })
            ->orderBy('nome')
            ->limit(6)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'nome' => $l->nome,
                'localidade' => $l->localidade,
                'cp' => $l->codigoPostalCompleto(),
            ])
            ->toArray();
    }

    public function selecionarLocalCarga(int $id): void
    {
        $local = LocalFrequente::findOrFail($id);
        $this->local_carga_nome = $local->nome;
        $this->local_carga_morada = $local->morada ?? '';
        $this->local_carga_localidade = $local->localidade ?? '';
        $this->local_carga_cpostal = $local->codigoPostalCompleto();
        $this->originTab = 'confirmado';
    }

    public function selecionarLocalDestino(int $id): void
    {
        $local = LocalFrequente::findOrFail($id);
        $this->destino_nome = $local->nome;
        $this->destino_morada = $local->morada ?? '';
        $this->destino_localidade = $local->localidade ?? '';
        $this->destino_cpostal = $local->codigoPostalCompleto();
        $this->destinoTab = 'confirmado';
    }

    public function searchRuas(string $q): array
    {
        if (strlen($q) < 2 || ! $this->originDD || ! $this->originCC) {
            return [];
        }

        $cached = NominatimCache::searchLocal($q, $this->originDD, $this->originCC, 10);
        if (! empty($cached)) {
            return $cached;
        }

        $concelhoDesig = CtConcelho::where('dd', $this->originDD)
            ->where('cc', $this->originCC)
            ->value('desig') ?? '';

        return NominatimCache::searchNominatim($q, $this->originDD, $this->originCC, $concelhoDesig);
    }

    public function searchRuasDestino(string $q): array
    {
        if (strlen($q) < 2 || ! $this->destinoDD || ! $this->destinoCC) {
            return [];
        }

        $cached = NominatimCache::searchLocal($q, $this->destinoDD, $this->destinoCC, 10);
        if (! empty($cached)) {
            return $cached;
        }

        $concelhoDesig = CtConcelho::where('dd', $this->destinoDD)
            ->where('cc', $this->destinoCC)
            ->value('desig') ?? '';

        return NominatimCache::searchNominatim($q, $this->destinoDD, $this->destinoCC, $concelhoDesig);
    }

    public function selecionarRuaOrigem(string $road, string $localidade, string $postcode): void
    {
        $this->local_carga_nome = $road;
        $this->local_carga_morada = $road;
        $this->local_carga_localidade = $localidade;
        $this->local_carga_cpostal = $postcode;
        $this->originTab = 'confirmado';
    }

    public function selecionarRuaDestino(string $road, string $localidade, string $postcode): void
    {
        $this->destino_nome = $road;
        $this->destino_morada = $road;
        $this->destino_localidade = $localidade;
        $this->destino_cpostal = $postcode;
        $this->destinoTab = 'confirmado';
    }

    // ── Materials ─────────────────────────────────────────────

    public function searchMateriais(string $q, int $index): void
    {
        if (strlen($q) < 2) {
            unset($this->materiaisResults[$index]);

            return;
        }

        $words = array_filter(explode(' ', mb_strtolower(trim($q))));
        $query = Material::where('activo', true);

        foreach ($words as $word) {
            $query->where(function ($sub) use ($word) {
                $sub->where('nome', 'like', "%{$word}%")
                    ->orWhere('codigo', 'like', "%{$word}%");
            });
        }

        $this->materiaisResults[$index] = $query->orderBy('nome')
            ->limit(15)
            ->get()
            ->map(fn ($m) => [
                'codigo' => $m->codigo,
                'nome' => $m->nome,
                'unidade' => $m->unidade_padrao ?? 'UN',
            ])
            ->toArray();
    }

    public function selecionarMaterial(int $index, string $nome, string $unidade): void
    {
        $this->items[$index]['descricao'] = $nome;
        $this->items[$index]['unidade'] = $unidade;
        unset($this->materiaisResults[$index]);
    }

    // ── Items ─────────────────────────────────────────────────

    public function addItem(): void
    {
        $this->items[] = ['descricao' => '', 'quantidade' => 1, 'unidade' => 'UN'];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index], $this->materiaisResults[$index]);
        $this->items = array_values($this->items);
        $this->materiaisResults = array_values($this->materiaisResults);
    }

    // ── Modal ─────────────────────────────────────────────────

    public function openModal(): void
    {
        $this->resetValidation();
        $this->modalMode = 'edit';
        $this->originTab = 'confirmado';
        $this->destinoTab = 'escolher';
        if (empty($this->items)) {
            $this->addItem();
        }
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->reset([
            'guia_id', 'tipo', 'local_carga_nome', 'local_carga_morada',
            'local_carga_localidade', 'local_carga_cpostal',
            'matricula', 'destino_nome', 'destino_morada',
            'destino_localidade', 'destino_cpostal',
            'estado', 'numero_at', 'motivo_recusa', 'requerente_id', 'items',
            'originDD', 'originCC', 'destinoDD', 'destinoCC', 'materiaisResults',
        ]);
        $this->local_carga_nome = 'ESTALEIRO CME';
        $this->local_carga_localidade = 'CANICAL';
        $this->data_inicio = now()->format('Y-m-d');
        $this->hora_inicio = now()->format('H:i');
        $this->data_fim = now()->format('Y-m-d');
        $this->hora_fim = now()->addHour()->format('H:i');
        $this->originTab = 'confirmado';
        $this->originModo = 'pesquisa';
        $this->destinoTab = 'escolher';
        $this->destinoModo = 'pesquisa';
        $this->modalMode = 'edit';
        $this->isModalOpen = false;
    }

    public function switchToEdit(): void
    {
        $this->modalMode = 'edit';
        $this->resetValidation();
    }

    public function editarGuia(int $id): void
    {
        $guia = GuiaTransporte::with('items')->findOrFail($id);
        $this->guia_id = $guia->id;
        $this->tipo = $guia->tipo;
        $this->local_carga_nome = $guia->local_carga_nome ?? 'ESTALEIRO CME';
        $this->local_carga_morada = $guia->local_carga_morada ?? '';
        $this->local_carga_localidade = $guia->local_carga_localidade ?? '';
        $this->local_carga_cpostal = $guia->local_carga_cpostal ?? '';
        $this->data_inicio = $guia->data_inicio?->format('Y-m-d');
        $this->hora_inicio = $guia->hora_inicio;
        $this->matricula = $guia->matricula;
        $this->destino_nome = $guia->destino_nome ?? '';
        $this->destino_morada = $guia->destino_morada ?? '';
        $this->destino_localidade = $guia->destino_localidade ?? '';
        $this->destino_cpostal = $guia->destino_cpostal ?? '';
        $this->data_fim = $guia->data_fim?->format('Y-m-d');
        $this->hora_fim = $guia->hora_fim;
        $this->estado = $guia->estado;
        $this->numero_at = $guia->numero_at ?? '';
        $this->motivo_recusa = $guia->motivo_recusa ?? '';
        $this->requerente_id = $guia->requerente_id;
        $this->originTab = 'confirmado';
        $this->destinoTab = $this->destino_nome ? 'confirmado' : 'escolher';
        $this->originDD = '';
        $this->originCC = '';
        $this->destinoDD = '';
        $this->destinoCC = '';
        $this->materiaisResults = [];

        $this->items = $guia->items->map(fn ($i) => [
            'descricao' => $i->descricao,
            'quantidade' => $i->quantidade,
            'unidade' => $i->unidade,
        ])->values()->toArray();

        $this->modalMode = $guia->origem === 'colaborador' ? 'view' : 'edit';
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function emitirGuia(): void
    {
        $this->validate(['numero_at' => 'required|string|max:50']);

        $guia = GuiaTransporte::findOrFail($this->guia_id);
        $guia->update([
            'estado' => 'emitida',
            'numero_at' => $this->numero_at,
            'data_emissao' => now(),
            'processed_by_id' => Auth::id(),
        ]);

        if ($guia->requerente_id) {
            AppNotification::create([
                'titulo' => 'Guia de Transporte Pronta',
                'mensagem' => "A guia para {$guia->matricula} foi emitida. Nº AT: {$guia->numero_at}. Pode iniciar o transporte.",
                'activa' => true,
                'colaborador_id' => $guia->requerente_id,
            ]);
        }

        session()->flash('success', 'Guia emitida com sucesso. Colaborador notificado.');
        $this->closeModal();
    }

    public function recusarGuia(): void
    {
        $this->validate(['motivo_recusa' => 'required|string']);

        $guia = GuiaTransporte::findOrFail($this->guia_id);
        $guia->update([
            'estado' => 'recusada',
            'motivo_recusa' => $this->motivo_recusa,
            'processed_by_id' => Auth::id(),
            'data_emissao' => now(),
        ]);

        if ($guia->requerente_id) {
            AppNotification::create([
                'titulo' => 'Guia de Transporte Recusada',
                'mensagem' => "A guia para {$guia->matricula} foi recusada. Motivo: {$this->motivo_recusa}",
                'activa' => true,
                'colaborador_id' => $guia->requerente_id,
            ]);
        }

        session()->flash('success', 'Guia recusada e colaborador notificado.');
        $this->closeModal();
    }

    public function salvarGuia(): void
    {
        $this->validate();

        DB::transaction(function () {
            $emitida = ! empty($this->numero_at);

            $guia = GuiaTransporte::updateOrCreate(
                ['id' => $this->guia_id],
                [
                    'origem' => 'admin',
                    'tipo' => $this->tipo,
                    'local_carga_nome' => $this->local_carga_nome,
                    'local_carga_morada' => $this->local_carga_morada,
                    'local_carga_localidade' => $this->local_carga_localidade,
                    'local_carga_cpostal' => $this->local_carga_cpostal,
                    'data_inicio' => $this->data_inicio,
                    'hora_inicio' => $this->hora_inicio,
                    'matricula' => strtoupper($this->matricula),
                    'destino_nome' => $this->destino_nome,
                    'destino_morada' => $this->destino_morada,
                    'destino_localidade' => $this->destino_localidade,
                    'destino_cpostal' => $this->destino_cpostal,
                    'data_fim' => $this->data_fim,
                    'hora_fim' => $this->hora_fim,
                    'estado' => $emitida ? 'emitida' : 'pendente',
                    'numero_at' => $this->numero_at ?: null,
                    'motivo_recusa' => null,
                    'data_emissao' => $emitida ? now() : null,
                    'processed_by_id' => Auth::id(),
                    'user_id' => Auth::id(),
                    'requerente_id' => $this->requerente_id,
                ]
            );

            $guia->items()->delete();
            foreach ($this->items as $item) {
                $guia->items()->create($item);
            }

            if ($emitida && $guia->requerente_id) {
                AppNotification::create([
                    'titulo' => 'Guia de Transporte Pronta',
                    'mensagem' => "A guia para {$guia->matricula} foi emitida. Nº AT: {$guia->numero_at}. Pode iniciar o transporte.",
                    'activa' => true,
                    'colaborador_id' => $guia->requerente_id,
                ]);
            }
        });

        session()->flash('success', 'Guia de transporte guardada com sucesso.');
        $this->loadSuggestions();
        $this->closeModal();
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function apagarGuia(): void
    {
        GuiaTransporte::find($this->eliminandoId)?->delete();
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
        session()->flash('success', 'Guia apagada.');
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function render(): mixed
    {
        $query = GuiaTransporte::with('items', 'user', 'requerente')
            ->orderBy('created_at', 'desc');

        if ($this->filtroEstado !== 'todas') {
            $query->where('estado', $this->filtroEstado);
        }

        $guias = $query->paginate(10);

        $counts = [
            'pendente' => GuiaTransporte::pendentes()->count(),
            'emitida' => GuiaTransporte::emitidas()->count(),
            'recusada' => GuiaTransporte::recusadas()->count(),
        ];

        $distritos = CtDistrito::orderBy('desig')->get();

        $concelhos = $this->originDD
            ? CtConcelho::where('dd', $this->originDD)->orderBy('desig')->get()
            : collect();

        $destinoConcelhos = $this->destinoDD
            ? CtConcelho::where('dd', $this->destinoDD)->orderBy('desig')->get()
            : collect();

        $locaisFrequentes = LocalFrequente::activos()
            ->orderBy('nome')
            ->limit(10)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'nome' => $l->nome,
                'localidade' => $l->localidade ?? '',
                'cp' => $l->codigoPostalCompleto(),
            ])
            ->toArray();

        return view('livewire.gestao-guias', [
            'guias' => $guias,
            'counts' => $counts,
            'distritos' => $distritos,
            'concelhos' => $concelhos,
            'destinoConcelhos' => $destinoConcelhos,
            'locaisFrequentes' => $locaisFrequentes,
        ])->layout('layouts.app');
    }
}
