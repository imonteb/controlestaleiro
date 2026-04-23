<?php

namespace Database\Seeders;

use App\Models\MaterialCategoria;
use Illuminate\Database\Seeder;

class MaterialCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Eléctrico', 'ordem' => 1],
            ['nome' => 'Civil', 'ordem' => 2],
            ['nome' => 'Ferramentas', 'ordem' => 3],
            ['nome' => 'Consumíveis', 'ordem' => 4],
            ['nome' => 'Outro', 'ordem' => 5],
        ];

        foreach ($categorias as $categoria) {
            MaterialCategoria::firstOrCreate(
                ['nome' => $categoria['nome']],
                ['ordem' => $categoria['ordem'], 'activo' => true]
            );
        }
    }
}
