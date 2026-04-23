<?php

namespace App\Livewire\LocaisFrequentes;

use App\Models\CtCodigoPostal;
use App\Models\CtConcelho;
use App\Models\CtDistrito;
use App\Models\LocalFrequente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Local Frequente')]
class Form extends Component
{
    public ?LocalFrequente $local = null;

    public bool $isEdit = false;

    public string $nome = '';

    public string $tipo = 'portugal';

    public string $morada = '';

    public string $localidade = '';

    public string $cp4 = '';

    public string $cp3 = '';

    public string $cpalf = '';

    public ?int $codigo_postal_id = null;

    public string $pais = 'Portugal';

    public bool $activo = true;

    public string $notas = '';

    // Buscador Portugal
    public string $selectedDd = '';

    public string $selectedCc = '';

    public string $selectedArtLocal = '';

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'tipo' => 'required|in:portugal,internacional',
            'morada' => 'nullable|string|max:255',
            'localidade' => 'nullable|string|max:150',
            'cp4' => 'nullable|string|max:10',
            'cp3' => 'nullable|string|max:3',
            'pais' => 'required|string|max:100',
            'activo' => 'boolean',
            'notas' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
        ];
    }

    public function mount(?LocalFrequente $local = null): void
    {
        if ($local && $local->exists) {
            $this->isEdit = true;
            $this->local = $local;
            $this->nome = $local->nome;
            $this->tipo = $local->tipo;
            $this->morada = $local->morada ?? '';
            $this->localidade = $local->localidade ?? '';
            $this->cp4 = $local->cp4 ?? '';
            $this->cp3 = $local->cp3 ?? '';
            $this->cpalf = $local->cpalf ?? '';
            $this->codigo_postal_id = $local->codigo_postal_id;
            $this->pais = $local->pais ?? 'Portugal';
            $this->activo = $local->activo;
            $this->notas = $local->notas ?? '';
        }
    }

    public function updatedSelectedDd(): void
    {
        $this->selectedCc = '';
        $this->localidade = '';
        $this->cp4 = '';
        $this->cp3 = '';
        $this->cpalf = '';
        $this->codigo_postal_id = null;
    }

    public function updatedSelectedCc(): void
    {
        $this->localidade = '';
        $this->cp4 = '';
        $this->cp3 = '';
        $this->cpalf = '';
        $this->codigo_postal_id = null;
    }

    /** Pesquisa server-side: localidade + art_local + cpalf */
    public function searchZona(string $query): array
    {
        if (strlen(trim($query)) < 2 || ! $this->selectedDd) {
            return [];
        }

        $results = CtCodigoPostal::where('dd', $this->selectedDd)
            ->when($this->selectedCc, fn ($q) => $q->where('cc', $this->selectedCc))
            ->where(function ($q) use ($query) {
                $q->where('localidade', 'like', '%'.$query.'%')
                    ->orWhere('art_local', 'like', '%'.$query.'%')
                    ->orWhere('cpalf', 'like', '%'.$query.'%');
            })
            ->select('id', 'localidade', 'art_local', 'cp4', 'cp3', 'cpalf')
            ->orderByRaw('CASE WHEN art_local IS NOT NULL THEN 0 ELSE 1 END, localidade')
            ->limit(50)
            ->get()
            ->unique(fn ($r) => ($r->art_local ?? $r->localidade).'|'.$r->cp4)
            ->take(10)
            ->values();

        return $results->map(fn ($r) => [
            'id' => $r->id,
            'label' => $r->art_local ? $r->art_local.' — '.$r->localidade : $r->localidade,
            'cp' => $r->cp4.'-'.$r->cp3,
            'cp4' => $r->cp4,
            'cp3' => $r->cp3,
            'cpalf' => $r->cpalf,
            'localidade' => $r->localidade,
        ])->toArray();
    }

    public function selecionarZona(int $id, string $localidade, string $cp4, string $cp3, string $cpalf, string $artLocal): void
    {
        $this->localidade = $localidade;
        $this->cp4 = $cp4;
        $this->cp3 = $cp3;
        $this->cpalf = $cpalf;
        $this->codigo_postal_id = $id;
        $this->selectedArtLocal = $artLocal;
        $this->morada = '';
    }

    public function searchRua(string $query): array
    {
        if (strlen(trim($query)) < 2 || ! $this->selectedDd) {
            return [];
        }

        $results = CtCodigoPostal::where('dd', $this->selectedDd)
            ->when($this->selectedCc, fn ($q) => $q->where('cc', $this->selectedCc))
            ->when($this->selectedArtLocal, fn ($q) => $q->where('art_local', $this->selectedArtLocal))
            ->whereNotNull('nome_arteria')
            ->where('nome_arteria', 'like', '%'.$query.'%')
            ->select('id', 'nome_arteria', 'cp4', 'cp3', 'cpalf', 'localidade', 'art_local')
            ->orderBy('nome_arteria')
            ->limit(50)
            ->get()
            ->unique(fn ($r) => $r->nome_arteria.'|'.$r->cp4.'-'.$r->cp3)
            ->take(10)
            ->values();

        return $results->map(fn ($r) => [
            'id' => $r->id,
            'nome' => $r->nome_arteria,
            'cp' => $r->cp4.'-'.$r->cp3,
            'cp4' => $r->cp4,
            'cp3' => $r->cp3,
            'cpalf' => $r->cpalf,
            'localidade' => $r->localidade,
        ])->toArray();
    }

    public function selecionarRua(int $id, string $nome, string $localidade, string $cp4, string $cp3, string $cpalf): void
    {
        $this->morada = $nome;
        $this->localidade = $localidade;
        $this->cp4 = $cp4;
        $this->cp3 = $cp3;
        $this->cpalf = $cpalf;
        $this->codigo_postal_id = $id;
    }

    public function limparZona(): void
    {
        $this->localidade = '';
        $this->cp4 = '';
        $this->cp3 = '';
        $this->cpalf = '';
        $this->codigo_postal_id = null;
    }

    public function save(): mixed
    {
        $this->validate();

        $data = [
            'nome' => trim($this->nome),
            'tipo' => $this->tipo,
            'morada' => $this->morada ?: null,
            'localidade' => $this->localidade ?: null,
            'cp4' => $this->cp4 ?: null,
            'cp3' => $this->cp3 ?: null,
            'cpalf' => $this->cpalf ?: null,
            'codigo_postal_id' => $this->codigo_postal_id,
            'pais' => $this->pais,
            'activo' => $this->activo,
            'notas' => $this->notas ?: null,
        ];

        if ($this->isEdit) {
            $this->local->update($data);
        } else {
            LocalFrequente::create($data);
        }

        return redirect()->route('locais-frequentes.index');
    }

    public function render(): mixed
    {
        return view('livewire.locais-frequentes.form', [
            'distritos' => CtDistrito::orderBy('desig')->get(),
            'concelhos' => $this->selectedDd
                ? CtConcelho::where('dd', $this->selectedDd)->orderBy('desig')->get()
                : collect(),
        ]);
    }
}
