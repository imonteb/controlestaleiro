<?php

namespace App\Livewire\Extintores;

use App\Models\Extintor;
use App\Models\Veiculo;
use Livewire\Component;

class Form extends Component
{
    public ?Extintor $extintor = null;

    public $num_serie = '';

    public $tipo_agente = 'Pó Químico';

    public $tamanho = '6kg';

    public $estado = 'Conforme';

    public $data_verificacao;

    public $proxima_revisao;

    public $veiculo_id;

    public function mount(?Extintor $extintor = null)
    {
        if ($extintor && $extintor->exists) {
            $this->extintor = $extintor;
            $this->num_serie = $extintor->num_serie;
            $this->tipo_agente = $extintor->tipo_agente;
            $this->tamanho = $extintor->tamanho;
            $this->estado = $extintor->estado;
            $this->data_verificacao = $extintor->data_verificacao?->format('Y-m-d');
            $this->proxima_revisao = $extintor->proxima_revisao?->format('Y-m-d');
            $this->veiculo_id = $extintor->veiculo_id;
        } else {
            $this->data_verificacao = now()->format('Y-m-d');
            $this->proxima_revisao = now()->addYear()->format('Y-m-d');
        }
    }

    protected $rules = [
        'num_serie' => 'required|string|max:100',
        'tipo_agente' => 'required|string|max:50',
        'tamanho' => 'required|string|max:50',
        'estado' => 'required|string',
        'data_verificacao' => 'nullable|date',
        'proxima_revisao' => 'nullable|date',
        'veiculo_id' => 'nullable|exists:veiculos,id',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'num_serie' => $this->num_serie,
            'tipo_agente' => $this->tipo_agente,
            'tamanho' => $this->tamanho,
            'estado' => $this->estado,
            'data_verificacao' => $this->data_verificacao,
            'proxima_revisao' => $this->proxima_revisao,
            'veiculo_id' => $this->veiculo_id ?: null,
        ];

        if ($this->extintor) {
            $this->extintor->update($data);
            session()->flash('success', 'Extintor atualizado com sucesso!');
        } else {
            Extintor::create($data);
            session()->flash('success', 'Extintor registado com sucesso!');
        }

        return $this->redirect(route('extintores.index'), navigate: true);
    }

    public function render()
    {
        $veiculos = Veiculo::orderBy('matricula')->get();

        return view('livewire.extintores.form', ['veiculos' => $veiculos])->layout('layouts.app');
    }
}
