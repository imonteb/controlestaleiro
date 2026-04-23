<?php

namespace App\Livewire\Epis;

use App\Models\AppNotification;
use App\Models\Colaborador;
use App\Models\EpiEntrega;
use App\Models\SignatureRequest;
use App\Services\SignatureService;
use App\Services\WebPushService;
use Livewire\Attributes\On;
use Livewire\Component;

class EntregaModal extends Component
{
    public bool $open = false;

    public array $entregaIds = [];

    public ?int $colaboradorId = null;

    /** 'agora' | 'remota' | 'nenhuma' */
    public string $modo = 'agora';

    public ?string $signatureToken = null;

    public ?string $assinaturaBase64 = null;

    public ?string $redirectUrl = null;

    #[On('abrir-modal-entrega')]
    public function abrir(array $entregaIds, int $colaboradorId, ?string $redirectUrl = null): void
    {
        $this->entregaIds = $entregaIds;
        $this->colaboradorId = $colaboradorId;
        $this->redirectUrl = $redirectUrl;
        $this->modo = 'agora';
        $this->signatureToken = null;
        $this->assinaturaBase64 = null;
        $this->open = true;
    }

    public function guardarAssinaturaPresencial(): void
    {
        $this->validate(['assinaturaBase64' => 'required|string']);

        $path = app(SignatureService::class)->store($this->assinaturaBase64);

        EpiEntrega::whereIn('id', $this->entregaIds)->update(['firma' => $path]);

        $this->fechar('EPI entregue e assinatura registada.');
    }

    public function selecionarRemoto(): void
    {
        $this->modo = 'remota';
        $this->gerarLinkRemoto();
    }

    public function gerarLinkRemoto(): void
    {
        $colaborador = Colaborador::findOrFail($this->colaboradorId);
        $entregas = EpiEntrega::with('epiItem')->whereIn('id', $this->entregaIds)->get();

        $token = bin2hex(random_bytes(16));

        SignatureRequest::create([
            'token' => $token,
            'signable_type' => EpiEntrega::class,
            'signable_id' => $this->entregaIds[0],
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
            'metadata' => [
                'type' => 'epi_delivery',
                'colaborador_nombre' => $colaborador->nombre,
                'colaborador_numero' => $colaborador->numero_colaborador,
                'items' => $entregas->map(fn ($e) => [
                    'nombre' => $e->epiItem->nombre,
                    'talla' => $e->talla,
                ])->toArray(),
                'batch_entrega_ids' => $this->entregaIds,
            ],
        ]);

        AppNotification::create([
            'titulo' => 'Assinatura Pendente: EPI',
            'mensagem' => 'Recebeu: '.$entregas->map(fn ($e) => $e->epiItem->nombre)->join(', ').'. Por favor, assine a receção no seu telemóvel.',
            'tipo' => 'assinatura',
            'url' => route('signature.show', $token),
            'activa' => true,
            'colaborador_id' => $this->colaboradorId,
        ]);

        $this->signatureToken = $token;

        app(WebPushService::class)->sendToColaborador($this->colaboradorId, [
            'title' => '✍️ Assinatura Pendente — EPI',
            'body' => 'Recebeu: '.$entregas->map(fn ($e) => $e->epiItem->nombre)->join(', ').'. Abra a app para assinar.',
            'tag' => "epi-assinatura-{$token}",
            'url' => route('signature.show', $token),
            'requireInteraction' => true,
        ]);
    }

    public function checkSignatureStatus(): void
    {
        if (! $this->signatureToken) {
            return;
        }

        $request = SignatureRequest::where('token', $this->signatureToken)->first();
        if ($request && $request->isCompleted()) {
            $this->fechar('EPI entregue e assinatura recebida.');
        }
    }

    public function confirmarSemAssinatura(): void
    {
        $this->fechar('EPI entregue e registado.');
    }

    private function fechar(string $mensagem = ''): void
    {
        $this->open = false;

        if ($mensagem) {
            session()->flash('success', $mensagem);
        }

        $this->redirect($this->redirectUrl ?? route('epis.entregas.index'));
    }

    public function render(): mixed
    {
        $colaborador = $this->colaboradorId ? Colaborador::find($this->colaboradorId) : null;
        $entregas = $this->entregaIds
            ? EpiEntrega::with('epiItem')->whereIn('id', $this->entregaIds)->get()
            : collect();

        return view('livewire.epis.entrega-modal', [
            'colaborador' => $colaborador,
            'entregas' => $entregas,
        ]);
    }
}
