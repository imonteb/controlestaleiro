<?php

namespace App\Livewire\MaterialCategorias;

use App\Models\MaterialCategoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Categoria de Material')]
class Form extends Component
{
    public ?MaterialCategoria $categoria = null;

    public string $nome = '';

    public int $ordem = 0;

    public bool $activo = true;

    public bool $isEdit = false;

    protected function rules(): array
    {
        $uniqueRule = 'unique:material_categorias,nome';
        if ($this->isEdit && $this->categoria) {
            $uniqueRule .= ','.$this->categoria->id;
        }

        return [
            'nome' => ['required', 'string', 'max:100', $uniqueRule],
            'ordem' => 'required|integer|min:0|max:999',
            'activo' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique' => 'Já existe uma categoria com este nome.',
            'ordem.required' => 'A ordem é obrigatória.',
        ];
    }

    public function mount(?MaterialCategoria $categoria = null): void
    {
        if ($categoria && $categoria->exists) {
            $this->isEdit = true;
            $this->categoria = $categoria;
            $this->nome = $categoria->nome;
            $this->ordem = $categoria->ordem;
            $this->activo = $categoria->activo;
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'nome' => $this->nome,
            'ordem' => $this->ordem,
            'activo' => $this->activo,
        ];

        if ($this->isEdit) {
            $this->categoria->update($data);
        } else {
            MaterialCategoria::create($data);
        }

        return redirect()->route('material-categorias.index');
    }

    public function render(): mixed
    {
        return view('livewire.material-categorias.form');
    }
}
