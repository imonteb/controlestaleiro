<?php

namespace App\Livewire\MaterialCategorias;

use App\Models\MaterialCategoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Categorias de Materiais')]
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

        $categoria = MaterialCategoria::find($this->eliminandoId);

        if ($categoria) {
            $categoria->materiais()->update(['categoria_id' => null]);
            $categoria->delete();
        }

        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function render(): mixed
    {
        return view('livewire.material-categorias.index', [
            'categorias' => MaterialCategoria::withCount('materiais')
                ->when($this->search, fn ($q) => $q->where('nome', 'like', '%'.$this->search.'%'))
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
