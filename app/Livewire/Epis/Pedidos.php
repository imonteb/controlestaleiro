<?php

namespace App\Livewire\Epis;

use App\Enums\EpiEntregaEstado;
use App\Enums\EpiPedidoEstado;
use App\Models\EpiEntrega;
use App\Models\EpiPedido;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pedidos de EPI')]
class Pedidos extends Component
{
    use WithPagination;

    public string $filtro = 'pendente';

    public string $search = '';

    public ?int $rejeitandoId = null;

    public string $motivoRejeicao = '';

    public ?int $entregandoId = null;

    /** @var list<int> IDs de todos os pedidos aprovados do mesmo colaborador a entregar em batch */
    public array $entregaBatch = [];

    public string $observacoesEntrega = '';

    protected $queryString = [
        'filtro' => ['except' => 'pendente'],
        'search' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFiltro(): void
    {
        $this->resetPage();
    }

    public function rejeitar(int $id): void
    {
        $this->rejeitandoId = $id;
        $this->motivoRejeicao = '';
    }

    public function confirmarRejeicao(): void
    {
        $this->validate(['motivoRejeicao' => 'required|string|max:500']);

        $pedido = EpiPedido::find($this->rejeitandoId);
        if ($pedido) {
            $pedido->update([
                'estado' => EpiPedidoEstado::Rejeitado,
                'motivo_admin' => $this->motivoRejeicao,
            ]);
            session()->flash('success', 'Pedido de '.$pedido->colaborador->nombre.' rejeitado.');

            app(WebPushService::class)->sendToColaborador($pedido->colaborador_id, [
                'title' => 'Pedido EPI Rejeitado',
                'body' => "O pedido de {$pedido->epiItem->nombre} foi rejeitado. Motivo: {$this->motivoRejeicao}",
                'tag' => "epi-pedido-{$pedido->id}",
                'url' => '/phone',
            ]);
        }

        $this->rejeitandoId = null;
    }

    public function aprovar(int $id): void
    {
        $pedido = EpiPedido::find($id);
        if ($pedido) {
            $pedido->update(['estado' => EpiPedidoEstado::Aprovado]);
            $this->filtro = 'aprovado';
            $this->resetPage();
            session()->flash('success', 'Pedido aprovado. Agora pode registar a entrega física.');

            app(WebPushService::class)->sendToColaborador($pedido->colaborador_id, [
                'title' => 'Pedido EPI Aprovado ✓',
                'body' => "O seu pedido de {$pedido->epiItem->nombre} foi aprovado. Pode levantar no armazém.",
                'tag' => "epi-pedido-{$pedido->id}",
                'url' => '/phone',
            ]);
        }
    }

    public function prepararEntrega(int $id): void
    {
        $pedido = EpiPedido::find($id);
        if (! $pedido) {
            return;
        }

        // Agrupar todos os pedidos aprovados do mesmo colaborador num único batch
        $batch = EpiPedido::where('colaborador_id', $pedido->colaborador_id)
            ->where('estado', EpiPedidoEstado::Aprovado)
            ->pluck('id')
            ->toArray();

        $this->entregandoId = $id;
        $this->entregaBatch = $batch;
        $this->observacoesEntrega = '';
    }

    public function cancelarEntrega(): void
    {
        $this->entregandoId = null;
        $this->entregaBatch = [];
        $this->observacoesEntrega = '';
    }

    public function confirmarEntrega(): void
    {
        $pedidos = EpiPedido::with(['colaborador', 'epiItem'])
            ->whereIn('id', $this->entregaBatch)
            ->get();

        if ($pedidos->isEmpty()) {
            return;
        }

        $colaborador = $pedidos->first()->colaborador;
        $entregaIds = [];

        foreach ($pedidos as $pedido) {
            $entrega = EpiEntrega::create([
                'epi_item_id' => $pedido->epi_item_id,
                'colaborador_id' => $pedido->colaborador_id,
                'cantidad' => $pedido->quantidade,
                'talla' => $pedido->tamanho,
                'fecha_entrega' => now(),
                'estado' => EpiEntregaEstado::Entregue,
                'entregado_por' => Auth::id(),
                'observaciones' => 'Pedido via Mobile.'.($pedido->motivo_colaborador ? ' '.$pedido->motivo_colaborador : '').($this->observacoesEntrega ? ' | Obs: '.$this->observacoesEntrega : ''),
            ]);
            $entregaIds[] = $entrega->id;
        }

        EpiPedido::whereIn('id', $this->entregaBatch)
            ->update(['estado' => EpiPedidoEstado::Entregue->value]);

        $this->entregandoId = null;
        $this->entregaBatch = [];
        $this->observacoesEntrega = '';

        $this->dispatch('abrir-modal-entrega',
            entregaIds: $entregaIds,
            colaboradorId: $colaborador->id,
        );
    }

    public function render()
    {
        $query = EpiPedido::with(['colaborador', 'epiItem'])->orderBy('created_at', 'desc');

        if ($this->filtro !== 'todos') {
            $query->where('estado', $this->filtro);
        }

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->whereHas('colaborador', function ($sq) use ($s) {
                    $sq->where('nombre', 'like', $s)->orWhere('numero_colaborador', 'like', $s);
                })->orWhereHas('epiItem', function ($sq) use ($s) {
                    $sq->where('nombre', 'like', $s);
                });
            });
        }

        $pedidosBatch = ! empty($this->entregaBatch)
            ? EpiPedido::with(['colaborador', 'epiItem'])->whereIn('id', $this->entregaBatch)->get()
            : collect();

        return view('livewire.epis.pedidos', [
            'pedidos' => $query->paginate(15),
            'pedidosBatch' => $pedidosBatch,
        ]);
    }
}
