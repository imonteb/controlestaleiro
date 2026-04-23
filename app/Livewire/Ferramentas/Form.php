<?php

namespace App\Livewire\Ferramentas;

use App\Models\Ferramenta;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Ferramenta $ferramenta = null;

    public $referencia = '';

    public $designacao = '';

    public $marca = '';

    public $modelo = '';

    public $num_serie = '';

    public $familia = '';

    public $localizacao = '';

    public $periodicidade_meses = 12;

    public $tipo_documentacao = 'Manual';

    public $estado_operacional = 'Apto';

    public $activo = true;

    public function mount(?Ferramenta $ferramenta = null)
    {
        if ($ferramenta && $ferramenta->exists) {
            $this->ferramenta = $ferramenta;
            $this->referencia = $ferramenta->referencia;
            $this->designacao = $ferramenta->designacao;
            $this->marca = $ferramenta->marca;
            $this->modelo = $ferramenta->modelo;
            $this->num_serie = $ferramenta->num_serie;
            $this->familia = $ferramenta->familia;
            $this->localizacao = $ferramenta->localizacao ?? '';
            $this->periodicidade_meses = $ferramenta->periodicidade_meses ?: 12;
            $this->tipo_documentacao = $ferramenta->tipo_documentacao ?: 'Manual';
            $this->estado_operacional = $ferramenta->estado_operacional ?: 'Apto';
            $this->activo = $ferramenta->activo;
        }
    }

    protected function rules()
    {
        return [
            'designacao' => 'required|string|max:255',
            'referencia' => 'required|string|max:50|unique:ferramentas,referencia,'.($this->ferramenta->id ?? 'NULL'),
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'num_serie' => 'nullable|string|max:100',
            'familia' => 'nullable|string|max:100',
            'localizacao' => 'nullable|string|max:100',
            'periodicidade_meses' => 'required|integer|min:1',
            'tipo_documentacao' => 'required|string',
            'estado_operacional' => 'required|string',
            'activo' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'referencia' => $this->referencia,
            'designacao' => $this->designacao,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'num_serie' => $this->num_serie,
            'familia' => $this->familia,
            'localizacao' => $this->localizacao ?: null,
            'periodicidade_meses' => $this->periodicidade_meses,
            'tipo_documentacao' => $this->tipo_documentacao,
            'estado_operacional' => $this->estado_operacional,
            'activo' => $this->activo,
        ];

        if ($this->ferramenta) {
            $this->ferramenta->update($data);
            session()->flash('success', 'Ferramenta atualizada com sucesso!');
        } else {
            Ferramenta::create($data);
            session()->flash('success', 'Ferramenta criada com sucesso!');
        }

        return $this->redirect(route('ferramentas.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.ferramentas.form');
    }
}
