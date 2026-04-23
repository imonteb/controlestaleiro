<?php

namespace App\Livewire\Materiais;

use App\Models\Material;
use App\Models\MaterialCategoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Material')]
class Form extends Component
{
    public ?Material $material = null;

    public string $codigo = '';

    public string $nome = '';

    public string $descripcion = '';

    public string $categoria_id = '';

    public string $unidade_padrao = 'UND';

    public bool $activo = true;

    public string $motivo_baja = '';

    public bool $isEdit = false;

    protected function rules(): array
    {
        $codigoUnique = 'unique:materiais,codigo';
        if ($this->isEdit && $this->material) {
            $codigoUnique .= ','.$this->material->id;
        }

        return [
            'codigo' => ['required', 'string', 'max:50', $codigoUnique],
            'nome' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_id' => 'nullable|exists:material_categorias,id',
            'unidade_padrao' => 'required|string|max:20',
            'activo' => 'boolean',
            'motivo_baja' => 'nullable|string|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'codigo.required' => 'O código é obrigatório.',
            'codigo.unique' => 'Já existe um material com este código.',
            'nome.required' => 'O nome é obrigatório.',
            'unidade_padrao.in' => 'Unidade inválida.',
        ];
    }

    public function mount(?Material $material = null): void
    {
        if ($material && $material->exists) {
            $this->isEdit = true;
            $this->material = $material;
            $this->codigo = $material->codigo;
            $this->nome = $material->nome;
            $this->descripcion = $material->descripcion ?? '';
            $this->categoria_id = (string) ($material->categoria_id ?? '');
            $this->unidade_padrao = $material->unidade_padrao;
            $this->activo = $material->activo;
            $this->motivo_baja = $material->motivo_baja ?? '';
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'codigo' => strtoupper(trim($this->codigo)),
            'nome' => trim($this->nome),
            'descripcion' => $this->descripcion ?: null,
            'categoria_id' => $this->categoria_id ?: null,
            'unidade_padrao' => strtoupper(trim($this->unidade_padrao)),
            'activo' => $this->activo,
            'motivo_baja' => ! $this->activo ? ($this->motivo_baja ?: null) : null,
        ];

        if ($this->isEdit) {
            $this->material->update($data);
        } else {
            Material::create($data);
        }

        return redirect()->route('materiais.index');
    }

    public function render(): mixed
    {
        return view('livewire.materiais.form', [
            'categorias' => MaterialCategoria::activas()->orderBy('ordem')->orderBy('nome')->get(),
            'unidades' => Material::UNIDADES,
        ]);
    }
}
