<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasLogin;
use App\Livewire\Concerns\HasModalSwitch;
use App\Livewire\Concerns\HasNotifications;
use App\Livewire\Concerns\HasScanner;
use App\Models\AppNotification;
use App\Models\DiaPublicado;
use App\Models\EpiEntrega;
use App\Models\SignatureRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.phone')]
#[Title('CME C016 — Equipas do Dia')]
class PhoneDisplay extends Component
{
    use HasLogin;
    use HasModalSwitch;
    use HasNotifications;
    use HasScanner;

    public string $activeTab = 'equipa';

    public function mount(): void
    {
        $this->activeColaboradorId = session('active_colaborador_id');

        if (! $this->activeColaboradorId) {
            $this->isLoggingIn = true;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    #[On('refresh-notifications')]
    public function refreshNotifications(): void
    {
        // Re-render automático al recibir el evento
    }

    #[On('open-tab')]
    public function onOpenTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    #[On('show-pin-change')]
    public function onShowPinChange(): void
    {
        $this->showPinChange = true;
    }

    #[On('open-scanner')]
    public function onOpenScanner(): void
    {
        $this->scanning = true;
    }

    public function render(): mixed
    {
        $diaAtivo = DiaPublicado::ativoNaTv();

        $notificacoesGlobais = collect();
        $assinaturasPendentes = collect();

        if ($this->activeColaboradorId) {
            $notificacoesGlobais = AppNotification::where('activa', true)
                ->whereNotIn('tipo', ['admin', 'assinatura']) // assinaturas já aparecem no header via $assinaturasPendentes
                ->where(function ($q) {
                    $q->whereNull('data_expiracao')
                        ->orWhere('data_expiracao', '>', now());
                })
                ->where(function ($q) {
                    $q->whereNull('colaborador_id')
                        ->orWhere('colaborador_id', $this->activeColaboradorId);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(fn ($n) => ! in_array("notificacao:{$n->id}", $this->dismissedAlerts));

            $assinaturasPendentes = SignatureRequest::where('status', 'pending')
                ->where('signable_type', EpiEntrega::class)
                ->whereIn('signable_id', EpiEntrega::where('colaborador_id', $this->activeColaboradorId)->pluck('id'))
                ->with('signable.epiItem')
                ->get()
                ->filter(fn ($s) => ! in_array("signature:{$s->id}", $this->dismissedAlerts));
        }

        return view('livewire.phone-display.index', [
            'diaAtivo' => $diaAtivo,
            'notificacoesGlobais' => $notificacoesGlobais,
            'assinaturasPendentes' => $assinaturasPendentes,
        ]);
    }
}
