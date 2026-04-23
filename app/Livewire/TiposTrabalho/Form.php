<?php

namespace App\Livewire\TiposTrabalho;

use App\Models\TipoTrabalho;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tipo de Trabalho')]
class Form extends Component
{
    public ?TipoTrabalho $tipoTrabalho = null;

    public string $nombre = '';

    public string $color = '#3B82F6';

    public bool $isEdit = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'O nome é obrigatório.',
            'color.regex' => 'A cor deve ser um valor hexadecimal válido (ex: #FF5733).',
        ];
    }

    public function mount(?TipoTrabalho $tipoTrabalho = null): void
    {
        if ($tipoTrabalho && $tipoTrabalho->exists) {
            $this->isEdit = true;
            $this->tipoTrabalho = $tipoTrabalho;
            $this->nombre = $tipoTrabalho->nombre;
            $this->color = $tipoTrabalho->color ?? '#3B82F6';
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'color' => $this->color,
        ];

        if ($this->isEdit) {
            $this->tipoTrabalho->update($data);
        } else {
            TipoTrabalho::create($data);
        }

        return redirect()->route('tipos-trabalho.index');
    }

    public function render()
    {
        return view('livewire.tipos-trabalho.form')
            ->title($this->isEdit ? 'Editar Tipo de Trabalho' : 'Novo Tipo de Trabalho');
    }
}
