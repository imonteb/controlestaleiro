<?php

namespace App\Livewire\Saude;

use App\Models\AutoSocorroKit;
use App\Models\SaudeItem;
use App\Models\SaudeKitItem;
use App\Models\Veiculo;
use Livewire\Component;

class KitForm extends Component
{
    public ?AutoSocorroKit $kit = null;

    public $veiculo_id;

    public $designacao = '';

    public $identificador_kit = '';

    // Dynamic Items
    public $kit_itens = []; // Array of {id, saude_item_id, data_validade, quantidade}

    public function mount(?AutoSocorroKit $kit = null)
    {
        if ($kit && $kit->exists) {
            $this->kit = $kit;
            $this->veiculo_id = $kit->veiculo_id;
            $this->designacao = $kit->designacao;
            $this->identificador_kit = $kit->identificador_kit;

            foreach ($kit->itens as $item) {
                $this->kit_itens[] = [
                    'id' => $item->id,
                    'saude_item_id' => $item->saude_item_id,
                    'data_validade' => $item->data_validade?->format('Y-m-d'),
                    'quantidade' => $item->quantidade,
                ];
            }
        } else {
            $this->addItem(); // Start with one empty row
        }
    }

    public function addItem()
    {
        $this->kit_itens[] = [
            'id' => null,
            'saude_item_id' => '',
            'data_validade' => '',
            'quantidade' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->kit_itens[$index]);
        $this->kit_itens = array_values($this->kit_itens);
    }

    protected $rules = [
        'veiculo_id' => 'required|exists:vehiculos,id',
        'designacao' => 'required|string|max:255',
        'identificador_kit' => 'nullable|string|max:50',
        'kit_itens.*.saude_item_id' => 'required|exists:saude_itens,id',
        'kit_itens.*.quantidade' => 'required|integer|min:1',
        'kit_itens.*.data_validade' => 'nullable|date',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'veiculo_id' => $this->veiculo_id,
            'designacao' => $this->designacao,
            'identificador_kit' => $this->identificador_kit,
        ];

        if ($this->kit) {
            $this->kit->update($data);
        } else {
            $this->kit = AutoSocorroKit::create($data);
        }

        // Sync Items
        $this->kit->itens()->delete(); // Simple approach: delete and recreate
        foreach ($this->kit_itens as $itemData) {
            SaudeKitItem::create([
                'kit_id' => $this->kit->id,
                'saude_item_id' => $itemData['saude_item_id'],
                'data_validade' => $itemData['data_validade'] ?: null,
                'quantidade' => $itemData['quantidade'],
            ]);
        }

        session()->flash('success', 'Kit de saúde guardado com sucesso!');

        return $this->redirect(route('saude.index'), navigate: true);
    }

    public function render()
    {
        $veiculos = Veiculo::orderBy('matricula')->get();
        $base_itens = SaudeItem::orderBy('nombre')->get();

        return view('livewire.saude.kit-form', [
            'veiculos' => $veiculos,
            'base_itens' => $base_itens,
        ])->layout('layouts.app');
    }
}
