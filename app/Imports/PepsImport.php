<?php

namespace App\Imports;

use App\Models\Localizacao;
use App\Models\Pep;
use App\Models\TipoTrabalho;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PepsImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (empty($row['nombre'])) {
                continue;
            }

            $localizacaoId = null;
            $tipoTrabalhoId = null;

            if (! empty($row['locacion'])) {
                $localizacao = Localizacao::firstOrCreate(['nombre' => trim($row['locacion'])]);
                $localizacaoId = $localizacao->id;
            }

            if (! empty($row['tipo_trabajo'])) {
                $tipoTrabalho = TipoTrabalho::firstOrCreate(
                    ['nombre' => trim($row['tipo_trabajo'])],
                    ['color' => '#6b7280']
                );
                $tipoTrabalhoId = $tipoTrabalho->id;
            }

            Pep::updateOrCreate(
                ['nombre' => trim($row['nombre'])],
                [
                    'locacion_id' => $localizacaoId,
                    'tipo_trabajo_id' => $tipoTrabalhoId,
                ]
            );

            $this->importedCount++;
        }
    }
}
