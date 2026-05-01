<?php

namespace App\Livewire;

use App\Models\LegalPage;
use App\Services\LegalVarsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class LegalPages extends Component
{
    public string $tab = 'privacidade';

    public function mount(string $tab = 'privacidade'): void
    {
        $this->tab = in_array($tab, ['privacidade', 'termos', 'cookies']) ? $tab : 'privacidade';
    }

    #[Layout('layouts.phone')]
    #[Title('Informação Legal')]
    public function render(): mixed
    {
        $pagina = LegalPage::where('slug', $this->tab)->publicada()->firstOrFail();

        $pagina->conteudo = LegalVarsService::replace($pagina->conteudo);

        return view('livewire.legal-pages', [
            'pagina' => $pagina,
        ]);
    }
}
