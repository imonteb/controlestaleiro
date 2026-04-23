<?php

namespace App\Livewire\Localizacoes;

use App\Models\Localizacao;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Localização')]
class Form extends Component
{
    public ?Localizacao $localizacao = null;

    public string $nombre = '';

    public bool $isEdit = false;

    protected function rules(): array
    {
        $uniqueRule = $this->isEdit
            ? 'unique:locaciones,nombre,'.($this->localizacao?->id ?? 'NULL')
            : 'unique:locaciones,nombre';

        return [
            'nombre' => ['required', 'string', 'max:255', $uniqueRule],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'O nome é obrigatório.',
            'nombre.unique' => 'Já existe uma localização com esse nome.',
        ];
    }

    public function mount(?Localizacao $localizacao = null): void
    {
        if ($localizacao && $localizacao->exists) {
            $this->isEdit = true;
            $this->localizacao = $localizacao;
            $this->nombre = $localizacao->nombre;
        }
    }

    public function save(): mixed
    {
        $this->validate();

        if ($this->isEdit) {
            $this->localizacao->update(['nombre' => $this->nombre]);
        } else {
            Localizacao::create(['nombre' => $this->nombre]);
        }

        return redirect()->route('localizacoes.index');
    }

    public function render()
    {
        return view('livewire.localizacoes.form')
            ->title($this->isEdit ? 'Editar Localização' : 'Nova Localização');
    }
}
