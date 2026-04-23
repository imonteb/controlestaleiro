<?php

namespace App\Livewire\PhoneDisplay;

use App\Livewire\Concerns\HasEpiRequest;
use App\Models\EpiItem;
use App\Models\EpiPedido;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class EpiTab extends Component
{
    use HasEpiRequest;

    #[Reactive]
    public ?int $colaboradorId = null;

    // HasEpiRequest reads $this->activeColaboradorId — kept in sync via mount + key() in view
    public ?int $activeColaboradorId = null;

    public function mount(): void
    {
        $this->activeColaboradorId = $this->colaboradorId;
    }

    public function render(): mixed
    {
        $meusPedidos = $this->colaboradorId
            ? EpiPedido::with('epiItem')
                ->where('colaborador_id', $this->colaboradorId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        return view('livewire.phone-display.epi-tab', [
            'epiItems' => EpiItem::where('activo', true)->orderBy('nombre')->get(),
            'meusPedidos' => $meusPedidos,
            'activeColaboradorId' => $this->colaboradorId,
        ]);
    }
}
