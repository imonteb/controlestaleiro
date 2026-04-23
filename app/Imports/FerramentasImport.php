<?php

namespace App\Imports;

use App\Models\Ferramenta;
use App\Models\FerramentaLog;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class FerramentasImport implements OnEachRow, WithHeadingRow
{
    public int $importedCount = 0;

    public int $updatedCount = 0;

    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $errorRows = [];

    public function onRow(Row $row): void
    {
        $rowIndex = $row->getIndex();
        $data = $row->toArray();

        $designacao = trim($data['designacao'] ?? $data['nome'] ?? '');
        if ($designacao === '') {
            $this->skippedCount++;

            return;
        }

        try {
            $referencia = trim($data['referencia'] ?? $data['ref'] ?? '');

            if ($referencia === '') {
                $this->errorRows[] = "Linha {$rowIndex}: campo 'referencia' é obrigatório.";

                return;
            }

            $exists = Ferramenta::where('referencia', $referencia)->exists();

            $ferramenta = Ferramenta::updateOrCreate(
                ['referencia' => $referencia],
                [
                    'designacao' => $designacao,
                    'marca' => trim($data['marca'] ?? '') ?: null,
                    'modelo' => trim($data['modelo'] ?? '') ?: null,
                    'num_serie' => trim($data['num_serie'] ?? $data['sn'] ?? '') ?: null,
                    'familia' => trim($data['familia'] ?? '') ?: null,
                    'localizacao' => trim($data['localizacao'] ?? '') ?: null,
                    'periodicidade_meses' => isset($data['periodicidade']) && $data['periodicidade'] !== '' ? (int) $data['periodicidade'] : 12,
                    'tipo_documentacao' => trim($data['documentacao'] ?? '') ?: 'Manual',
                    'estado_operacional' => trim($data['estado'] ?? '') ?: 'Apto',
                    'activo' => true,
                ]
            );

            $exists ? $this->updatedCount++ : $this->importedCount++;

            // Log de verificação histórica (opcional)
            $dataVerifRaw = $data['data_verificacao'] ?? $data['ultima_verificacao'] ?? $data['data'] ?? null;

            if ($dataVerifRaw) {
                $dataVerif = is_numeric($dataVerifRaw)
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataVerifRaw)
                    : Carbon::parse($dataVerifRaw);

                $dataVerifFormatted = Carbon::instance($dataVerif)->format('Y-m-d');

                $alreadyLogged = FerramentaLog::where('ferramenta_id', $ferramenta->id)
                    ->where('data_verificacao', $dataVerifFormatted)
                    ->exists();

                if (! $alreadyLogged) {
                    $proxima = Carbon::instance($dataVerif)->addMonths($ferramenta->periodicidade_meses);

                    // Registo histórico: não ocupa folha de verificação nem sequencial
                    FerramentaLog::create([
                        'ferramenta_id' => $ferramenta->id,
                        'data_verificacao' => $dataVerifFormatted,
                        'proxima_verificacao' => $proxima,
                        'manutencao_tipo' => 'Importação',
                        'conclusao' => 'Registo histórico importado via Excel.',
                        'tipo_movimento' => 'importacao',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            $this->errorRows[] = "Linha {$rowIndex}: ".$e->getMessage();
        }
    }
}
