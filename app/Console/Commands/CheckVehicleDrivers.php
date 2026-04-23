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

    protected $description = 'Verifica se veículos em PEPs ativos têm condutor registado às 9:00 AM';

    public function handle(): void
    {
        $today = now()->toDateString();
        $push = app(WebPushService::class);

        $atribuicoes = Atribuicao::with(['veiculos', 'colaboradores'])
            ->where('fecha', $today)
            ->whereNotNull('pep_id')
            ->get();

        foreach ($atribuicoes as $atrib) {
            foreach ($atrib->veiculos as $veh) {
                $temCondutor = VehicleDriverLog::where('vehicle_id', $veh->id)
                    ->whereNull('ended_at')
                    ->exists();

                if ($temCondutor) {
                    continue;
                }

                // Evitar duplicar notificações no mesmo dia
                $jaAlertado = AppNotification::where('tipo', 'sem_condutor')
                    ->where('mensagem', 'like', "%{$veh->matricula}%")
                    ->whereDate('created_at', $today)
                    ->exists();

                if ($jaAlertado) {
                    continue;
                }

                $this->warn("Veículo {$veh->matricula} sem condutor registado.");

                // Notificação no PC (header)
                AppNotification::create([
                    'tipo' => 'sem_condutor',
                    'titulo' => "Veículo sem condutor: {$veh->matricula}",
                    'mensagem' => "O veículo {$veh->matricula} está atribuído a uma equipa mas não tem condutor registado. Quem está a conduzir?",
                    'activa' => true,
                    'data_expiracao' => now()->endOfDay(),
                ]);

                // Push para todos os colaboradores da equipa
                $colaboradorIds = $atrib->colaboradores->pluck('id');
                foreach ($colaboradorIds as $colId) {
                    $push->sendToColaborador($colId, [
                        'title' => '🚗 Quem conduz o veículo?',
                        'body' => "O {$veh->matricula} ainda não tem condutor registado. Abra a app e marque «Sou eu que conduzo».",
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
