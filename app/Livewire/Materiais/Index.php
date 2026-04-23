<?php

namespace App\Livewire\Materiais;

use App\Imports\MateriaisImport;
use App\Models\Material;
use App\Models\MaterialCategoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Materiais')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $filtroCategoria = '';

    public string $filtroActivo = 'activos';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public bool $showImport = false;

    public $ficheiroImport = null;

    public bool $importSuccess = false;

    public string $importMsg = '';

    public bool $importError = false;

    public string $importErrorMsg = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroActivo(): void
    {
        $this->resetPage();
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminarPermanente(): void
    {
        Material::find($this->eliminandoId)?->delete();

        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminar = false;
        $this->eliminandoId = null;
    }

    public function importar(): void
    {
        $this->validate([
            'ficheiroImport' => 'required|file|mimes:xlsx,xls,csv',
        ], ['ficheiroImport.required' => 'Selecione um ficheiro Excel.']);

        try {
            $import = new MateriaisImport;
            Excel::import($import, $this->ficheiroImport->getRealPath());

            $this->importSuccess = true;
            $this->importMsg = 'Materiais importados com sucesso.';
            $this->importError = false;
            $this->ficheiroImport = null;
            $this->showImport = false;
        } catch (\Throwable $e) {
            $this->importError = true;
            $this->importErrorMsg = 'Erro ao importar: '.$e->getMessage();
            $this->importSuccess = false;
        }
    }

    public function render(): mixed
    {
        $materiais = Material::with('categoria')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('nome', 'like', '%'.$this->search.'%')
                    ->orWhere('codigo', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filtroCategoria, fn ($q) => $q->where('categoria_id', $this->filtroCategoria))
            ->when($this->filtroActivo === 'activos', fn ($q) => $q->where('activo', true))
            ->when($this->filtroActivo === 'inactivos', fn ($q) => $q->where('activo', false))
            ->orderBy('nome')
            ->paginate(25);

        return view('livewire.materiais.index', [
            'materiais' => $materiais,
            'categorias' => MaterialCategoria::activas()->orderBy('ordem')->orderBy('nome')->get(),
        ]);
    }
}
