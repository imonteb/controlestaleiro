<?php

namespace App\Livewire\Concerns;

trait HasModalSwitch
{
    public $showConfirmModal = false;

    public $pendingTargetModal = null;

    public function requestOpenModal(string $target): void
    {
        if ($target === 'seguranca' && $this->isSegurancaOpen) {
            return;
        }
        if ($target === 'guias' && $this->isGuiasOpen) {
            return;
        }
        if ($target === 'epi' && $this->isRequestingEpi) {
            return;
        }

        $hasOpen = $this->isSegurancaOpen || $this->isGuiasOpen || $this->isRequestingEpi;
        if ($hasOpen) {
            $this->pendingTargetModal = $target;
            $this->showConfirmModal = true;
        } else {
            $this->executeOpenModal($target);
        }
    }

    public function executeOpenModal(string $target): void
    {
        $this->isSegurancaOpen = false;
        $this->isGuiasOpen = false;
        $this->isRequestingEpi = false;
        $this->showConfirmModal = false;
        $this->pendingTargetModal = null;

        if ($target === 'seguranca') {
            $this->isSegurancaOpen = true;
        } elseif ($target === 'guias') {
            $this->isGuiasOpen = true;
            $this->loadGuiaSuggestions();
        } elseif ($target === 'epi') {
            $this->isRequestingEpi = true;
        }
    }

    public function confirmModalSwitch(): void
    {
        if ($this->pendingTargetModal) {
            $this->executeOpenModal($this->pendingTargetModal);
        } else {
            $this->showConfirmModal = false;
        }
    }

    public function cancelModalSwitch(): void
    {
        $this->showConfirmModal = false;
        $this->pendingTargetModal = null;
    }
}
