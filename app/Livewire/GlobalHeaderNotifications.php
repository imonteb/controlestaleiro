<?php

namespace App\Livewire;

use App\Models\AppNotification;
use App\Models\EpiPedido;
use App\Models\GuiaTransporte;
use App\Models\IncidentReport;
use Livewire\Component;

class GlobalHeaderNotifications extends Component
{
    public $epiCount = 0;

    public $guiaCount = 0;

    public $incidentCount = 0;

    public $semCondutorCount = 0;

    public function render()
    {
        $this->epiCount = EpiPedido::where('estado', 'pendente')->count();
        $this->guiaCount = GuiaTransporte::pendentes()->count();

        // Count new individual incident reports + active SOS broadcasts
        $this->incidentCount = IncidentReport::where('estado', 'novo')->count() +
                              AppNotification::where('tipo', 'seguranca')
                                  ->where('activa', true)
                                  ->where(function ($q) {
                                      $q->whereNull('data_expiracao')
                                          ->orWhere('data_expiracao', '>', now());
                                  })->count();

        $this->semCondutorCount = AppNotification::where('tipo', 'sem_condutor')
            ->where('activa', true)
            ->where(function ($q) {
                $q->whereNull('data_expiracao')
                    ->orWhere('data_expiracao', '>', now());
            })->count();

        return view('livewire.global-header-notifications');
    }
}
