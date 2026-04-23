<?php

namespace App\Imports;

use App\Models\Colaborador;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ColaboradoresImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    public function model(array $row): ?Colaborador
    {
        if (empty($row['numero_colaborador'])) {
            return null;
        }

        return new Colaborador([
            'numero_colaborador' => trim($row['numero_colaborador']),
            'nombre' => trim($row['nombre'] ?? ''),
            'apellido' => trim($row['apellido'] ?? ''),
            'telefono' => isset($row['telefono']) ? trim($row['telefono']) : null,
            'denominacion_cargo' => trim($row['denominacion_cargo'] ?? ''),
        ]);
    }

    public function uniqueBy(): string|array
    {
        return 'numero_colaborador';
    }
}
