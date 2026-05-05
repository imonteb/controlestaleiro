<?php

namespace App\Livewire\Saude;

use App\Imports\AutoSocorroKitsImport;
use App\Models\AutoSocorroKit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]

class Index extends Component
{
    use WithFileUploads;

    public $ficheiroImport;

    public $importMsg = '';

    public $importError = '';

    public $showImport = false;

    public function deleteKit($id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        AutoSocorroKit::findOrFail($id)->delete();
        session()->flash('success', 'Kit de saúde removido com sucesso!');
    }

    public function importar()
    {
        $this->validate([
            'ficheiroImport' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new AutoSocorroKitsImport, $this->ficheiroImport);
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
        // Load kits with their items and vehicle
        $kits = AutoSocorroKit::with(['veiculo', 'itens.item'])->get();

        // Calculate status for each kit in memory for the UI
        foreach ($kits as $kit) {
            $kit->status_saude = $this->calculateKitStatus($kit);
        }

        return view('livewire.saude.index', [
            'kits' => $kits,
        ]);
    }

    private function calculateKitStatus($kit)
    {
        if ($kit->itens->isEmpty()) {
            return 'Vazio';
        }

        $hasExpired = false;
        foreach ($kit->itens as $kitItem) {
            if ($kitItem->data_validade && $kitItem->data_validade->isPast()) {
                $hasExpired = true;
                break;
            }
        }

        return $hasExpired ? 'Expirado' : 'OK';
    }
}
