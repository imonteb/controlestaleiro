<?php

namespace App\Livewire\Concerns;

use App\Models\AppNotification;

trait HasNotifications
{
    public $dismissedAlerts = [];

    public function handleNotificationClick(int $id): void
    {
        $notif = AppNotification::find($id);
        if (! $notif) {
            return;
        }

        if ($notif->tipo === 'seguranca') {
            $this->isSegurancaOpen = true;
            $this->activeSegurancaTab = 'reportar';
            $this->dismissNotification($id);
        }
    }

    public function dismissNotification(int $id): void
    {
        if (! $this->activeColaboradorId) {
            return;
        }

        $notif = AppNotification::find($id);
        if ($notif && ($notif->colaborador_id == $this->activeColaboradorId || is_null($notif->colaborador_id))) {
            if ($notif->colaborador_id) {
                $notif->update(['activa' => false]);
            } else {
                $this->dismissAlert('notificacao', $id);
            }
        }
    }

    public function dismissAlert(string $type, mixed $id = null): void
    {
        $key = $id ? "{$type}:{$id}" : $type;
        if (! in_array($key, $this->dismissedAlerts)) {
            $this->dismissedAlerts[] = $key;
        }
    }
}
