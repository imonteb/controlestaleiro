<?php

namespace App\Livewire;

use App\Models\LegalPage;
use Livewire\Component;

class GestaoLegalPages extends Component
{
    public $tab = 'privacidade';

    // Form fields
    public $titulo;

    public $versao;

    public $ultima_revisao;

    public $publicada;

    public $conteudo;

    public function mount()
    {
        $this->loadPageData();
    }

    public function updatedTab()
    {
        $this->loadPageData();
    }

    public function loadPageData()
    {
        $page = LegalPage::where('slug', $this->tab)->first();
        if ($page) {
            $this->titulo = $page->titulo;
            $this->versao = $page->versao;
            $this->ultima_revisao = $page->ultima_revisao?->format('Y-m-d');
            $this->publicada = $page->publicada;
            $this->conteudo = $page->conteudo;
        }
    }

    public function guardar()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'versao' => 'required|string|max:20',
            'ultima_revisao' => 'required|date',
            'publicada' => 'boolean',
            'conteudo' => 'required|string',
        ]);

        $page = LegalPage::where('slug', $this->tab)->first();
        if ($page) {
            $page->update([
                'titulo' => $this->titulo,
                'versao' => $this->versao,
                'ultima_revisao' => $this->ultima_revisao,
                'publicada' => $this->publicada,
                'conteudo' => $this->conteudo,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('success', 'Página legal atualizada com sucesso!');
        }
    }

    public function render()
    {
        return view('livewire.gestao-legal-pages')
            ->layout('layouts.app');
    }
}
