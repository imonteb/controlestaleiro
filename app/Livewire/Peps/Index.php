<?php

namespace App\Livewire\Peps;

use App\Models\Pep;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('PEPs')]
class Index extends Component
{
    use WithPagination;

    public string $filtro = 'activos';

    public string $pesquisa = '';

    public ?int $desativandoId = null;

    public string $motivoBaixa = '';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public function updatingFiltro(): void
    {
        $this->resetPage();
    }

    public function updatingPesquisa(): void
    {
        $this->resetPage();
    }

    public function desativar(int $id): void
    {
        $this->desativandoId = $id;
        $this->motivoBaixa = '';
    }

    public function confirmarDesativar(): void
    {
        $this->validate(['motivoBaixa' => 'nullable|string|max:500']);

        Pep::find($this->desativandoId)?->update([
            'activo' => false,
            'motivo_baja' => $this->motivoBaixa ?: null,
        ]);

        $this->desativandoId = null;
        $this->motivoBaixa = '';
    }

    public function reativar(int $id): void
    {
        Pep::find($id)?->update(['activo' => true, 'motivo_baja' => null]);
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminarPermanente(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->confirmandoEliminar = false;

            return;
        }
        Pep::find($this->eliminandoId)?->delete();
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
        $query = Pep::with(['localizacao', 'tipoTrabalho'])->orderBy('nombre');

        if ($this->filtro === 'activos') {
            $query->ativos();
        } elseif ($this->filtro === 'inactivos') {
            $query->inativos();
        }

        if ($this->pesquisa !== '') {
            $s = '%'.$this->pesquisa.'%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhereHas('localizacao', fn ($lq) => $lq->where('nombre', 'like', $s))
                    ->orWhereHas('tipoTrabalho', fn ($tq) => $tq->where('nombre', 'like', $s));
            });
        }

        return view('livewire.peps.index', [
            'peps' => $query->paginate(25),
        ]);
    }
}
