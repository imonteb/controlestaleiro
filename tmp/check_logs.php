<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FerramentaLog;

$logs = FerramentaLog::whereNotNull('num_registo_seq')->orderBy('num_registo_seq', 'asc')->get();

foreach ($logs as $l) {
    echo "ID: {$l->id} | Folha: {$l->folha_id} | Seq: {$l->num_registo_seq} | Reg: {$l->num_registo_verificacao}\n";
}
