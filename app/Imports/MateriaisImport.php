<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MaterialCategoria;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class MateriaisImport implements SkipsOnError, ToModel, WithHeadingRow, WithUpserts
{
    use SkipsErrors;

    /** @var array<string,int> */
    private array $categoriasCache = [];

    public function model(array $row): ?Material
    {
        $codigo = strtoupper(trim($row['codigo'] ?? ''));
        $nome = trim($row['nome'] ?? '');

        if ($codigo === '' || $nome === '') {
            return null;
        }

        $categoriaId = null;
        $nomeCategoria = trim($row['categoria'] ?? '');
        if ($nomeCategoria !== '') {
            if (! isset($this->categoriasCache[$nomeCategoria])) {
                $cat = MaterialCategoria::firstOrCreate(
                    ['nome' => $nomeCategoria],
                    ['ordem' => 99, 'activo' => true]
                );
                $this->categoriasCache[$nomeCategoria] = $cat->id;
            }
            $categoriaId = $this->categoriasCache[$nomeCategoria];
        }

        $unidade = strtoupper(trim($row['unidade'] ?? 'UND'));
        if ($unidade === '') {
            $unidade = 'UND';
        }

        return new Material([
            'codigo' => $codigo,
            'nome' => $nome,
            'descripcion' => trim($row['descricao'] ?? '') ?: null,
            'categoria_id' => $categoriaId,
            'unidade_padrao' => $unidade,
            'activo' => true,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'codigo';
    }
}
