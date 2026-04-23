<?php

namespace App\Livewire\Concerns;

use App\Models\EpiItem;
use App\Models\EpiPedido;

trait HasEpiRequest
{
    public $isRequestingEpi = false;

    public $selectedEpiId = null;

    public $selectedTamanho = '';

    public $quantidadeEpi = 1;

    public $motivoPedido = '';

    /** @var array<int, array{epi_item_id: int, nombre: string, tamanho: string, quantidade: int}> */
    public array $epiCarrito = [];

    public function fecharModalPedido(): void
    {
        $this->reset(['isRequestingEpi', 'selectedEpiId', 'selectedTamanho', 'quantidadeEpi', 'motivoPedido', 'epiCarrito']);
    }

    private function resolveColaboradorId(): ?int
    {
        return $this->activeColaboradorId
            ?? (property_exists($this, 'colaboradorId') ? $this->colaboradorId : null);
    }

    public function adicionarAoCarrito(): void
    {
        if (! $this->resolveColaboradorId()) {
            return;
        }

        $this->validate([
            'selectedEpiId' => 'required',
            'quantidadeEpi' => 'required|integer|min:1',
        ]);

        $item = EpiItem::find($this->selectedEpiId);

        $this->epiCarrito[] = [
            'epi_item_id' => $this->selectedEpiId,
            'nombre' => $item->nombre,
            'tamanho' => $this->selectedTamanho,
            'quantidade' => $this->quantidadeEpi,
        ];

        $this->reset(['selectedEpiId', 'selectedTamanho']);
        $this->quantidadeEpi = 1;
        $this->dispatch('epi-item-adicionado');
    }

    public function removerDoCarrito(int $index): void
    {
        array_splice($this->epiCarrito, $index, 1);
    }

    public function enviarPedidoEpi(): void
    {
        $colaboradorId = $this->resolveColaboradorId();

        if (! $colaboradorId) {
            return;
        }

        if (empty($this->epiCarrito) && $this->selectedEpiId) {
            $this->adicionarAoCarrito();
        }

        if (empty($this->epiCarrito)) {
            return;
        }

        foreach ($this->epiCarrito as $item) {
            $epiItem = EpiItem::find($item['epi_item_id']);
            $estado = ($epiItem && $epiItem->stock_total > 0)
                ? \App\Enums\EpiPedidoEstado::Pendente
                : \App\Enums\EpiPedidoEstado::SemStock;

            EpiPedido::create([
                'colaborador_id' => $colaboradorId,
                'epi_item_id' => $item['epi_item_id'],
                'tamanho' => $item['tamanho'],
                'quantidade' => $item['quantidade'],
                'motivo_colaborador' => $this->motivoPedido,
                'estado' => $estado,
            ]);
        }

        $this->fecharModalPedido();
        $this->js("
            const b = document.getElementById('epi-success-banner');
            if (b) { b.style.display = 'flex'; setTimeout(() => b.style.display = 'none', 4000); }
            window.dispatchEvent(new CustomEvent('epi-pedido-enviado'));
        ");
    }
}
