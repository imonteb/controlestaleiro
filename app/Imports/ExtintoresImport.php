<?php

namespace App\Imports;

use App\Models\Extintor;
use App\Models\Veiculo;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ExtintoresImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    public function model(array $row): ?Extintor
    {
        $num_serie = trim($row['num_serie'] ?? $row['sn'] ?? '');
        if ($num_serie === '') {
            return null;
        }

        $veiculo_id = null;
        if (! empty($row['matricula'])) {
            $veiculo = Veiculo::where('matricula', trim($row['matricula']))->first();
            $veiculo_id = $veiculo ? $veiculo->id : null;
        }

        // Parse dates if available
        $data_verificacao = null;
        if (! empty($row['data_verificacao'])) {
            try {
                $data_verificacao = Carbon::parse($row['data_verificacao']);
            } catch (\Exception $e) {
            }
        }
        $proxima_revisao = null;
        if (! empty($row['proxima_revisao'])) {
            try {
                $proxima_revisao = Carbon::parse($row['proxima_revisao']);
            } catch (\Exception $e) {
            }
        }

        return new Extintor([
            'num_serie' => $num_serie,
            'tipo_agente' => ! empty($row['tipo']) ? trim($row['tipo']) : (! empty($row['agente']) ? trim($row['agente']) : 'Pó Químico'),
            'tamanho' => ! empty($row['tamanho']) ? trim($row['tamanho']) : '6kg',
            'estado' => ! empty($row['estado']) ? trim($row['estado']) : 'Conforme',
            'data_verificacao' => $data_verificacao,
            'proxima_revisao' => $proxima_revisao,
            'veiculo_id' => $veiculo_id,
        ]);
    }

    public function uniqueBy(): string|array
    {
        return 'num_serie';
    }
}
