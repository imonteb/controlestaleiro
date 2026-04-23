<?php

namespace App\Livewire\Concerns;

use App\Models\Colaborador;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

trait HasLogin
{
    public $loginNumero = '';

    public $loginPin = '';

    public $isLoggingIn = false;

    public $showTermsModal = false;

    public $activeColaboradorId = null;

    public bool $showPinChange = false;

    public string $pinAtual = '';

    public string $pinNovo = '';

    public string $pinNovoConfirmacao = '';

    public function loginColaborador(): void
    {
        $throttleKey = 'login-phone:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('login', "Demasiadas tentativas. Tente em {$seconds} segundos.");

            return;
        }

        $colaborador = Colaborador::where('numero_colaborador', $this->loginNumero)->first();

        if (! $colaborador || ! Hash::check($this->loginPin, $colaborador->pin)) {
            $colaborador = null;
        }

        if ($colaborador) {
            RateLimiter::clear($throttleKey);
            $this->activeColaboradorId = $colaborador->id;
            session(['active_colaborador_id' => $colaborador->id]);
            $this->isLoggingIn = false;
            $this->reset(['loginNumero', 'loginPin']);

            $this->dispatch('colaborador-loggedin', colaboradorId: $colaborador->id);

            if (is_null($colaborador->terms_accepted_at)) {
                $this->showTermsModal = true;
            } else {
                session()->flash('success', "Olá, {$colaborador->nombre}!");
            }
        } else {
            RateLimiter::hit($throttleKey);
            $this->addError('login', 'Nº de colaborador ou PIN incorretos.');
            session()->flash('error', 'Dados de acesso inválidos.');
        }
    }

    public function acceptFirstTerms(): void
    {
        if (! $this->activeColaboradorId) {
            return;
        }

        $colaborador = Colaborador::find($this->activeColaboradorId);
        if ($colaborador && is_null($colaborador->terms_accepted_at)) {
            $colaborador->terms_accepted_at = now();
            $colaborador->save();
        }

        $this->showTermsModal = false;
        session()->flash('success', "Olá, {$colaborador->nombre}! Normas aceites.");
    }

    public function logoutColaborador(): mixed
    {
        $this->activeColaboradorId = null;
        session()->forget('active_colaborador_id');
        session()->flash('success', 'Sessão encerrada.');

        return redirect()->to('/');
    }

    public function alterarPin(): void
    {
        $colaborador = Colaborador::findOrFail($this->activeColaboradorId);

        if (! Hash::check($this->pinAtual, $colaborador->pin)) {
            $this->addError('pinAtual', 'PIN atual incorreto.');

            return;
        }

        $this->validate([
            'pinNovo' => ['required', 'digits:4', 'confirmed'],
            'pinNovoConfirmacao' => ['required'],
        ], [
            'pinNovo.required' => 'O novo PIN é obrigatório.',
            'pinNovo.digits' => 'O PIN deve ter exatamente 4 dígitos.',
            'pinNovo.confirmed' => 'A confirmação não coincide.',
        ]);

        $colaborador->pin = $this->pinNovo;
        $colaborador->save();

        $this->reset(['pinAtual', 'pinNovo', 'pinNovoConfirmacao', 'showPinChange']);
        session()->flash('success', 'PIN alterado com sucesso!');
    }
}
