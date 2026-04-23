<?php

namespace App\Livewire\LocaisFrequentes;

use App\Models\LocalFrequente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Locais Frequentes')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroTipo = '';

    public string $filtroActivo = 'activos';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminar(): void
    {
        LocalFrequente::find($this->eliminandoId)?->delete();

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
        $locais = LocalFrequente::when($this->search, fn ($q) => $q->where('nome', 'like', '%'.$this->search.'%')
            ->orWhere('localidade', 'like', '%'.$this->search.'%')
            ->orWhere('morada', 'like', '%'.$this->search.'%'))
            ->when($this->filtroTipo, fn ($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroActivo === 'activos', fn ($q) => $q->where('activo', true))
            ->when($this->filtroActivo === 'inactivos', fn ($q) => $q->where('activo', false))
            ->orderBy('nome')
            ->paginate(25);

        return view('livewire.locais-frequentes.index', [
            'locais' => $locais,
        ]);
    }
}
