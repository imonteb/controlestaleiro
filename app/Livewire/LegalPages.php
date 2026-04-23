<?php

namespace App\Livewire;

use App\Models\LegalPage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class LegalPages extends Component
{
    public $tab = 'privacidade';

    public function mount($tab = 'privacidade')
    {
        $this->tab = in_array($tab, ['privacidade', 'termos', 'cookies']) ? $tab : 'privacidade';
    }

    #[Layout('layouts.phone')]
    #[Title('Informação Legal')]
    public function render()
    {
        $pagina = LegalPage::where('slug', $this->tab)->publicada()->firstOrFail();

        return view('livewire.legal-pages', [
            'pagina' => $pagina,
        ]);
    }
}
