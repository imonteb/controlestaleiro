<?php

namespace App\Livewire\Concerns;

use App\Models\IncidentReport;
use Livewire\WithFileUploads;

trait HasSeguranca
{
    use WithFileUploads;

    public $isSegurancaOpen = false;

    public $activeSegurancaTab = 'procedimentos';

    public $incidenteTipo = '';

    public $incidenteLocalizacao = '';

    public $incidenteDescricao = '';

    public $incidenteFoto = null;

    public function fecharSeguranca(): void
    {
        $this->isSegurancaOpen = false;
        $this->reset(['incidenteTipo', 'incidenteLocalizacao', 'incidenteDescricao', 'incidenteFoto']);
    }

    public function enviarIncidente(): void
    {
        if (! $this->activeColaboradorId) {
            return;
        }

        $this->validate([
            'incidenteTipo' => 'required',
            'incidenteLocalizacao' => 'required|string|max:255',
            'incidenteDescricao' => 'required|string',
            'incidenteFoto' => 'nullable|image|max:10240',
        ]);

        $path = null;
        if ($this->incidenteFoto) {
            $path = $this->incidenteFoto->store('incidentes', 'public');
        }

        IncidentReport::create([
            'colaborador_id' => $this->activeColaboradorId,
            'tipo' => $this->incidenteTipo,
            'data_hora' => now(),
            'localizacao' => $this->incidenteLocalizacao,
            'descricao' => $this->incidenteDescricao,
            'photo_path' => $path,
            'estado' => 'novo',
        ]);

        $this->reset(['incidenteTipo', 'incidenteLocalizacao', 'incidenteDescricao', 'incidenteFoto']);
        $this->activeSegurancaTab = 'procedimentos';
        $this->fecharSeguranca();
        session()->flash('success', 'Relatório submetido com sucesso. A equipa foi notificada.');
    }
}
