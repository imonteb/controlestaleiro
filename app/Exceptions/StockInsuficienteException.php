<?php

namespace App\Exceptions;

use App\Models\EpiItem;
use RuntimeException;

class StockInsuficienteException extends RuntimeException
{
    public function __construct(
        public readonly EpiItem $item,
        public readonly int $cantidadSolicitada,
    ) {
        parent::__construct(
            "Stock insuficiente para «{$item->nombre}». "
            ."Disponível: {$item->stock_total} {$item->unidade}, "
            ."solicitado: {$cantidadSolicitada}."
        );
    }
}
