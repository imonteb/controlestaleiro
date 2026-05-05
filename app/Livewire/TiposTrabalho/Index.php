<?php

namespace App\Livewire\TiposTrabalho;

use App\Models\TipoTrabalho;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tipos de Trabalho')]
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

        $tipo = TipoTrabalho::find($this->eliminandoId);

        if ($tipo) {
            // Desasociar PEPs antes de eliminar
            $tipo->peps()->update(['tipo_trabajo_id' => null]);
            $tipo->delete();
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
        return view('livewire.tipos-trabalho.index', [
            'tipos' => TipoTrabalho::withCount('peps')
                ->when($this->search, fn ($q) => $q->where('nombre', 'like', '%'.$this->search.'%'))
                ->orderBy('nombre')->get(),
        ]);
    }
}
