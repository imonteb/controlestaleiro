<?php

namespace App\Observers;

use App\Enums\EpiEntregaEstado;
use App\Models\EpiEntrega;
use App\Services\EpiStockService;

class EpiEntregaObserver
{
    public function __construct(private EpiStockService $stock) {}

    public function created(EpiEntrega $epiEntrega): void
    {
        if ($epiEntrega->estado->contaComoSaida()) {
            $this->stock->applyDelta($epiEntrega->epi_item_id, -$epiEntrega->cantidad, $epiEntrega->talla);
        }
    }

    public function updated(EpiEntrega $epiEntrega): void
    {
        $oldEstado = EpiEntregaEstado::tryFrom($epiEntrega->getRawOriginal('estado'));
        $isOldActive = $oldEstado?->contaComoSaida() ?? false;
        $isNewActive = $epiEntrega->estado->contaComoSaida();

        if ($isOldActive && ! $isNewActive) {
            $this->stock->applyDelta($epiEntrega->epi_item_id, $epiEntrega->cantidad, $epiEntrega->talla);
        } elseif (! $isOldActive && $isNewActive) {
            $this->stock->applyDelta($epiEntrega->epi_item_id, -$epiEntrega->cantidad, $epiEntrega->talla);
        } elseif ($isOldActive && $isNewActive) {
            $diferencia = $epiEntrega->cantidad - $epiEntrega->getOriginal('cantidad');
            if ($diferencia !== 0) {
                $this->stock->applyDelta($epiEntrega->epi_item_id, -$diferencia, $epiEntrega->talla);
            }
        }
    }

    public function deleted(EpiEntrega $epiEntrega): void
    {
        if ($epiEntrega->estado->contaComoSaida()) {
            $this->stock->applyDelta($epiEntrega->epi_item_id, $epiEntrega->cantidad, $epiEntrega->talla);
        }
    }
}
