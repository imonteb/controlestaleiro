<?php

namespace Database\Seeders;

use App\Models\Locacion;
use Illuminate\Database\Seeder;

class LocacionesSeeder extends Seeder
{
    public function run(): void
    {
        $locaciones = [
            // Concelho de Funchal
            'Funchal',
            'Monte',
            'Santo António',
            'Câmara de Lobos',
            'Estreito de Câmara de Lobos',

            // Concelho de Santa Cruz
            'Santa Cruz',
            'Caniço',
            'Machico',
            'Caniçal',
            'Santo da Serra',
            'Camacha',

            // Concelho de Santana
            'Santana',
            'São Jorge',
            'Faial',
            'Boaventura',

            // Concelho de São Vicente
            'São Vicente',
            'Seixal',
            'Ponta Delgada',

            // Concelho da Ribeira Brava
            'Ribeira Brava',
            'Ponta do Sol',
            'Arco da Calheta',

            // Concelho da Calheta
            'Calheta',
            'Prazeres',
            'Jardim do Mar',
            'Paul do Mar',

            // Ponta de São Lourenço / Norte
            'Porto Moniz',
            'Porto Santo',

            // Sede / Instalaciones especiales
            'Estaleiro Principal',
        ];

        foreach ($locaciones as $nombre) {
            Locacion::firstOrCreate(['nombre' => $nombre]);
        }

        $this->command->info('Locaciones de Madeira importadas: '.count($locaciones));
    }
}
