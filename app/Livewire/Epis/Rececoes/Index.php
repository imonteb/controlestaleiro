<?php

namespace App\Livewire\Epis\Rececoes;

use App\Imports\EpiRececoesImport;
use App\Models\EpiItem;
use App\Models\EpiRececao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Recepções EPI')]
class Index extends Component
{
    use WithFileUploads;

    public string $search = '';

    public ?int $filtroEpi = null;

    // Import
    public bool $showImport = false;

    public $ficheiroImport;

    public string $mensagemImport = '';

    public bool $importError = false;

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

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

        /** @var EpiRececao|null $rececao */
        $rececao = EpiRececao::find($this->eliminandoId);
        if ($rececao) {
            $rececao->delete();
        }
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
            'ficheiroImport' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        try {
            Excel::import(new EpiRececoesImport(Auth::id()), $this->ficheiroImport->getRealPath());
            $this->mensagemImport = 'Importação concluída com sucesso.';
            $this->importError = false;
        } catch (\Throwable $e) {
            $this->mensagemImport = 'Erro na importação: '.$e->getMessage();
            $this->importError = true;
        }

        $this->ficheiroImport = null;
    }

    public function render()
    {
        $query = EpiRececao::with(['epiItem', 'registradoPor'])->orderByDesc('fecha');

        if ($this->filtroEpi) {
            $query->where('epi_item_id', $this->filtroEpi);
        }

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('proveedor', 'like', $s)
                    ->orWhere('numero_factura', 'like', $s)
                    ->orWhereHas('epiItem', fn ($eq) => $eq->where('nombre', 'like', $s));
            });
        }

        return view('livewire.epis.rececoes.index', [
            'rececoes' => $query->get(),
            'epiItems' => EpiItem::ativos()->orderBy('nombre')->get(),
        ]);
    }
}
