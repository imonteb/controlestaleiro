<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Atribuicao;
use App\Models\VehicleDriverLog;
use App\Services\WebPushService;
use Illuminate\Console\Command;

class CheckVehicleDrivers extends Command
{
    protected $signature = 'app:check-vehicle-drivers';

    protected $description = 'Verifica se veículos atribuídos têm condutor registado (08:15 e 08:45)';

    public function handle(): void
    {
        $today = now()->toDateString();
        $push = app(WebPushService::class);

        $atribuicoes = Atribuicao::with(['veiculos', 'colaboradores'])
            ->where('fecha', $today)
            ->get();

        foreach ($atribuicoes as $atrib) {
            foreach ($atrib->veiculos as $veh) {
                $equipoTipo = $veh->pivot->equipo_tipo;

                $temCondutor = VehicleDriverLog::where('vehicle_id', $veh->id)
                    ->whereNull('ended_at')
                    ->exists();

                if ($temCondutor) {
                    continue;
                }

                // Máximo 2 notificações por veículo por dia (08:15 + 08:45)
                $alertasHoje = AppNotification::where('tipo', 'sem_condutor')
                    ->whereDate('created_at', $today)
                    ->where('mensagem', 'like', "%{$veh->matricula}%")
                    ->count();

                if ($alertasHoje >= 2) {
                    continue;
                }

                $this->warn("Veículo {$veh->matricula} ({$equipoTipo}) sem condutor registado.");

                // Jefe do mesmo equipo_tipo
                $jefe = $atrib->colaboradores
                    ->first(fn ($c) => $c->pivot->equipo_tipo === $equipoTipo && (int) $c->pivot->es_jefe === 1);

                AppNotification::create([
                    'colaborador_id' => $jefe?->id,
                    'tipo' => 'sem_condutor',
                    'titulo' => "Veículo sem condutor: {$veh->matricula}",
                    'mensagem' => "O veículo {$veh->matricula} está atribuído à equipa {$equipoTipo} mas não tem condutor registado.",
                    'activa' => true,
                    'data_expiracao' => now()->endOfDay(),
                ]);

                // Push só para o jefe da equipa
                if ($jefe) {
                    $push->sendToColaborador($jefe->id, [
                        'title' => '🚗 Veículo sem condutor',
                        'body' => "O {$veh->matricula} ({$equipoTipo}) ainda não tem condutor registado.",
                        'tag' => "sem-condutor-{$veh->id}",
                        'url' => '/phone',
                        'requireInteraction' => true,
                    ]);
                }
            }
        }

        $this->info('Verificação concluída.');
    }
}
