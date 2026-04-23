<?php

namespace App\Services;

use App\Enums\EpiPedidoEstado;
use App\Exceptions\StockInsuficienteException;
use App\Models\EpiItem;
use App\Models\EpiPedido;
use Illuminate\Support\Facades\DB;

class EpiStockService
{
    /**
     * Apply a stock delta to an EpiItem inside a locked transaction.
     * Positive delta = stock increases (reception, return).
     * Negative delta = stock decreases (delivery, loss).
     */
    public function applyDelta(int $epiItemId, int $delta, ?string $talla = null): void
    {
        DB::transaction(function () use ($epiItemId, $delta, $talla) {
            $item = EpiItem::lockForUpdate()->findOrFail($epiItemId);

            if ($delta < 0 && ($item->stock_total + $delta) < 0) {
                throw new StockInsuficienteException($item, abs($delta));
            }

            $item->stock_total += $delta;

            if ($item->requiere_talla && $talla) {
                $tallas = $item->stock_por_talla ?? [];

                if ($delta < 0 && (($tallas[$talla] ?? 0) + $delta) < 0) {
                    throw new StockInsuficienteException($item, abs($delta));
                }

                $tallas[$talla] = ($tallas[$talla] ?? 0) + $delta;
                $item->stock_por_talla = $tallas;
            }

            $stockAnterior = $item->stock_total - $delta;
            $item->saveQuietly();

            // Activar pedidos sem_stock si el stock pasa de 0 a positivo
            if ($delta > 0 && $stockAnterior <= 0 && $item->stock_total > 0) {
                EpiPedido::where('epi_item_id', $epiItemId)
                    ->where('estado', EpiPedidoEstado::SemStock)
                    ->update(['estado' => EpiPedidoEstado::Pendente]);
            }
        });
    }

    /**
     * Validate that the requested quantity is available in stock.
     * Throws a StockInsuficienteException when stock is insufficient.
     */
    public function validateStock(EpiItem $item, int $cantidad): void
    {
        if ($cantidad > $item->stock_total) {
            throw new StockInsuficienteException($item, $cantidad);
        }
    }
}
