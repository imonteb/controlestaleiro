<?php

namespace App\Livewire\Extintores;

use App\Imports\ExtintoresImport;
use App\Models\Extintor;
use App\Models\Veiculo;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]

class Index extends Component
{
    use WithFileUploads;
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $filtro_veiculo = '';

    // Import properties
    public $showImport = false;

    public $ficheiroImport;

    public $importMsg = '';

    public $importError = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Extintor::findOrFail($id)->delete();
        session()->flash('success', 'Extintor removido com sucesso!');
    }

    public function importar()
    {
        $this->validate([
            'ficheiroImport' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ExtintoresImport, $this->ficheiroImport);
            $this->importMsg = 'Importação concluída com sucesso!';
            $this->ficheiroImport = null;
            $this->showImport = false;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->importMsg = '';
            $this->importError = 'Erro na importación: '.$e->getMessage();
        }
    }

    public function render()
    {
        $query = Extintor::with('veiculo')
            ->when($this->search, function ($q) {
                $q->where('num_serie', 'like', '%'.$this->search.'%')
                    ->orWhere('tipo_agente', 'like', '%'.$this->search.'%');
            })
            ->when($this->filtro_veiculo, fn ($q) => $q->where('veiculo_id', $this->filtro_veiculo));

        $extintores = $query->paginate(20);
        $veiculos = Veiculo::orderBy('matricula')->get();

        return view('livewire.extintores.index', [
            'extintores' => $extintores,
            'veiculos' => $veiculos,
        ]);
    }
}
