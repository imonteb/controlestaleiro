<?php

namespace App\Livewire;

use App\Models\GuiaItem;
use App\Models\GuiaTransporte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoGuias extends Component
{
    use WithPagination;

    public $isModalOpen = false;

    public $guia_id;

    public string $filtroEstado = 'todas';

    // Form fields
    public $tipo = 'normal';

    public $local_carga_nome = 'ESTALEIRO CME';

    public $local_carga_morada = '';

    public $local_carga_localidade = 'CANICAL';

    public $local_carga_cpostal = '';

    public $data_inicio;

    public $hora_inicio;

    public $matricula = '';

    public $destino_nome = '';

    public $destino_morada = '';

    public $destino_localidade = '';

    public $destino_cpostal = '';

    public $data_fim;

    public $hora_fim;

    public $estado = 'pendente';

    public $numero_at = '';

    public $motivo_recusa = '';

    public $requerente_id = null;

    public $items = [];

    public $itemSuggestions = [];

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

    public function loadSuggestions(): void
    {
        $this->itemSuggestions = GuiaItem::select('descricao')
            ->distinct()
            ->orderBy('descricao')
            ->limit(50)
            ->pluck('descricao')
            ->toArray();
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

        return view('livewire.gestao-guias', [
            'guias' => $guias,
            'counts' => $counts,
        ])->layout('layouts.app');
    }

    public function openModal(): void
    {
        $this->resetValidation();
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
        ]);
        $this->local_carga_nome = 'ESTALEIRO CME';
        $this->local_carga_localidade = 'CANICAL';
        $this->data_inicio = now()->format('Y-m-d');
        $this->hora_inicio = now()->format('H:i');
        $this->data_fim = now()->format('Y-m-d');
        $this->hora_fim = now()->addHour()->format('H:i');
        $this->isModalOpen = false;
    }

    public function addItem(): void
    {
        $this->items[] = ['descricao' => '', 'quantidade' => 1, 'unidade' => 'und'];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
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
                    'matricula' => $this->matricula,
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
                \App\Models\AppNotification::create([
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

    public function editarGuia(int $id): void
    {
        $guia = GuiaTransporte::with('items')->findOrFail($id);
        $this->guia_id = $guia->id;
        $this->tipo = $guia->tipo;
        $this->local_carga_nome = $guia->local_carga_nome;
        $this->local_carga_morada = $guia->local_carga_morada;
        $this->local_carga_localidade = $guia->local_carga_localidade;
        $this->local_carga_cpostal = $guia->local_carga_cpostal;
        $this->data_inicio = $guia->data_inicio?->format('Y-m-d');
        $this->hora_inicio = $guia->hora_inicio;
        $this->matricula = $guia->matricula;
        $this->destino_nome = $guia->destino_nome;
        $this->destino_morada = $guia->destino_morada;
        $this->destino_localidade = $guia->destino_localidade;
        $this->destino_cpostal = $guia->destino_cpostal;
        $this->data_fim = $guia->data_fim?->format('Y-m-d');
        $this->hora_fim = $guia->hora_fim;
        $this->estado = $guia->estado;
        $this->numero_at = $guia->numero_at ?? '';
        $this->motivo_recusa = $guia->motivo_recusa;
        $this->requerente_id = $guia->requerente_id;

        $this->items = $guia->items->map(fn ($i) => [
            'descricao' => $i->descricao,
            'quantidade' => $i->quantidade,
            'unidade' => $i->unidade,
        ])->values()->toArray();

        $this->openModal();
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
            \App\Models\AppNotification::create([
                'titulo' => 'Guia de Transporte Recusada',
                'mensagem' => "A guia para {$guia->matricula} foi recusada. Motivo: {$this->motivo_recusa}",
                'activa' => true,
                'colaborador_id' => $guia->requerente_id,
            ]);
        }

        session()->flash('success', 'Guia recusada e colaborador notificado.');
        $this->closeModal();
    }

    public function apagarGuia(int $id): void
    {
        GuiaTransporte::find($id)?->delete();
        session()->flash('success', 'Guia apagada.');
    }
}
