<?php

namespace App\Livewire\Peps;

use App\Models\Localizacao;
use App\Models\Pep;
use App\Models\TipoTrabalho;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('PEP')]
class Form extends Component
{
    public ?Pep $pep = null;

    public string $nome = '';

    public ?int $localizacao_id = null;

    public ?int $tipo_trabalho_id = null;

    public bool $isEdit = false;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'localizacao_id' => 'nullable|exists:localizacoes,id',
            'tipo_trabalho_id' => 'nullable|exists:tipos_trabalho,id',
        ];
    }

    public function mount(?Pep $pep = null): void
    {
        if ($pep && $pep->exists) {
            $this->isEdit = true;
            $this->pep = $pep;
            $this->nome = $pep->nombre;
            $this->localizacao_id = $pep->localizacao_id;
            $this->tipo_trabalho_id = $pep->tipo_trabalho_id;
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'nombre' => $this->nome,
            'locacion_id' => $this->localizacao_id ?: null,
            'tipo_trabajo_id' => $this->tipo_trabalho_id ?: null,
        ];

        if ($this->isEdit) {
            $this->pep->update($data);
        } else {
            Pep::create($data);
        }

        return redirect()->route('peps.index');
    }

    public function render()
    {
        return view('livewire.peps.form', [
            'localizacoes' => Localizacao::orderBy('nombre')->get(),
            'tiposTrabalho' => TipoTrabalho::orderBy('nombre')->get(),
        ])->title($this->isEdit ? 'Editar PEP' : 'Novo PEP');
    }
}
