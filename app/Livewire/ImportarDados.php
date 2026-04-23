<?php

namespace App\Livewire;

use App\Imports\ColaboradoresImport;
use App\Imports\PepsImport;
use App\Imports\VeiculosImport;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Importar Dados')]
class ImportarDados extends Component
{
    use WithFileUploads;

    public string $tab = 'colaboradores';

    public $ficheiroColaboradores = null;

    public $ficheiroVeiculos = null;

    public $ficheiroPeps = null;

    public bool $success = false;

    public string $successMsg = '';

    public bool $hasError = false;

    public string $errorMsg = '';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->success = false;
        $this->hasError = false;
    }

    public function importarColaboradores(): void
    {
        $this->validate([
            'ficheiroColaboradores' => 'required|file|mimes:xlsx,xls,csv',
        ], ['ficheiroColaboradores.required' => 'Selecione um ficheiro Excel.']);

        try {
            Excel::import(new ColaboradoresImport, $this->ficheiroColaboradores->getRealPath());
            $this->success = true;
            $this->successMsg = 'Colaboradores importados com sucesso.';
            $this->hasError = false;
            $this->ficheiroColaboradores = null;
        } catch (\Throwable $e) {
            $this->hasError = true;
            $this->errorMsg = 'Erro ao importar: '.$e->getMessage();
            $this->success = false;
        }
    }

    public function importarVeiculos(): void
    {
        $this->validate([
            'ficheiroVeiculos' => 'required|file|mimes:xlsx,xls,csv',
        ], ['ficheiroVeiculos.required' => 'Selecione um ficheiro Excel.']);

        try {
            Excel::import(new VeiculosImport, $this->ficheiroVeiculos->getRealPath());
            $this->success = true;
            $this->successMsg = 'Veículos importados com sucesso.';
            $this->hasError = false;
            $this->ficheiroVeiculos = null;
        } catch (\Throwable $e) {
            $this->hasError = true;
            $this->errorMsg = 'Erro ao importar: '.$e->getMessage();
            $this->success = false;
        }
    }

    public function importarPeps(): void
    {
        $this->validate([
            'ficheiroPeps' => 'required|file|mimes:xlsx,xls,csv',
        ], ['ficheiroPeps.required' => 'Selecione um ficheiro Excel.']);

        try {
            $import = new PepsImport;
            Excel::import($import, $this->ficheiroPeps->getRealPath());
            $this->success = true;
            $this->successMsg = "PEPs importados com sucesso ({$import->importedCount} registos).";
            $this->hasError = false;
            $this->ficheiroPeps = null;
        } catch (\Throwable $e) {
            $this->hasError = true;
            $this->errorMsg = 'Erro ao importar: '.$e->getMessage();
            $this->success = false;
        }
    }

    public function render()
    {
        return view('livewire.importar-dados');
    }
}
