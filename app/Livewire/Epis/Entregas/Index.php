<?php

namespace App\Livewire\Epis\Entregas;

use App\Enums\EpiEntregaEstado;
use App\Models\Colaborador;
use App\Models\EpiEntrega;
use App\Models\EpiItem;
use App\Services\SignatureService;
use App\Traits\HasMobileSignature;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Entregas de EPI')]
class Index extends Component
{
    use HasMobileSignature;

    public string $search = '';

    public ?int $filtroEpi = null;

    public string $filtroEstado = '';

    // Devolução
    public ?int $devolvendoId = null;

    public string $dataDevolucao = '';

    public function marcarDevolvido(int $id): void
    {
        $this->devolvendoId = $id;
        $this->dataDevolucao = now()->format('Y-m-d');
    }

    public function confirmarDevolucao(): void
    {
        $this->validate(['dataDevolucao' => 'required|date']);

        /** @var EpiEntrega|null $entrega */
        $entrega = EpiEntrega::find($this->devolvendoId);
        if ($entrega && $entrega->estado === EpiEntregaEstado::Entregue) {
            $entrega->estado = EpiEntregaEstado::Devolvido;
            $entrega->fecha_devolucion = $this->dataDevolucao;

            // Firma automática del usuario logado (mismo que aparece en "Responsável pela entrega")
            /** @var \App\Models\User|null $user */
            $user = auth()->user();
            $rawSig = $user instanceof \App\Models\User ? $user->getRawOriginal('signature') : null;
            if ($rawSig) {
                // Si ya es path de fichero, usarlo directamente.
                // Si es base64, guardarlo como fichero via SignatureService.
                $sigService = app(SignatureService::class);
                $entrega->firma_devolucion = $sigService->isStoredPath($rawSig)
                    ? $rawSig
                    : $sigService->store($rawSig);
            }

            $entrega->save();
        }

        $this->devolvendoId = null;
        $this->dataDevolucao = '';
    }

