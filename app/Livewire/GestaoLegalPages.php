<?php

namespace App\Livewire;

use App\Models\LegalPage;
use App\Services\LegalVarsService;
use Livewire\Component;

class GestaoLegalPages extends Component
{
    public string $tab = 'privacidade';

    public bool $showVars = false;

    // Page fields
    public string $titulo = '';

    public string $versao = '';

    public string $ultima_revisao = '';

    public bool $publicada = true;

    public string $conteudo = '';

    // Variables
    public array $variaveis = [];

    public function mount(): void
    {
        $this->variaveis = LegalVarsService::all();
        $this->loadPageData();
    }

    public function updatedTab(): void
    {
        $this->loadPageData();
        $this->showVars = false;
    }

    public function loadPageData(): void
    {
        $page = LegalPage::where('slug', $this->tab)->first();

        if ($page) {
            $this->titulo = $page->titulo;
            $this->versao = $page->versao;
            $this->ultima_revisao = $page->ultima_revisao?->format('Y-m-d') ?? '';
            $this->publicada = (bool) $page->publicada;
            $this->conteudo = $page->conteudo;
        }
    }

    public function guardar(): void
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

            session()->flash('success', 'Página "'.ucfirst($this->tab).'" atualizada com sucesso.');
        }
    }

    public function guardarVariaveis(): void
    {
        $this->validate([
            'variaveis.empresa_nome' => 'required|string|max:255',
            'variaveis.empresa_nif' => 'required|string|max:30',
            'variaveis.empresa_morada' => 'required|string|max:500',
            'variaveis.empresa_email' => 'required|email|max:255',
            'variaveis.empresa_unidade' => 'required|string|max:50',
            'variaveis.app_nome' => 'required|string|max:100',
            'variaveis.dpo_nome' => 'nullable|string|max:255',
            'variaveis.dpo_email' => 'nullable|email|max:255',
            'variaveis.hosting_entidade' => 'required|string|max:100',
            'variaveis.hosting_pais' => 'required|string|max:100',
        ]);

        LegalVarsService::save($this->variaveis);

        session()->flash('success', 'Variáveis globais guardadas com sucesso.');
    }

    public function render(): mixed
    {
        return view('livewire.gestao-legal-pages', [
            'labels' => LegalVarsService::$labels,
        ])->layout('layouts.app');
    }
}
