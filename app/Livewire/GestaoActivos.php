<?php

namespace App\Livewire;

use App\Models\AutoSocorroKit;
use App\Models\Extintor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GestaoActivos extends Component
{
    use WithPagination;

    public $search = '';

    public $type = 'all';

    public $statusFilter = 'all';

    public function generateToken($id, $itemType)
    {
        $token = bin2hex(random_bytes(8));
        if ($itemType === 'extintor') {
            Extintor::where('id', $id)->update(['qr_code_token' => $token]);
        } else {
            AutoSocorroKit::where('id', $id)->update(['qr_code_token' => $token]);
        }
        session()->flash('success', 'Token QR gerado com sucesso!');
    }

    public function toggleRestock($id, $itemType)
    {
        if ($itemType === 'extintor') {
            $item = Extintor::findOrFail($id);
            $item->update(['needs_restock' => ! $item->needs_restock]);
        } else {
            $item = AutoSocorroKit::findOrFail($id);
            $item->update(['needs_restock' => ! $item->needs_restock]);
        }
    }

    public function render()
    {
        $extintores = Extintor::with(['veiculo', 'localizacao'])->get()->map(function ($e) {
            return [
                'id' => $e->id,
                'type' => 'extintor',
                'name' => $e->num_serie ?: 'S/N',
                'description' => $e->tipo_agente.' '.$e->tamanho,
                'location' => $e->veiculo ? '🚗 '.$e->veiculo->matricula : ($e->localizacao ? '📍 '.$e->localizacao->nombre : 'Estaleiro'),
                'expiry' => $e->proxima_revisao,
                'status' => $this->getExtintorStatus($e),
                'needs_restock' => $e->needs_restock,
                'token' => $e->qr_code_token,
            ];
        });

        $kits = AutoSocorroKit::with(['veiculo', 'localizacao', 'itens'])->get()->map(function ($k) {
            $minExpiry = $k->itens->min('data_validade');

            return [
                'id' => $k->id,
                'type' => 'kit',
                'name' => $k->identificador_kit ?: $k->designacao,
                'description' => $k->itens->count().' itens',
                'location' => $k->veiculo ? '🚗 '.$k->veiculo->matricula : ($k->localizacao ? '📍 '.$k->localizacao->nombre : 'Estaleiro'),
                'expiry' => $minExpiry,
                'status' => $this->getKitStatus($k),
                'needs_restock' => $k->needs_restock,
                'token' => $k->qr_code_token,
            ];
        });

        $allAssets = $extintores->concat($kits)
            ->when($this->type !== 'all', fn ($c) => $c->filter(fn ($i) => $i['type'] === $this->type))
            ->when($this->statusFilter !== 'all', fn ($c) => $c->filter(fn ($i) => $i['status'] === $this->statusFilter))
            ->when($this->search, function ($c) {
                $s = strtolower($this->search);

                return $c->filter(fn ($i) => str_contains(strtolower($i['name']), $s) || str_contains(strtolower($i['location']), $s));
            })->sortBy('expiry');

        return view('livewire.gestao-activos', [
            'assets' => $allAssets,
        ]);
    }

    private function getExtintorStatus($e)
    {
        if ($e->proxima_revisao && $e->proxima_revisao->isPast()) {
            return 'expired';
        }
        if ($e->proxima_revisao && $e->proxima_revisao->diffInDays(now()) < 30) {
            return 'warning';
        }

        return 'ok';
    }

    private function getKitStatus($k)
    {
        $minExpiry = $k->itens->min('data_validade');
        if ($minExpiry && $minExpiry->isPast()) {
            return 'expired';
        }
        if ($minExpiry && $minExpiry->diffInDays(now()) < 30) {
            return 'warning';
        }

        return 'ok';
    }
}
