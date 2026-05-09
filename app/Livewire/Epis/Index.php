<?php

namespace App\Livewire\Epis;

use App\Enums\EpiEntregaEstado;
use App\Imports\EpiItemsImport;
use App\Models\EpiItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Inventário')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $filtro = 'activos';

    public string $search = '';

    // Import
    public bool $showImport = false;

    public $ficheiroImport;

    public string $importMsg = '';

    public bool $importError = false;

    public ?int $desativandoId = null;

    public string $motivoBaixa = '';

    public bool $confirmandoEliminar = false;

    public ?int $eliminandoId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltro(): void
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

        /** @var EpiItem|null $item */
        $item = EpiItem::find($this->desativandoId);
        $item?->update([
            'activo' => false,
            'motivo_baja' => $this->motivoBaixa ?: null,
        ]);

        $this->desativandoId = null;
        $this->motivoBaixa = '';
    }

    public function reactivar(int $id): void
    {
        /** @var EpiItem|null $item */
        $item = EpiItem::find($id);
        $item?->update(['activo' => true, 'motivo_baja' => null]);
    }

    public function pedirEliminar(int $id): void
    {
        $this->eliminandoId = $id;
        $this->confirmandoEliminar = true;
    }

    public function eliminarPermanente(): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (! $user?->isEpi()) {
            $this->confirmandoEliminar = false;

            return;
        }

        /** @var EpiItem|null $item */
        $item = EpiItem::find($this->eliminandoId);
        if ($item && $item->entregas()->count() === 0 && $item->rececoes()->count() === 0) {
            $item->delete();
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
            Excel::import(new EpiItemsImport, $this->ficheiroImport->getRealPath());
            $this->importMsg = 'Importação concluída com sucesso.';
            $this->importError = false;
        } catch (\Throwable $e) {
            $this->importMsg = 'Erro na importação: '.$e->getMessage();
            $this->importError = true;
        }

        $this->ficheiroImport = null;
    }

    public function render()
    {
        $query = EpiItem::orderBy('nombre');

        if ($this->filtro === 'activos') {
            $query->ativos();
        } elseif ($this->filtro === 'inactivos') {
            $query->inativos();
        }

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhere('codigo', 'like', $s)
                    ->orWhere('ca_numero', 'like', $s)
                    ->orWhere('talla', 'like', $s);
            });
        }

        // Eager load stock sums to prevent N+1 query issue
        $query->withSum('rececoes', 'cantidad')
            ->withSum(['entregas' => fn ($q) => $q->whereIn('estado', EpiEntregaEstado::valoresComSaida())], 'cantidad')
            ->withSum('ajustes', 'diferencia');

        $items = $query->paginate(20);

        // Calculate final stock for each item using the eagerly loaded sums
        $items->getCollection()->each(function ($item) {
            $recebido = (int) $item->rececoes_sum_cantidad;
            $entregue = (int) $item->entregas_sum_cantidad;
            $ajuste = (int) $item->ajustes_sum_diferencia;

            $item->stock_total = $recebido - $entregue + $ajuste;
            $item->alerta_stock = $item->stock_total < $item->stock_minimo;
        });

        return view('livewire.epis.index', [
            'items' => $items,
        ]);
    }
}