    public function eliminar(int $id): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        /** @var EpiEntrega|null $entrega */
        $entrega = EpiEntrega::find($id);
        if ($entrega) {
            $entrega->delete();
        }
    }

    public function cancelarDevolucao(): void
    {
        $this->devolvendoId = null;
        $this->dataDevolucao = '';
    }

    // Assinatura de Devolução
    public ?int $firmandoDevolucaoEntregaId = null;

    public ?string $assinaturaDevolucaoBase64 = null;

    public function abrirModalAssinaturaDevolucao(int $entregaId): void
    {
        $this->firmandoDevolucaoEntregaId = $entregaId;
        $this->assinaturaDevolucaoBase64 = null;
        $this->dispatch('abrir-modal-firma-devolucion');
    }

    public function guardarAssinaturaDevolucao(): void
    {
        $this->validate([
            'firmandoDevolucaoEntregaId' => 'required|exists:epi_entregas,id',
            'assinaturaDevolucaoBase64' => 'required|string',
        ]);

        $path = app(SignatureService::class)->store($this->assinaturaDevolucaoBase64);

        EpiEntrega::where('id', $this->firmandoDevolucaoEntregaId)
            ->whereNotNull('fecha_devolucion')
            ->update(['firma_devolucion' => $path]);

        $this->reset(['firmandoDevolucaoEntregaId', 'assinaturaDevolucaoBase64']);
        $this->dispatch('cerrar-modal-firma-devolucion');
    }

    // Assinatura Retroativa (por sessão/colaborador)
    public ?int $assinandoColaboradorId = null;

    public ?string $assinandoColaboradorNome = null;

    public array $entregasPendentesAssinatura = [];

    public ?string $assinaturaRetroativaBase64 = null;

    public function solicitarAssinatura(int $entregaId): void
    {
        $entrega = EpiEntrega::with('colaborador')->find($entregaId);
        if (! $entrega || $entrega->firma) {
            return;
        }

        $this->dispatch('abrir-modal-entrega',
            entregaIds: [$entregaId],
            colaboradorId: $entrega->colaborador_id,
        );
    }

    public function abrirModalAssinatura(int $colaboradorId): void
    {
        $colaborador = Colaborador::find($colaboradorId);
        if (! $colaborador) {
            return;
        }

        $pendientes = EpiEntrega::where('colaborador_id', $colaboradorId)
            ->whereIn('estado', EpiEntregaEstado::valoresComSaida())
            ->with('epiItem')
            ->get();

        if ($pendientes->isEmpty()) {
            return;
        }

        $this->assinandoColaboradorId = $colaboradorId;
        $this->assinandoColaboradorNome = trim($colaborador->nombre.' '.$colaborador->apellido);
        $this->entregasPendentesAssinatura = $pendientes->map(fn ($e) => [
            'id' => $e->id,
            'nombre' => $e->epiItem->nombre ?? '—',
            'talla' => $e->talla,
        ])->toArray();
        $this->assinaturaRetroativaBase64 = null;

        $this->dispatch('abrir-modal-firma');
    }

    public function startMobileSignatureIndex()
    {
        $this->validate([
            'assinandoColaboradorId' => 'required|exists:colaboradores,id',
        ]);

        $colaborador = Colaborador::find($this->assinandoColaboradorId);

        // Preparar metadata para a página móvel
        $metadata = [
            'colaborador_nombre' => trim($colaborador->nombre.' '.$colaborador->apellido),
            'colaborador_numero' => $colaborador->numero_colaborador,
            'items' => $this->entregasPendentesAssinatura,
            'type' => 'epi_delivery',
        ];

        $this->generateSignatureRequest($colaborador, $metadata);
    }

    public function onSignatureCompleted($request)
    {
        $this->assinaturaRetroativaBase64 = $request->signature_data;
        $this->dispatch('signature-ready', signature: $request->signature_data);
    }

    public function guardarAssinaturaRetroativa(): void
    {
        $this->validate([
            'assinandoColaboradorId' => 'required|exists:colaboradores,id',
            'assinaturaRetroativaBase64' => 'required|string',
        ]);

        $path = app(SignatureService::class)->store($this->assinaturaRetroativaBase64);

        EpiEntrega::where('colaborador_id', $this->assinandoColaboradorId)
            ->whereIn('estado', EpiEntregaEstado::valoresComSaida())
            ->update(['firma' => $path]);

        $this->reset(['assinandoColaboradorId', 'assinandoColaboradorNome', 'entregasPendentesAssinatura', 'assinaturaRetroativaBase64']);
        $this->dispatch('cerrar-modal-firma');
        $this->dispatch('firma-guardada');
    }

    public function render()
    {
        $query = EpiEntrega::with(['epiItem', 'colaborador', 'entregadoPor'])->orderByDesc('fecha_entrega');

        if ($this->filtroEpi) {
            $query->where('epi_item_id', $this->filtroEpi);
        }

        if ($this->filtroEstado !== '') {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->whereHas('colaborador', fn ($cq) => $cq->where('nombre', 'like', $s)->orWhere('apellido', 'like', $s))
                    ->orWhereHas('epiItem', fn ($eq) => $eq->where('nombre', 'like', $s));
            });
        }

        $allEntregas = $query->get();

        // Agrupar por Colaborador
        $colaboradores = $allEntregas->groupBy('colaborador_id')->map(function ($entregasColab) {
            $colab = $entregasColab->first()->colaborador;

            // Agrupar por Mes y Año
            $meses = $entregasColab->groupBy(function ($e) {
                return $e->fecha_entrega->format('Y-m');
            })->map(function ($entregasMes, $key) {
                [$year, $month] = explode('-', $key);

                return [
                    'year' => $year,
                    'month' => $month,
                    'label' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
                    'items' => $entregasMes,
                ];
            })->sortKeysDesc();

            return [
                'info' => $colab,
                'meses' => $meses,
            ];
        });

        return view('livewire.epis.entregas.index', [
            'colaboradores' => $colaboradores,
            'epiItems' => EpiItem::ativos()->orderBy('nombre')->get(),
        ]);
    }
}
