<?php

namespace App\Livewire\Veiculos;

use App\Models\Veiculo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Veículos')]
class Index extends Component
{
    use WithPagination;

    public string $sortBy = 'marca';

    public string $sortDir = 'asc';

    public string $filtro = 'activos';

    public string $pesquisa = '';

    public ?int $desativandoId = null;

    public string $motivoBaixa = '';

    public ?int $editandoLinkId = null;

    public string $editandoLink = '';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public function updatingPesquisa(): void
    {
        $this->resetPage();
    }

    public function updatingFiltro(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function editarLink(int $id): void
    {
        $this->editandoLinkId = $id;
        $this->editandoLink = Veiculo::find($id)?->link_seguros ?? '';
    }

    public function guardarLink(): void
    {
        $this->validate(['editandoLink' => 'nullable|url|max:65535']);

        Veiculo::where('id', $this->editandoLinkId)->update(['link_seguros' => $this->editandoLink ?: null]);
        $this->editandoLinkId = null;
        $this->editandoLink = '';
    }

    public function desativar(int $id): void
    {
        $this->desativandoId = $id;
        $this->motivoBaixa = '';
    }

    public function confirmarDesativar(): void
    {
        $this->validate(['motivoBaixa' => 'nullable|string|max:500']);

        $veiculo = Veiculo::find($this->desativandoId);
        if ($veiculo) {
            $veiculo->activo = false;
            $veiculo->motivo_baja = $this->motivoBaixa ?: null;
            $veiculo->save();
        }

        $this->desativandoId = null;
        $this->motivoBaixa = '';
    }

    public function reactivar(int $id): void
    {
        $veiculo = Veiculo::find($id);
        if ($veiculo) {
            $veiculo->activo = true;
            $veiculo->motivo_baja = null;
            $veiculo->save();
        }
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminarPermanente(): void
    {
        if (! \Auth::user()?->isAdmin()) {
            $this->confirmandoEliminar = false;

            return;
        }
        $veiculo = Veiculo::find($this->eliminandoId);
        if ($veiculo) {
            $veiculo->delete();
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
        $query = Veiculo::orderBy($this->sortBy, $this->sortDir);

        if ($this->filtro === 'activos') {
            $query->ativos();
        } elseif ($this->filtro === 'inactivos') {
            $query->inativos();
        }

        if ($this->pesquisa !== '') {
            $s = '%'.$this->pesquisa.'%';
            $query->where(function ($q) use ($s) {
                $q->where('matricula', 'like', $s)
                    ->orWhere('marca', 'like', $s)
                    ->orWhere('modelo', 'like', $s);
            });
        }

        return view('livewire.veiculos.index', [
            'veiculos' => $query->paginate(20),
        ]);
    }
}
