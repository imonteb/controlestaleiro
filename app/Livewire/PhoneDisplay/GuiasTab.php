<?php

namespace App\Livewire\PhoneDisplay;

use App\Models\CtConcelho;
use App\Models\CtDistrito;
use App\Models\GuiaTransporte;
use App\Models\LocalFrequente;
use App\Models\Material;
use App\Models\NominatimCache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class GuiasTab extends Component
{
    #[Reactive]
    public ?int $colaboradorId = null;

    public string $activeGuiaTab = 'solicitar';

    // ── Form fields ────────────────────────────────────────────

    public string $tipo = 'normal';

    public string $matricula = '';

    // Origin
    public string $localCargaNome = 'ESTALEIRO CME';

    public string $localCargaMorada = '';

    public string $localCargaLocalidade = 'CANICAL';

    public string $localCargaCpostal = '';

    public string $dataInicio = '';

    public string $horaInicio = '';

    // Origin UI state
    public string $originTab = 'confirmado';  // 'escolher' | 'confirmado'

    public string $originModo = 'pesquisa';   // 'gps' | 'frequente' | 'pesquisa' | 'manual'

    // Origin cascade
    public string $originDD = '';

    public string $originCC = '';

    public string $originLocalidade = '';

    public string $originArtLocal = '';

    public string $originRua = '';

    // Destination
    public string $destinoNome = '';

    public string $destinoMorada = '';

    public string $destinoLocalidade = '';

    public string $destinoCpostal = '';

    public string $dataFim = '';

    public string $horaFim = '';

    // Destination UI state
    public string $destinoTab = 'escolher';   // 'escolher' | 'confirmado'

    public string $destinoModo = 'pesquisa';  // 'frequente' | 'pesquisa' | 'manual'

    // Destination cascade
    public string $destinoDD = '';

    public string $destinoCC = '';

    // Items
    public array $items = [];

    // State
    public bool $sucesso = false;

    protected function rules(): array
    {
        return [
            'tipo' => 'required|in:normal,global',
            'matricula' => 'required|string|max:20',
            'localCargaNome' => 'required|string|max:255',
            'dataInicio' => 'required|date',
            'horaInicio' => 'required',
            'items.*.descricao' => 'required|string|max:255',
            'items.*.quantidade' => 'required|numeric|min:0.01',
            'items.*.unidade' => 'required|string|max:20',
        ];
    }

    public function mount(): void
    {
        $this->dataInicio = now()->format('Y-m-d');
        $this->horaInicio = now()->format('H:i');
        $this->dataFim = now()->format('Y-m-d');
        $this->horaFim = now()->addHour()->format('H:i');
        $this->items = [['descricao' => '', 'quantidade' => 1, 'unidade' => 'UN']];
    }

    // ── Origin cascade ────────────────────────────────────────

    public function updatedOriginDD(): void
    {
        $this->originCC = '';
    }

    public function updatedDestinDD(): void
    {
        $this->destinoCC = '';
    }

    public function alterarOrigem(): void
    {
        $this->originTab = 'escolher';
        $this->originModo = 'pesquisa';
    }

    public function alterarDestino(): void
    {
        $this->destinoTab = 'escolher';
        $this->destinoModo = 'pesquisa';
    }

    // ── Locais Frequentes ─────────────────────────────────────

    public function searchLocaisFrequentes(string $q): array
    {
        if (strlen($q) < 1) {
            return [];
        }

        return LocalFrequente::activos()
            ->where(function ($query) use ($q) {
                $query->where('nome', 'like', "%{$q}%")
                    ->orWhere('localidade', 'like', "%{$q}%");
            })
            ->orderBy('nome')
            ->limit(6)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'nome' => $l->nome,
                'localidade' => $l->localidade,
                'cp' => $l->codigoPostalCompleto(),
            ])
            ->toArray();
    }

    public function selecionarLocalCarga(int $id): void
    {
        $local = LocalFrequente::findOrFail($id);
        $this->localCargaNome = $local->nome;
        $this->localCargaMorada = $local->morada ?? '';
        $this->localCargaLocalidade = $local->localidade ?? '';
        $this->localCargaCpostal = $local->codigoPostalCompleto();
        $this->originTab = 'confirmado';
    }

    public function selecionarLocalDestino(int $id): void
    {
        $local = LocalFrequente::findOrFail($id);
        $this->destinoNome = $local->nome;
        $this->destinoMorada = $local->morada ?? '';
        $this->destinoLocalidade = $local->localidade ?? '';
        $this->destinoCpostal = $local->codigoPostalCompleto();
        $this->destinoTab = 'confirmado';
    }

    // ── Nominatim search ─────────────────────────────────────

    public function searchRuas(string $q): array
    {
        if (strlen($q) < 2 || ! $this->originDD || ! $this->originCC) {
            return [];
        }

        $cached = NominatimCache::searchLocal($q, $this->originDD, $this->originCC, 10);

        if (! empty($cached)) {
            return $cached;
        }

        $concelhoDesig = CtConcelho::where('dd', $this->originDD)
            ->where('cc', $this->originCC)
            ->value('desig') ?? '';

        return NominatimCache::searchNominatim($q, $this->originDD, $this->originCC, $concelhoDesig);
    }

    public function searchRuasDestino(string $q): array
    {
        if (strlen($q) < 2 || ! $this->destinoDD || ! $this->destinoCC) {
            return [];
        }

        $cached = NominatimCache::searchLocal($q, $this->destinoDD, $this->destinoCC, 10);

        if (! empty($cached)) {
            return $cached;
        }

        $concelhoDesig = CtConcelho::where('dd', $this->destinoDD)
            ->where('cc', $this->destinoCC)
            ->value('desig') ?? '';

        return NominatimCache::searchNominatim($q, $this->destinoDD, $this->destinoCC, $concelhoDesig);
    }

    public function selecionarRuaOrigem(string $road, string $localidade, string $postcode): void
    {
        $this->localCargaNome = $road;
        $this->localCargaMorada = $road;
        $this->localCargaLocalidade = $localidade;
        $this->localCargaCpostal = $postcode;
        $this->originTab = 'confirmado';
    }

    public function selecionarRuaDestino(string $road, string $localidade, string $postcode): void
    {
        $this->destinoNome = $road;
        $this->destinoMorada = $road;
        $this->destinoLocalidade = $localidade;
        $this->destinoCpostal = $postcode;
        $this->destinoTab = 'confirmado';
    }

    // ── Material search ───────────────────────────────────────

    public function searchMateriais(string $q): array
    {
        if (strlen($q) < 2) {
            return [];
        }

        $words = array_filter(explode(' ', mb_strtolower(trim($q))));

        $query = Material::where('activo', true);

        foreach ($words as $word) {
            $query->where(function ($sub) use ($word) {
                $sub->where('nome', 'like', "%{$word}%")
                    ->orWhere('codigo', 'like', "%{$word}%");
            });
        }

        return $query->orderBy('nome')
            ->limit(15)
            ->get()
            ->map(fn ($m) => [
                'codigo' => $m->codigo,
                'nome' => $m->nome,
                'unidade' => $m->unidade_padrao ?? 'UN',
            ])
            ->toArray();
    }

    // ── Items ─────────────────────────────────────────────────

    public function addItem(): void
    {
        $this->items[] = ['descricao' => '', 'quantidade' => 1, 'unidade' => 'UN'];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    // ── Form helpers ──────────────────────────────────────────

    public function limparFormulario(): void
    {
        $this->resetValidation();
        $this->tipo = 'normal';
        $this->matricula = '';
        $this->localCargaNome = 'ESTALEIRO CME';
        $this->localCargaMorada = '';
        $this->localCargaLocalidade = 'CANICAL';
        $this->localCargaCpostal = '';
        $this->dataInicio = now()->format('Y-m-d');
        $this->horaInicio = now()->format('H:i');
        $this->originTab = 'confirmado';
        $this->originModo = 'pesquisa';
        $this->originDD = '';
        $this->originCC = '';
        $this->originLocalidade = '';
        $this->originArtLocal = '';
        $this->originRua = '';
        $this->destinoNome = '';
        $this->destinoMorada = '';
        $this->destinoLocalidade = '';
        $this->destinoCpostal = '';
        $this->destinoTab = 'escolher';
        $this->destinoModo = 'pesquisa';
        $this->destinoDD = '';
        $this->destinoCC = '';
        $this->dataFim = now()->format('Y-m-d');
        $this->horaFim = now()->addHour()->format('H:i');
        $this->items = [['descricao' => '', 'quantidade' => 1, 'unidade' => 'UN']];
        $this->sucesso = false;
    }

    public function repetirGuia(int $id): void
    {
        $guia = GuiaTransporte::with('items')->findOrFail($id);

        $this->tipo = $guia->tipo;
        $this->matricula = $guia->matricula;
        $this->localCargaNome = $guia->local_carga_nome ?? 'ESTALEIRO CME';
        $this->localCargaMorada = $guia->local_carga_morada ?? '';
        $this->localCargaLocalidade = $guia->local_carga_localidade ?? 'CANICAL';
        $this->localCargaCpostal = $guia->local_carga_cpostal ?? '';
        $this->dataInicio = now()->format('Y-m-d');
        $this->horaInicio = now()->format('H:i');
        $this->originTab = 'confirmado';
        $this->originDD = '';
        $this->originCC = '';
        $this->destinoNome = $guia->destino_nome ?? '';
        $this->destinoMorada = $guia->destino_morada ?? '';
        $this->destinoLocalidade = $guia->destino_localidade ?? '';
        $this->destinoCpostal = $guia->destino_cpostal ?? '';
        $this->destinoTab = $this->destinoNome ? 'confirmado' : 'escolher';
        $this->destinoDD = '';
        $this->destinoCC = '';
        $this->dataFim = now()->format('Y-m-d');
        $this->horaFim = now()->addHour()->format('H:i');

        $this->items = $guia->items->map(fn ($i) => [
            'descricao' => $i->descricao,
            'quantidade' => $i->quantidade,
            'unidade' => $i->unidade,
        ])->values()->toArray();

        if (empty($this->items)) {
            $this->items = [['descricao' => '', 'quantidade' => 1, 'unidade' => 'UN']];
        }

        $this->sucesso = false;
        $this->activeGuiaTab = 'solicitar';
        $this->resetValidation();
    }

    // ── Submit ────────────────────────────────────────────────

    public function enviar(): void
    {
        $this->validate();

        DB::transaction(function () {
            $guia = GuiaTransporte::create([
                'origem' => 'colaborador',
                'tipo' => $this->tipo,
                'local_carga_nome' => $this->localCargaNome,
                'local_carga_morada' => $this->localCargaMorada ?: null,
                'local_carga_localidade' => $this->localCargaLocalidade ?: null,
                'local_carga_cpostal' => $this->localCargaCpostal ?: null,
                'data_inicio' => $this->dataInicio,
                'hora_inicio' => $this->horaInicio,
                'matricula' => strtoupper($this->matricula),
                'destino_nome' => $this->destinoNome ?: null,
                'destino_morada' => $this->destinoMorada ?: null,
                'destino_localidade' => $this->destinoLocalidade ?: null,
                'destino_cpostal' => $this->destinoCpostal ?: null,
                'data_fim' => $this->dataFim ?: null,
                'hora_fim' => $this->horaFim ?: null,
                'estado' => 'pendente',
                'requerente_id' => $this->colaboradorId,
            ]);

            foreach ($this->items as $item) {
                $guia->items()->create($item);
            }
        });

        $this->sucesso = true;
        $this->limparFormulario();
        $this->sucesso = true;
        $this->activeGuiaTab = 'historico';
    }

    // ── Render ────────────────────────────────────────────────

    public function render(): mixed
    {
        $minhasGuias = $this->colaboradorId
            ? GuiaTransporte::with('items')
                ->where('requerente_id', $this->colaboradorId)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
            : collect();

        $locaisFrequentes = LocalFrequente::activos()
            ->orderBy('nome')
            ->limit(8)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'nome' => $l->nome,
                'localidade' => $l->localidade ?? '',
                'cp' => $l->codigoPostalCompleto(),
            ])
            ->toArray();

        $distritos = CtDistrito::orderBy('desig')->get();

        $concelhos = $this->originDD
            ? CtConcelho::where('dd', $this->originDD)->orderBy('desig')->get()
            : collect();

        $destinoConcelhos = $this->destinoDD
            ? CtConcelho::where('dd', $this->destinoDD)->orderBy('desig')->get()
            : collect();

        return view('livewire.phone-display.guias-tab', [
            'minhasGuias' => $minhasGuias,
            'locaisFrequentes' => $locaisFrequentes,
            'distritos' => $distritos,
            'concelhos' => $concelhos,
            'destinoConcelhos' => $destinoConcelhos,
        ]);
    }
}
