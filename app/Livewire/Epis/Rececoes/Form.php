<?php

namespace App\Livewire\Epis\Rececoes;

use App\Models\EpiItem;
use App\Models\EpiRececao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Recepção EPI')]
class Form extends Component
{
    public ?int $epi_item_id = null;

    public int $quantidade = 1;

    public ?string $talla = null;

    public string $data = '';

    public ?string $fornecedor = '';

    public ?string $numero_fatura = '';

    public ?string $observacoes = '';

    // Removed selectedItem back-reference

    #[\Livewire\Attributes\Computed]
    public function epiItems()
    {
        return EpiItem::ativos()->orderBy('nombre')->get();
    }

    protected function rules(): array
    {
        return [
            'epi_item_id' => 'required|exists:epi_items,id',
            'quantidade' => 'required|integer|min:1',
            'talla' => 'nullable|string|max:50',
            'data' => 'required|date',
            'fornecedor' => 'nullable|string|max:255',
            'numero_fatura' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(): void
    {
        $this->data = now()->format('Y-m-d');
    }

    public function updatedEpiItemId($value): void
    {
        $item = $value ? $this->epiItems->firstWhere('id', $value) : null;
        $this->talla = $item?->talla ?: null;
    }

    public function save(): mixed
    {
        $this->validate();

        EpiRececao::create([
            'epi_item_id' => $this->epi_item_id,
            'cantidad' => $this->quantidade,
            'talla' => $this->talla ?: null,
            'fecha' => $this->data,
            'proveedor' => $this->fornecedor ?: null,
            'numero_factura' => $this->numero_fatura ?: null,
            'observaciones' => $this->observacoes ?: null,
            'registrado_por' => Auth::id(),
        ]);

        return redirect()->route('epis.rececoes.index');
    }

    public function render()
    {
        return view('livewire.epis.rececoes.form', [
            'epiItems' => $this->epiItems,
        ]);
    }
}
