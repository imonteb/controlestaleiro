<?php

namespace App\Livewire;

use App\Models\EmergencyContact;
use App\Models\EmergencyProcedure;
use App\Models\IncidentReport;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoSeguranca extends Component
{
    use WithPagination;

    public $activeTab = 'contactos';

    protected $queryString = ['activeTab' => ['as' => 'tab']];

    public function mount()
    {
        // Capture the tab from the URL if present
        $this->activeTab = request()->query('tab', $this->activeTab);
    }

    // Form inputs: Contactos
    public $contato_id;

    public $nome = '';

    public $telefone = '';

    public $descricao = '';

    public $ordem = 0;

    public $logo = '';

    // Form inputs: Procedimentos
    public $procedimento_id;

    public $titulo = '';

    public $subtitulo = '';

    public $conteudo = '';

    public $tipo_proc = 'geral';

    public $ordem_proc = 0;

    public $isModalOpen = false;

    public function render()
    {
        $contactos = EmergencyContact::orderBy('ordem')->orderBy('nome')->get();
        $procedimentos = EmergencyProcedure::orderBy('ordem')->orderBy('tipo')->orderBy('titulo')->get();
        $incidentes = IncidentReport::with('colaborador')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.gestao-seguranca', [
            'contactos' => $contactos,
            'procedimentos' => $procedimentos,
            'incidentes' => $incidentes,
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->reset(['contato_id', 'nome', 'telefone', 'descricao', 'ordem', 'logo', 'procedimento_id', 'titulo', 'subtitulo', 'conteudo', 'tipo_proc', 'ordem_proc']);
        $this->resetValidation();
        $this->isModalOpen = false;
    }

    public function salvarContato()
    {
        $this->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:50',
            'descricao' => 'nullable|string|max:255',
            'ordem' => 'integer',
            'logo' => 'nullable|string',
        ]);

        EmergencyContact::updateOrCreate(
            ['id' => $this->contato_id],
            [
                'nome' => $this->nome,
                'telefone' => $this->telefone,
                'descricao' => $this->descricao,
                'ordem' => (int) $this->ordem,
                'logo' => $this->logo,
                'activo' => true,
            ]
        );

        session()->flash('success', 'Contato guardado com sucesso.');
        $this->closeModal();
    }

    public function editarContato($id)
    {
        $contato = EmergencyContact::findOrFail($id);
        $this->contato_id = $contato->id;
        $this->nome = $contato->nome;
        $this->telefone = $contato->telefone;
        $this->descricao = $contato->descricao;
        $this->ordem = $contato->ordem;
        $this->logo = $contato->logo;

        $this->openModal();
    }

    public function apagarContato($id)
    {
        EmergencyContact::find($id)?->delete();
        session()->flash('success', 'Contato apagado.');
    }

    public function salvarProcedimento()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'subtitulo' => 'nullable|string|max:255',
            'conteudo' => 'required|string',
            'tipo_proc' => 'required|in:geral,acidente,transito,seguro',
            'ordem_proc' => 'integer',
        ]);

        EmergencyProcedure::updateOrCreate(
            ['id' => $this->procedimento_id],
            [
                'titulo' => $this->titulo,
                'subtitulo' => $this->subtitulo,
                'conteudo' => $this->conteudo,
                'tipo' => $this->tipo_proc,
                'ordem' => (int) $this->ordem_proc,
                'activo' => true,
            ]
        );

        session()->flash('success', 'Procedimento guardado com sucesso.');
        $this->closeModal();
    }

    public function editarProcedimento($id)
    {
        $proc = EmergencyProcedure::findOrFail($id);
        $this->procedimento_id = $proc->id;
        $this->titulo = $proc->titulo;
        $this->subtitulo = $proc->subtitulo;
        $this->conteudo = $proc->conteudo;
        $this->tipo_proc = $proc->tipo;
        $this->ordem_proc = $proc->ordem;

        $this->activeTab = 'procedimentos';
        $this->openModal();
    }

    public function apagarProcedimento($id)
    {
        EmergencyProcedure::find($id)?->delete();
        session()->flash('success', 'Procedimento apagado.');
    }

    public function alterarEstadoIncidente($id, $novoEstado)
    {
        $inc = IncidentReport::findOrFail($id);
        $inc->update(['estado' => $novoEstado]);
        session()->flash('success', 'Estado do incidente atualizado.');
    }
}
