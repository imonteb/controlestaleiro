<?php

namespace Database\Seeders;

use App\Models\Locacion;
use App\Models\Pep;
use App\Models\TipoTrabajo;
use Illuminate\Database\Seeder;

class PepDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Locaciones base iniciales (se pueden agregar más luego)
        $locaciones = [
            'Funchal', 'Calheta', 'Santana', 'Câmara de Lobos', 'Machico', 'Ponta do Sol', 'Ribeira Brava', 'Santa Cruz', 'São Vicente', 'Estaleiro Principal',
        ];

        foreach ($locaciones as $loc) {
            Locacion::firstOrCreate(['nombre' => $loc]);
        }

        // 2. Tipos de trabajo base iniciales
        $tipos = [
            ['nombre' => 'BT', 'color' => '#39B54A'], // Verde
            ['nombre' => 'AT', 'color' => '#D32F2F'], // Rojo
            ['nombre' => 'Fibra', 'color' => '#0057B8'], // Azul
            ['nombre' => 'Manutenção', 'color' => '#FFC107'], // Amarillo
            ['nombre' => 'Geral', 'color' => '#333333'], // Gris
        ];

        foreach ($tipos as $tipo) {
            TipoTrabajo::firstOrCreate(['nombre' => $tipo['nombre']], ['color' => $tipo['color']]);
        }

        // 3. PEPs proporcionados
        // Al ser iniciales, asignaremos temporalmente la primera locación y tipo para tener data
        // El usuario luego podrá editar estas asignaciones desde la app
        $locacionDefault = Locacion::first()->id;
        $tipoDefault = TipoTrabajo::first()->id;

        $peps = [
            'P.016.047/002', 'P.016.048/002', 'P.016.048/003', 'P.016.048/005',
            'P.016.048/006', 'P.016.048/011', 'P.016.048/010', 'P.016.047/003',
            'P.016.047/004', 'P.9.211.008/001', 'P.016.041/001', 'P.016.048/009',
            'P.016.049/001', 'P.016.052/001', 'P.016.048/008', 'Estaleiro',
        ];

        foreach ($peps as $nombre_pep) {
            Pep::firstOrCreate(
                ['nombre' => $nombre_pep],
                ['locacion_id' => $locacionDefault, 'tipo_trabajo_id' => $tipoDefault]
            );
        }
    }
}
