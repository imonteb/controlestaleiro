<?php

namespace App\Imports;

use App\Models\Veiculo;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class VeiculosImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    public function model(array $row): ?Veiculo
    {
        if (empty($row['matricula'])) {
            return null;
        }

        return new Veiculo([
            'marca' => trim($row['marca'] ?? ''),
            'modelo' => trim($row['modelo'] ?? ''),
            'matricula' => strtoupper(trim($row['matricula'])),
        ]);
    }

    public function uniqueBy(): string|array
    {
        return 'matricula';
    }
}
