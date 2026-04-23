<?php

namespace App\Livewire;

use App\Models\AppNotification;
use App\Services\WebPushService;
use Livewire\Component;

class GestaoNotificacoes extends Component
{
    public $tipo = 'geral';

    public $titulo = '';

    public $mensagem = '';

    public $data_expiracao;

    protected $rules = [
        'tipo' => 'required|in:clima,rrhh,seguranca,geral',
        'titulo' => 'required|string|max:100',
        'mensagem' => 'required|string|max:1000',
        'data_expiracao' => 'nullable|date|after:now',
    ];

    public function mount()
    {
        $this->data_expiracao = now()->addDays(1)->format('Y-m-d\TH:i');
    }

    public function criarNotificacao()
    {
        $this->validate();

        AppNotification::create([
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'mensagem' => $this->mensagem,
            'data_expiracao' => $this->data_expiracao,
            'activa' => true,
        ]);

        $icons = [
            'clima' => '🌦️',
            'rrhh' => '👥',
            'seguranca' => '🔒',
            'geral' => '📢',
        ];

        app(WebPushService::class)->broadcast([
            'title' => ($icons[$this->tipo] ?? '📢').' '.$this->titulo,
            'body' => $this->mensagem,
            'tag' => 'notif-'.$this->tipo,
            'url' => '/phone',
            'requireInteraction' => false,
        ]);

        $this->reset(['titulo', 'mensagem']);
        $this->data_expiracao = now()->addDays(1)->format('Y-m-d\TH:i');

        session()->flash('success', 'Notificação enviada com sucesso para a PWA!');
    }

    public function desactivar($id)
    {
        $notificacao = AppNotification::find($id);
        if ($notificacao) {
            $notificacao->update(['activa' => false]);
            session()->flash('success', 'Notificação desativada.');
        }
    }

    public function apagar($id)
    {
        $notificacao = AppNotification::find($id);
        if ($notificacao) {
            $notificacao->delete();
            session()->flash('success', 'Notificação eliminada.');
        }
    }

    public function render()
    {
        $notificacoesActivas = AppNotification::where('activa', true)
            ->where(function ($q) {
                $q->whereNull('data_expiracao')
                    ->orWhere('data_expiracao', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $notificacoesInactivas = AppNotification::where('activa', false)
            ->orWhere('data_expiracao', '<=', now())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('livewire.gestao-notificacoes', [
            'notificacoesActivas' => $notificacoesActivas,
            'notificacoesInactivas' => $notificacoesInactivas,
        ])->layout('layouts.app');
    }
}
