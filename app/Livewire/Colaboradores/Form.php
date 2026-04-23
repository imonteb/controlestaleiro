<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Colaborador $colaborador = null;

    public $numero_colaborador = '';

    public $nome = '';

    public $apelido = '';

    public $telefone = '';

    public $cargo = '';

    public bool $visible_en_dashboard = true;

    public $isEdit = false;

    // Validation rules
    protected function rules()
    {
        return [
            'numero_colaborador' => 'required|string|max:255|unique:colaboradores,numero_colaborador,'.($this->colaborador ? $this->colaborador->id : 'NULL'),
            'nome' => 'required|string|max:255',
            'apelido' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'cargo' => 'required|string|max:255',
            'visible_en_dashboard' => 'boolean',
        ];
    }

    public function mount(?Colaborador $colaborador = null)
    {
        if ($colaborador && $colaborador->exists) {
            $this->isEdit = true;
            $this->colaborador = $colaborador;

            $this->numero_colaborador = $colaborador->numero_colaborador;
            $this->nome = $colaborador->nombre;
            $this->apelido = $colaborador->apellido;
            $this->telefone = $colaborador->telefono;
            $this->cargo = $colaborador->denominacion_cargo;
            $this->visible_en_dashboard = (bool) $colaborador->visible_en_dashboard;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'numero_colaborador' => $this->numero_colaborador,
            'nombre' => $this->nome,
            'apellido' => $this->apelido,
            'telefono' => $this->telefone,
            'denominacion_cargo' => $this->cargo,
            'visible_en_dashboard' => $this->visible_en_dashboard,
        ];

        if ($this->isEdit) {
            $this->colaborador->update($data);
        } else {
            Colaborador::create($data);
        }

        return redirect()->route('colaboradores.index');
    }

    public function render()
    {
        return view('livewire.colaboradores.form')
            ->title($this->isEdit ? 'Editar Colaborador' : 'Novo Colaborador');
    }
}
