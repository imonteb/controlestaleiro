<?php

namespace App\Console\Commands;

use App\Enums\EpiEntregaEstado;
use App\Models\EpiAjuste;
use App\Models\EpiEntrega;
use App\Models\EpiItem;
use App\Models\EpiRececao;
use Illuminate\Console\Command;

class RecalculateEpiStock extends Command
{
    protected $signature = 'epi:recalculate-stock';

    protected $description = 'Recalculate global and sized stock for all EPIs based on history.';

    public function handle()
    {
        $items = EpiItem::all();
        $this->info("Recalculating stock for {$items->count()} items...");

        foreach ($items as $item) {
            $recepciones = EpiRececao::where('epi_item_id', $item->id)->get();
            $entregas = EpiEntrega::where('epi_item_id', $item->id)
                ->whereIn('estado', EpiEntregaEstado::valoresComSaida())->get();
            $ajustes = EpiAjuste::where('epi_item_id', $item->id)->get();

            $globalStock = 0;
            $stockPorTalla = [];

            // Add Recepciones
            foreach ($recepciones as $r) {
                $globalStock += $r->cantidad;
                if ($item->requiere_talla && $r->talla) {
                    $stockPorTalla[$r->talla] = ($stockPorTalla[$r->talla] ?? 0) + $r->cantidad;
                }
            }

            // Subtract Entregas
            foreach ($entregas as $e) {
                $globalStock -= $e->cantidad;
                if ($item->requiere_talla && $e->talla) {
                    $stockPorTalla[$e->talla] = ($stockPorTalla[$e->talla] ?? 0) - $e->cantidad;
                }
            }

            // Apply Ajustes
            foreach ($ajustes as $a) {
                $globalStock += $a->diferencia;
                if ($item->requiere_talla && $a->talla) {
                    $stockPorTalla[$a->talla] = ($stockPorTalla[$a->talla] ?? 0) + $a->diferencia;
                }
            }

            $item->stock_total = $globalStock;
            $item->stock_por_talla = empty($stockPorTalla) ? null : $stockPorTalla;
            $item->saveQuietly();
            $this->line("Item {$item->id}: {$globalStock} total");
        }

        $this->info('Done!');
    }
}
