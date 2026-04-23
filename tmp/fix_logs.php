<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FerramentaLog;

$logs = FerramentaLog::whereNotNull('num_registo_seq')->orderBy('num_registo_seq', 'asc')->get();

foreach ($logs as $l) {
    if (str_starts_with($l->num_registo_verificacao, 'R-')) {
        $year = $l->data_verificacao ? $l->data_verificacao->year : now()->year;
        $correctReg = "R-{$l->folha_id}/{$year}";

        if ($l->num_registo_verificacao !== $correctReg) {
            echo "ID: {$l->id} | Old: {$l->num_registo_verificacao} | New: {$correctReg}\n";
            $l->update(['num_registo_verificacao' => $correctReg]);
        }
    }
}
echo "Cleanup complete.\n";
