<?php

namespace App\Livewire;

use App\Models\UserSession;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Gestão de Sessões')]
class GestaoSessoes extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $onlyActive = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'onlyActive' => ['except' => false],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedOnlyActive()
    {
        $this->resetPage();
    }

    public function terminateSession(string $sessionId)
    {
        // 1. Remove from Laravel's database sessions table
        DB::table('sessions')->where('id', $sessionId)->delete();

        // 2. Mark as logged out in our persistent logs
        UserSession::where('session_id', $sessionId)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        session()->flash('success', 'Sessão terminada com sucesso.');
    }

    public function render()
    {
        $sessions = UserSession::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })->orWhere('ip_address', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%");
            })
            ->when($this->onlyActive, function ($query) {
                // Consider active if logout_at is null AND last_activity is recent (within session lifetime)
                $query->whereNull('logout_at')
                    ->where('last_activity_at', '>', now()->subMinutes(config('session.lifetime')));
            })
            ->orderBy('last_activity_at', 'desc')
            ->paginate(15);

        return view('livewire.gestao-sessoes', [
            'sessions' => $sessions,
        ]);
    }
}
