<?php

namespace App\Livewire\Epis;

use App\Models\EpiAjuste;
use App\Models\EpiItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Inventário EPI')]
class Inventario extends Component
{
    public array $linhas = [];

    public bool $mostrarHistorico = false;

    public string $search = '';

    public bool $soloConStock = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'soloConStock' => ['except' => false],
    ];

    public function mount(): void
    {
        $this->carregarLinhas();
    }

    public function updatedSearch(): void
    {
        $this->carregarLinhas();
    }

    public function updatedSoloConStock(): void
    {
        $this->carregarLinhas();
    }

    public function carregarLinhas(): void
    {
        $this->linhas = [];
        $query = EpiItem::query();

        if (! empty($this->search)) {
            $s = '%'.$this->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhere('codigo', 'like', $s)
                    ->orWhere('talla', 'like', $s);
            });
        }

        $items = $query->ativos()->orderBy('nombre')->get();

        foreach ($items as $item) {
            $stockSistema = $item->stock_total;

            // Apply 0-stock filter UNLESS searching
            if ($this->soloConStock && $stockSistema <= 0 && empty($this->search)) {
                continue;
            }

            $this->linhas[] = [
                'epi_item_id' => $item->id,
                'nome' => $item->nombre,
                'codigo' => $item->codigo,
                'tamanho' => $item->talla ?: '—',
                'stock_sistema' => $stockSistema,
                'stock_real' => $stockSistema,
                'motivo' => '',
                'diferenca' => 0,
            ];
        }
    }

    public function updatedLinhas($value, $key): void
    {
        // key = "0.stock_real" → extract the index
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'stock_real') {
            $i = (int) $parts[0];
            $stockReal = (int) ($this->linhas[$i]['stock_real'] ?? 0);
            $stockSistema = (int) ($this->linhas[$i]['stock_sistema'] ?? 0);
            $this->linhas[$i]['diferenca'] = $stockReal - $stockSistema;
        }
    }

    public function guardar(): void
    {
        $ajustes = collect($this->linhas)->filter(fn ($l) => (int) $l['diferenca'] !== 0);

        if ($ajustes->isEmpty()) {
            session()->flash('info', 'Não há diferenças para ajustar.');

            return;
        }

        // Validate motivo for each line with difference
        foreach ($ajustes as $i => $l) {
            if (empty(trim($l['motivo']))) {
                $this->addError("linhas.{$i}.motivo", 'Motivo obrigatório quando há diferença.');

                return;
            }
        }

        foreach ($ajustes as $l) {
            EpiAjuste::create([
                'epi_item_id' => $l['epi_item_id'],
                'talla' => $l['tamanho'],
                'stock_sistema' => $l['stock_sistema'],
                'stock_real' => (int) $l['stock_real'],
                'diferencia' => (int) $l['diferenca'],
                'motivo' => trim($l['motivo']),
                'ajustado_por' => Auth::id(),
            ]);
        }

        session()->flash('message', 'Ajustes registados com sucesso ('.$ajustes->count().').');
        $this->carregarLinhas();
    }

    public function render()
    {
        $historial = collect();

        if ($this->mostrarHistorico) {
            // 1. Receções
            $rececoes = \App\Models\EpiRececao::with(['epiItem', 'registradoPor'])
                ->latest('fecha')
                ->limit(30)
                ->get()
                ->map(fn ($r) => [
                    'id' => 'r-'.$r->id,
                    'fecha' => $r->fecha,
                    'tipo' => 'rececao',
                    'item' => $r->epiItem,
                    'talla' => $r->talla,
                    'qtd' => $r->cantidad,
                    'detalhe' => 'Fornecedor: '.($r->proveedor ?: '—'),
                    'user' => $r->registradoPor->name ?? '—',
                    'sort' => $r->fecha->startOfDay()->timestamp + ($r->created_at->timestamp % 86400),
                ]);

            // 2. Entregas
            $entregas = \App\Models\EpiEntrega::with(['epiItem', 'colaborador', 'entregadoPor'])
                ->latest('fecha_entrega')
                ->limit(30)
                ->get()
                ->map(fn ($e) => [
                    'id' => 'e-'.$e->id,
                    'fecha' => $e->fecha_entrega,
                    'tipo' => 'entrega',
                    'item' => $e->epiItem,
                    'talla' => $e->talla,
                    'qtd' => -$e->cantidad,
                    'detalhe' => 'Colab: '.($e->colaborador->nombre ?? 'Desconhecido'),
                    'user' => $e->entregadoPor->name ?? '—',
                    'sort' => $e->fecha_entrega->startOfDay()->timestamp + ($e->created_at->timestamp % 86400),
                ]);

            // 3. Ajustes
            $ajustes = \App\Models\EpiAjuste::with(['epiItem', 'ajustadoPor'])
                ->latest()
                ->limit(30)
                ->get()
                ->map(fn ($a) => [
                    'id' => 'a-'.$a->id,
                    'fecha' => $a->created_at,
                    'tipo' => 'ajuste',
                    'item' => $a->epiItem,
                    'talla' => $a->talla,
                    'qtd' => $a->diferencia,
                    'detalhe' => 'Motivo: '.$a->motivo,
                    'user' => $a->ajustadoPor->name ?? '—',
                    'sort' => $a->created_at->timestamp,
                ]);

            $historial = $rececoes->concat($entregas)->concat($ajustes)
                ->sortByDesc('sort')
                ->take(50);
        }

        return view('livewire.epis.inventario', [
            'historial' => $historial,
        ]);
    }
}
