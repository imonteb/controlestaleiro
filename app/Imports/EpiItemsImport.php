<?php

namespace App\Imports;

use App\Models\EpiItem;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class EpiItemsImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    public function model(array $row): ?EpiItem
    {
        $nombre = trim($row['nome'] ?? $row['nombre'] ?? '');
        if ($nombre === '') {
            return null;
        }

        $tipo = mb_strtolower(trim($row['tipo'] ?? 'epi'));
        if (! in_array($tipo, ['epi', 'saude', 'incendio', 'ferramenta'])) {
            $tipo = 'epi';
        }

        // Talla manual (Flat SKU model)
        $talla = trim($row['tamanho'] ?? $row['talla'] ?? '');

        // Parse risks only for EPI
        $riscos = null;
        if ($tipo === 'epi' && ! empty($row['riscos'])) {
            $parsed = array_map('trim', explode(',', $row['riscos']));
            $riscos = array_values(array_filter($parsed, fn ($r) => in_array($r, EpiItem::RISCOS_DISPONIVEIS)));
        }

        // CA number only for EPI
        $ca_numero = ($tipo === 'epi' && ! empty($row['ca'])) ? trim($row['ca']) : null;

        return new EpiItem([
            'nombre' => $nombre,
            'codigo' => ! empty($row['codigo']) ? trim($row['codigo']) : null,
            'talla' => $talla ?: null,
            'tipo' => $tipo,
            'descripcion' => ! empty($row['descricao']) ? trim($row['descricao']) : (! empty($row['descripcion']) ? trim($row['descripcion']) : null),
            'ca_numero' => $ca_numero,
            'requiere_talla' => false,
            'tallas_disponibles' => null,
            'riscos' => $riscos,
            'unidade' => ! empty($row['unidade']) ? trim($row['unidade']) : 'unidade',
            'stock_minimo' => (int) ($row['stock_minimo'] ?? 0),
        ]);
    }

    public function uniqueBy(): string|array
    {
        // En el modelo Flat SKU, el código es el identificador único real.
        // Si no hay código, el sistema de Upsert podría fallar o duplicar si el nombre es igual.
        return 'codigo';
    }
}
