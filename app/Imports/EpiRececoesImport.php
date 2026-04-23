<?php

namespace App\Imports;

use App\Models\EpiItem;
use App\Models\EpiRececao;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EpiRececoesImport implements SkipsOnError, ToModel, WithHeadingRow
{
    use SkipsErrors;

    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row): ?EpiRececao
    {
        // Resolve EPI by codigo or nombre
        $epiItem = null;
        if (! empty($row['codigo'])) {
            $epiItem = EpiItem::where('codigo', trim($row['codigo']))->first();
        }
        if (! $epiItem && ! empty($row['epi'])) {
            $epiItem = EpiItem::where('nombre', trim($row['epi']))->first();
        }

        if (! $epiItem) {
            return null;
        }

        $cantidad = (int) ($row['quantidade'] ?? $row['cantidad'] ?? 0);
        if ($cantidad < 1) {
            return null;
        }

        $fecha = null;
        if (! empty($row['data'])) {
            try {
                $fecha = \Carbon\Carbon::parse($row['data'])->format('Y-m-d');
            } catch (\Throwable) {
                $fecha = now()->format('Y-m-d');
            }
        } else {
            $fecha = now()->format('Y-m-d');
        }

        return new EpiRececao([
            'epi_item_id' => $epiItem->id,
            'cantidad' => $cantidad,
            'talla' => ! empty($row['tamanho']) ? trim($row['tamanho']) : (! empty($row['talla']) ? trim($row['talla']) : null),
            'fecha' => $fecha,
            'proveedor' => ! empty($row['fornecedor']) ? trim($row['fornecedor']) : null,
            'numero_factura' => ! empty($row['fatura']) ? trim($row['fatura']) : null,
            'observaciones' => ! empty($row['observacoes']) ? trim($row['observacoes']) : null,
            'registrado_por' => $this->userId,
        ]);
    }
}
