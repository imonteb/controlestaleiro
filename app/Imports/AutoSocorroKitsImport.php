<?php

namespace App\Imports;

use App\Models\AutoSocorroKit;
use App\Models\Veiculo;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class AutoSocorroKitsImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    public function model(array $row): ?AutoSocorroKit
    {
        $designacao = trim($row['designacao'] ?? $row['nome'] ?? '');
        if ($designacao === '') {
            return null;
        }

        $veiculo_id = null;
        if (! empty($row['matricula'])) {
            $veiculo = Veiculo::where('matricula', trim($row['matricula']))->first();
            $veiculo_id = $veiculo ? $veiculo->id : null;
        }

        return new AutoSocorroKit([
            'designacao' => $designacao,
            'identificador_kit' => ! empty($row['identificador']) ? trim($row['identificador']) : (! empty($row['ref']) ? trim($row['ref']) : null),
            'veiculo_id' => $veiculo_id,
        ]);
    }

    public function uniqueBy(): string|array
    {
        return ['identificador_kit'];
    }
}
