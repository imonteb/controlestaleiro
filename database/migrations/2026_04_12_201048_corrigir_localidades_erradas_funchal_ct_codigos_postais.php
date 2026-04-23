<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige entradas incorrectas no concelho do Funchal (dd=31, cc=03)
 * provenientes do ficheiro CTT oficial com erros de classificação:
 *
 * - Pico do Areeiro: cume montanhoso entre Funchal/Santana/Câmara de Lobos, não é localidade
 * - Selvagem Grande / Selvagem Pequena: reserva natural no Atlântico, classificada erroneamente como Funchal
 * - Vale Paraíso: pertence a Camacha, Santa Cruz (cc=08), onde já existe correctamente
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('ct_codigos_postais')
            ->where('dd', '31')
            ->where('cc', '03')
            ->whereIn('localidade', [
                'Pico do Areeiro',
                'Selvagem Grande',
                'Selvagem Pequena',
                'Vale Paraíso',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Reinsere os registos removidos (para rollback)
        DB::table('ct_codigos_postais')->insert([
            ['dd' => '31', 'cc' => '03', 'localidade' => 'Pico do Areeiro', 'cp4' => '9050', 'cp3' => '553'],
            ['dd' => '31', 'cc' => '03', 'localidade' => 'Selvagem Grande',  'cp4' => '9000', 'cp3' => '900'],
            ['dd' => '31', 'cc' => '03', 'localidade' => 'Selvagem Pequena', 'cp4' => '9000', 'cp3' => '900'],
            ['dd' => '31', 'cc' => '03', 'localidade' => 'Vale Paraíso',     'cp4' => '9060', 'cp3' => '432'],
        ]);
    }
};
