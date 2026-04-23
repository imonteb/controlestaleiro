<?php

namespace App\Livewire\PhoneDisplay;

use App\Livewire\Concerns\HasSeguranca;
use App\Models\EmergencyContact;
use App\Models\EmergencyProcedure;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class SosTab extends Component
{
    use HasSeguranca;

    #[Reactive]
    public ?int $colaboradorId = null;

    // HasSeguranca reads $this->activeColaboradorId — kept in sync via mount + key() in view
    public ?int $activeColaboradorId = null;

    public function mount(): void
    {
        $this->activeColaboradorId = $this->colaboradorId;
    }

    public function render(): mixed
    {
        return view('livewire.phone-display.sos-tab', [
            'contactosEmergencia' => EmergencyContact::where('activo', true)->orderBy('ordem')->orderBy('nome')->get(),
            'procedimentosEmergencia' => EmergencyProcedure::where('activo', true)->get()->sortBy('ordem')->values(),
            'activeColaboradorId' => $this->colaboradorId,
        ]);
    }
}
