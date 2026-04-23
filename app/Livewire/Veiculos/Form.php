<?php

namespace App\Livewire\Veiculos;

use App\Models\Veiculo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Veiculo $veiculo = null;

    public $marca = '';

    public $modelo = '';

    public $matricula = '';

    public $isEdit = false;

    // Validation rules
    protected function rules()
    {
        return [
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'matricula' => 'required|string|max:255|unique:vehiculos,matricula,'.($this->veiculo ? $this->veiculo->id : 'NULL'),
        ];
    }

    public function mount(?Veiculo $veiculo = null)
    {
        if ($veiculo && $veiculo->exists) {
            $this->isEdit = true;
            $this->veiculo = $veiculo;

            $this->marca = $veiculo->marca;
            $this->modelo = $veiculo->modelo;
            $this->matricula = $veiculo->matricula;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'matricula' => $this->matricula,
        ];

        if ($this->isEdit) {
            $this->veiculo->update($data);
        } else {
            Veiculo::create($data);
        }

        return redirect()->route('veiculos.index');
    }

    public function render()
    {
        return view('livewire.veiculos.form')
            ->title($this->isEdit ? 'Editar Veículo' : 'Novo Veículo');
    }
}
