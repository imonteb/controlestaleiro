<?php

namespace App\Http\Controllers;

use App\Models\FerramentaLog;

class FerramentaLogPrintController extends Controller
{
    public function __invoke($folhaId)
    {
        if ($folhaId === 'all') {
            $logs = FerramentaLog::with('ferramenta')
                ->where('tipo_movimento', '!=', 'importacao')
                ->orderBy('num_registo_seq', 'asc')
                ->get();

            $maxFolha = FerramentaLog::where('tipo_movimento', '!=', 'importacao')->max('folha_id') ?: 1;

            return view('ferramentas.log-print-all', [
                'logs' => $logs,
                'maxFolha' => $maxFolha,
            ]);
        }

        $logs = FerramentaLog::with('ferramenta')
            ->where('tipo_movimento', '!=', 'importacao')
            ->where('folha_id', $folhaId)
            ->orderBy('num_registo_seq', 'asc')
            ->get();

        return view('ferramentas.log-print', [
            'folha_id' => $folhaId,
            'ano_verificacao' => now()->year,
            'logs' => $logs,
        ]);
    }
}
