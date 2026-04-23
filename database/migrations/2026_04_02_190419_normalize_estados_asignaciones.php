<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Normaliza os valores de estado em espanhol para português.
 *
 * Mapeamento:
 *   baja       → baixa
 *   licencia   → licenca
 *   vacaciones → ferias
 *   formacion  → formacao
 *
 * Os valores já corretos (baixa, licenca, ferias, formacao, consulta_medica,
 * reparacao, estaleiro, pep) não são alterados.
 */
return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'baja' => 'baixa',
            'licencia' => 'licenca',
            'vacaciones' => 'ferias',
            'formacion' => 'formacao',
        ];

        foreach ($map as $old => $new) {
            DB::table('asignaciones')
                ->where('estado', $old)
                ->update(['estado' => $new]);
        }
    }

    public function down(): void
    {
        $map = [
            'baixa' => 'baja',
            'licenca' => 'licencia',
            'ferias' => 'vacaciones',
            'formacao' => 'formacion',
        ];

        foreach ($map as $old => $new) {
            DB::table('asignaciones')
                ->where('estado', $old)
                ->update(['estado' => $new]);
        }
    }
};
