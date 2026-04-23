<?php

namespace App\Livewire\Saude;

use App\Models\SaudeItem;
use Livewire\Component;

class ItensIndex extends Component
{
    public $nombre = '';

    public $unidade = 'un';

    public $edit_id = null;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'unidade' => 'required|string|max:10',
    ];

    public function save()
    {
        $this->validate();

        if ($this->edit_id) {
            SaudeItem::find($this->edit_id)->update([
                'nombre' => $this->nombre,
                'unidade' => $this->unidade,
            ]);
        } else {
            SaudeItem::create([
                'nombre' => $this->nombre,
                'unidade' => $this->unidade,
            ]);
        }

        $this->reset(['nombre', 'unidade', 'edit_id']);
        session()->flash('success', 'Artigo de saúde guardado!');
    }

    public function edit($id)
    {
        $item = SaudeItem::findOrFail($id);
        $this->edit_id = $id;
        $this->nombre = $item->nombre;
        $this->unidade = $item->unidade;
    }

    public function delete($id)
    {
        SaudeItem::findOrFail($id)->delete();
        session()->flash('success', 'Artigo removido!');
    }

    public function render()
    {
        return view('livewire.saude.itens-index', [
            'itens' => SaudeItem::orderBy('nombre')->get(),
        ])->layout('layouts.app');
    }
}
