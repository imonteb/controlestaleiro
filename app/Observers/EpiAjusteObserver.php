<?php

namespace App\Observers;

use App\Models\EpiAjuste;
use App\Services\EpiStockService;

class EpiAjusteObserver
{
    public function __construct(private EpiStockService $stock) {}

    public function created(EpiAjuste $epiAjuste): void
    {
        $this->stock->applyDelta($epiAjuste->epi_item_id, $epiAjuste->diferencia, $epiAjuste->talla);
    }

    public function updated(EpiAjuste $epiAjuste): void
    {
        $diferencia = $epiAjuste->diferencia - $epiAjuste->getOriginal('diferencia');
        if ($diferencia !== 0) {
            $this->stock->applyDelta($epiAjuste->epi_item_id, $diferencia, $epiAjuste->talla);
        }
    }

    public function deleted(EpiAjuste $epiAjuste): void
    {
        $this->stock->applyDelta($epiAjuste->epi_item_id, -$epiAjuste->diferencia, $epiAjuste->talla);
    }
}
