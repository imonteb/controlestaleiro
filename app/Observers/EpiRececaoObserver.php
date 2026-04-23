<?php

namespace App\Observers;

use App\Models\EpiRececao;
use App\Services\EpiStockService;

class EpiRececaoObserver
{
    public function __construct(private EpiStockService $stock) {}

    public function created(EpiRececao $epiRececao): void
    {
        $this->stock->applyDelta($epiRececao->epi_item_id, $epiRececao->cantidad, $epiRececao->talla);
    }

    public function updated(EpiRececao $epiRececao): void
    {
        $diferencia = $epiRececao->cantidad - $epiRececao->getOriginal('cantidad');
        if ($diferencia !== 0) {
            $this->stock->applyDelta($epiRececao->epi_item_id, $diferencia, $epiRececao->talla);
        }
    }

    public function deleted(EpiRececao $epiRececao): void
    {
        $this->stock->applyDelta($epiRececao->epi_item_id, -$epiRececao->cantidad, $epiRececao->talla);
    }
}
