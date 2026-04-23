<?php

namespace App\Livewire\Epis;

use App\Models\EpiItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('EPI')]
class Form extends Component
{
    public ?EpiItem $epiItem = null;

    public bool $isEdit = false;

    public string $nome = '';

    public string $talla = '';

    public ?string $codigo = '';

    public string $tipo = 'epi';

    public bool $ativo = true;

    public ?string $descricao = '';

    public array $campos_personalizados = [];

    public array $riscos = [];

    public ?string $ca_numero = '';

    public string $unidade = 'unidade';

    public int $stock_minimo = 0;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:100',
            'tipo' => 'required|in:epi,saude,incendio,ferramenta',
            'descricao' => 'nullable|string|max:1000',
            'ca_numero' => 'nullable|string|max:100',
            'unidade' => 'required|string|max:30',
            'stock_minimo' => 'required|integer|min:0',
            'riscos' => 'array',
            'talla' => 'nullable|string|max:50',
        ];
    }

    public function mount(?EpiItem $epiItem = null): void
    {
        if ($epiItem && $epiItem->exists) {
            $this->isEdit = true;
            $this->epiItem = $epiItem;
            $this->nome = $epiItem->nombre;
            $this->talla = $epiItem->talla ?? '';
            $this->codigo = $epiItem->codigo ?? '';
            $tipoMap = ['individual' => 'epi', 'colectivo' => 'epi', 'colectivo_protecao' => 'epi'];
            $this->tipo = $tipoMap[$epiItem->tipo] ?? $epiItem->tipo;
            $this->descricao = $epiItem->descripcion ?? '';
            $this->campos_personalizados = $epiItem->campos_personalizados ?? [];
            $this->riscos = $epiItem->riscos ?? [];
            $this->ca_numero = $epiItem->ca_numero ?? '';
            $this->unidade = $epiItem->unidade ?? 'unidade';
            $this->stock_minimo = $epiItem->stock_minimo;
        }
    }

    public function adicionarCampo(): void
    {
        $this->campos_personalizados[] = [
            'nome' => '',
            'tipo' => 'text',
            'requerido' => false,
        ];
    }

    public function removerCampo(int $index): void
    {
        unset($this->campos_personalizados[$index]);
        $this->campos_personalizados = array_values($this->campos_personalizados);
    }

    public function toggleRisco(string $risco): void
    {
        if (in_array($risco, $this->riscos, true)) {
            $this->riscos = array_values(array_filter($this->riscos, fn ($r) => $r !== $risco));
        } else {
            $this->riscos[] = $risco;
        }
    }

    public function updatedTipo(): void
    {
        if ($this->tipo !== 'epi') {
            $this->riscos = [];
            $this->ca_numero = '';
        }
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'nombre' => $this->nome,
            'talla' => $this->talla ?: null,
            'codigo' => $this->codigo ?: null,
            'tipo' => $this->tipo,
            'descripcion' => $this->descricao ?: null,
            'requiere_talla' => false,
            'tallas_disponibles' => null,
            'campos_personalizados' => (isset($this->campos_personalizados) && count($this->campos_personalizados) > 0) ? $this->campos_personalizados : null,
            'riscos' => count($this->riscos) > 0 ? $this->riscos : null,
            'ca_numero' => $this->ca_numero ?: null,
            'unidade' => $this->unidade,
            'stock_minimo' => $this->stock_minimo,
        ];

        if ($this->isEdit) {
            $this->epiItem->update($data);
        } else {
            EpiItem::create($data);
        }

        return redirect()->route('epis.index');
    }

    public function render()
    {
        return view('livewire.epis.form', [
            'riscosDisponiveis' => EpiItem::RISCOS_DISPONIVEIS,
        ])->title($this->isEdit ? 'Editar EPI' : 'Novo EPI');
    }
}
