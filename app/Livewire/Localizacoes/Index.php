<?php

namespace App\Livewire\Localizacoes;

use App\Models\Localizacao;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Localizações')]
class Index extends Component
{
    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public string $search = '';

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminarPermanente(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $locacion = Localizacao::find($this->eliminandoId);

        if ($locacion) {
            // Desassociar PEPs antes de eliminar
            $locacion->peps()->update(['locacion_id' => null]);
            $locacion->delete();
        }

        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function render()
    {
        return view('livewire.localizacoes.index', [
            'locaciones' => Localizacao::withCount('peps')
                ->when($this->search, fn ($q) => $q->where('nombre', 'like', '%'.$this->search.'%'))
                ->orderBy('nombre')->get(),
        ]);
    }
}
