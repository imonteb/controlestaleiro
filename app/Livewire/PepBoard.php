<?php

namespace App\Livewire;

use App\Models\Pep;
use Livewire\Component;

class PepBoard extends Component
{
    public function render()
    {
        $peps = Pep::with(['locacion', 'tipoTrabajo'])->get();

        return view('livewire.pep-board', [
            'peps' => $peps,
        ]);
    }
}
