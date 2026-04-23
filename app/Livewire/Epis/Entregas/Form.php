<?php

namespace App\Livewire\Epis\Entregas;

use App\Exceptions\StockInsuficienteException;
use App\Models\Colaborador;
use App\Models\EpiEntrega;
use App\Models\EpiItem;
use App\Services\EpiStockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Entrega EPI')]
class Form extends Component
{
    public ?int $colaborador_id = null;

    public array $entregas = [
        [
            'key' => '',
            'epi_item_id' => null,
            'cantidad' => 1,
            'talla' => null,
            'valores_personalizados' => [],
            'fecha_entrega' => '',
            'observaciones' => '',
            'selectedItem' => null,
            'stockDisponible' => '',
        ],
    ];

    protected function rules(): array
    {
        $rules = [
            'colaborador_id' => 'required|exists:colaboradores,id',
        ];
        foreach ($this->entregas as $i => $entrega) {
            $rules["entregas.$i.epi_item_id"] = 'required|exists:epi_items,id';
            $rules["entregas.$i.cantidad"] = 'required|integer|min:1';
            $rules["entregas.$i.talla"] = 'nullable|string|max:50';
            $rules["entregas.$i.fecha_entrega"] = 'required|date';
            $rules["entregas.$i.observaciones"] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->entregas[0]['fecha_entrega'] = now()->format('Y-m-d');
        $this->entregas[0]['key'] = uniqid('entrega_', true);
    }

    public function updated($property, $value): void
    {
        // Detecta si es epi_item_id o talla de alguna entrega
        if (preg_match('/entregas\\.(\\d+)\\.epi_item_id/', $property, $m)) {
            $i = (int) $m[1];
            $item = $value ? EpiItem::find($value) : null;
            $this->entregas[$i]['selectedItem'] = $item;
            $this->entregas[$i]['talla'] = null;
            $this->entregas[$i]['valores_personalizados'] = [];
            if ($item && $item->campos_personalizados) {
                foreach ($item->campos_personalizados as $campo) {
                    $this->entregas[$i]['valores_personalizados'][$campo['nome'] ?? $campo['nombre'] ?? ''] = '';
                }
            }
            $this->actualizarStock($i);
        }
        if (preg_match('/entregas\\.(\\d+)\\.talla/', $property, $m)) {
            $i = (int) $m[1];
            $this->actualizarStock($i);
        }
    }

    private function actualizarStock($i): void
    {
        $item = $this->entregas[$i]['selectedItem'];
        $talla = $this->entregas[$i]['talla'];
        if ($item) {
            if ($item->requiere_talla && $talla) {
                $stockPorTalla = $item->stock_por_talla ?? [];
                $stock = (int) ($stockPorTalla[$talla] ?? 0);
            } else {
                $stock = $item->stockAtual();
            }
            $uni = $item->unidade ?? 'unidade';
            $this->entregas[$i]['stockDisponible'] = "Stock disponível: {$stock} {$uni}";
        } else {
            $this->entregas[$i]['stockDisponible'] = '';
        }
    }

    public function addEntrega()
    {
        $this->entregas[] = [
            'key' => uniqid('entrega_', true),
            'epi_item_id' => null,
            'cantidad' => 1,
            'talla' => null,
            'valores_personalizados' => [],
            'fecha_entrega' => now()->format('Y-m-d'),
            'observaciones' => '',
            'selectedItem' => null,
            'stockDisponible' => '',
        ];
    }

    public function removeEntrega($key)
    {
        if (count($this->entregas) > 1) {
            $index = collect($this->entregas)->search(fn ($e) => $e['key'] === $key);
            if ($index !== false) {
                array_splice($this->entregas, $index, 1);
            }
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $entregaIds = [];

        try {
            DB::transaction(function () use (&$entregaIds) {
                $stockService = app(EpiStockService::class);

                foreach ($this->entregas as $i => $entrega) {
                    $item = EpiItem::lockForUpdate()->findOrFail($entrega['epi_item_id']);

                    $stockService->validateStock($item, $entrega['cantidad']);

                    $created = EpiEntrega::create([
                        'epi_item_id' => $entrega['epi_item_id'],
                        'colaborador_id' => $this->colaborador_id,
                        'cantidad' => $entrega['cantidad'],
                        'talla' => $entrega['talla'] ?: null,
                        'valores_personalizados' => $entrega['valores_personalizados'] ?: null,
                        'fecha_entrega' => $entrega['fecha_entrega'],
                        'estado' => 'entregue',
                        'observaciones' => $entrega['observaciones'] ?: null,
                        'entregado_por' => Auth::id(),
                    ]);

                    $entregaIds[] = $created->id;
                }
            });
        } catch (StockInsuficienteException $e) {
            $this->addError('entregas', $e->getMessage());

            return null;
        }

        $this->dispatch('abrir-modal-entrega',
            entregaIds: $entregaIds,
            colaboradorId: $this->colaborador_id,
            redirectUrl: route('epis.entregas.index'),
        );

        return null;
    }

    public function render()
    {
        return view('livewire.epis.entregas.form', [
            'epiItems' => EpiItem::ativos()->orderBy('nombre')->get(),
            'colaboradores' => Colaborador::ativos()->orderBy('nombre')->get(),
        ]);
    }
}
