<?php

namespace App\Livewire;

use App\Models\Colaborador;
use App\Models\Veiculo;
use Carbon\Carbon;
use Livewire\Component;

class RecursosBoard extends Component
{
    public $data;

    public function mount()
    {
        $this->data = Carbon::today()->toDateString();
    }

    public function render()
    {
        // Por agora, trazemos todos os recursos.
        // Mais adiante, aqui filtraremos os que NÃO estão atribuídos ainda na `$this->data`.

        return view('livewire.recursos-board', [
            'colaboradores_livres' => Colaborador::all(),
            'veiculos_livres' => Veiculo::all(),
        ]);
    }
}
