<?php

namespace App\Livewire\PhoneDisplay;

use App\Jobs\NominatimLookupJob;
use App\Models\CtCodigoPostal;
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

    public string $originModo = 'gps';        // 'gps' | 'frequente' | 'pesquisa' | 'manual'

    // Origin CT cascade
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
        $this->originLocalidade = '';
        $this->originRua = '';
    }

    public function updatedOriginCC(): void
    {
        $this->originLocalidade = '';
        $this->originRua = '';
    }

    public function updatedOriginLocalidade(): void
    {
        $this->originArtLocal = '';
        $this->originRua = '';
    }

    public function updatedOriginArtLocal(): void
    {
        $this->originRua = '';
    }

    public function aplicarPesquisaOrigem(): void
    {
        if (! $this->originLocalidade) {
            return;
        }

        $query = CtCodigoPostal::where('dd', $this->originDD)
            ->where('cc', $this->originCC)
            ->where('localidade', $this->originLocalidade);

        if ($this->originArtLocal) {
            $query->where('art_local', $this->originArtLocal);
        }

        if ($this->originRua) {
            $query->where('nome_arteria', $this->originRua);
        }

        $row = $query->first();

        $this->localCargaMorada = $this->originRua ?: '';
        $this->localCargaLocalidade = $this->originLocalidade;
        $this->localCargaCpostal = $row ? "{$row->cp4}-{$row->cp3}" : '';

        if (! $this->localCargaNome || $this->localCargaNome === 'ESTALEIRO CME') {
            $this->localCargaNome = $this->originRua ?: $this->originLocalidade;
        }

        $this->originTab = 'confirmado';
    }

    // ── Localidade search for destination (CT) ────────────────

    public function searchLocalidades(string $q): array
    {
        if (strlen($q) < 2) {
            return [];
        }

        return CtCodigoPostal::select('localidade', DB::raw('MIN(cp4) as cp4'), DB::raw('MIN(cp3) as cp3'))
            ->where('localidade', 'like', "{$q}%")
            ->groupBy('localidade')
            ->orderBy('localidade')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'localidade' => $r->localidade,
                'cp' => "{$r->cp4}-{$r->cp3}",
            ])
            ->toArray();
    }

    public function selecionarLocalidade(string $localidade, string $cp): void
    {
        $this->destinoLocalidade = $localidade;
        $this->destinoCpostal = $cp;
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
    }

    // ── Rua search ───────────────────────────────────────────

    public function searchRuas(string $q): array
    {
        if (strlen($q) < 2 || ! $this->originDD || ! $this->originCC) {
            return [];
        }

        // 1. Buscar en el caché local (Nominatim ya consultado)
        $cached = NominatimCache::searchLocal($q, $this->originDD, $this->originCC, 15);

        if (! empty($cached)) {
            return array_values(array_unique(array_filter(array_column($cached, 'road'))));
        }

        // 2. Fallback a CT database (datos locales)
        $normalized = preg_replace('/\b(de|da|do|das|dos|e|à|ao|a)\b/iu', ' ', $q);
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));
        $words = array_filter(explode(' ', $normalized));

        $query = CtCodigoPostal::where('dd', $this->originDD)
            ->where('cc', $this->originCC)
            ->when($this->originLocalidade, fn ($q2) => $q2->where('localidade', $this->originLocalidade))
            ->when($this->originArtLocal, fn ($q2) => $q2->where(fn ($q3) => $q3->where('art_local', $this->originArtLocal)->orWhereNull('art_local')))
            ->whereNotNull('nome_arteria')
            ->where('nome_arteria', '!=', '');

        foreach ($words as $word) {
            $query->where('nome_arteria', 'like', "%{$word}%");
        }

        $ctResults = $query->select('nome_arteria')
            ->distinct()
            ->orderBy('nome_arteria')
            ->limit(30)
            ->pluck('nome_arteria')
            ->toArray();

        // 3. Encolar búsqueda en Nominatim en background para futuras consultas
        if ($this->originDD && $this->originCC) {
            $concelhoDesig = CtConcelho::where('dd', $this->originDD)
                ->where('cc', $this->originCC)
                ->value('desig') ?? '';

            NominatimLookupJob::dispatch($q, $this->originDD, $this->originCC, $concelhoDesig);
        }

        return $ctResults;
    }

    // ── Material search ───────────────────────────────────────

    public function searchMateriais(string $q): array
    {
        if (strlen($q) < 2) {
            return [];
        }

        return Material::where('activo', true)
            ->where(function ($query) use ($q) {
                $query->where('nome', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%");
            })
            ->orderBy('nome')
            ->limit(8)
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
        $this->originModo = 'gps';
        $this->originDD = '';
        $this->originCC = '';
        $this->originLocalidade = '';
        $this->originArtLocal = '';
        $this->originRua = '';
        $this->destinoNome = '';
        $this->destinoMorada = '';
        $this->destinoLocalidade = '';
        $this->destinoCpostal = '';
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
        $this->destinoNome = $guia->destino_nome ?? '';
        $this->destinoMorada = $guia->destino_morada ?? '';
        $this->destinoLocalidade = $guia->destino_localidade ?? '';
        $this->destinoCpostal = $guia->destino_cpostal ?? '';
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

        $localidades = $this->originCC
            ? CtCodigoPostal::where('dd', $this->originDD)
                ->where('cc', $this->originCC)
                ->select('localidade')
                ->groupBy('localidade')
                ->orderBy('localidade')
                ->get()
                ->pluck('localidade')
            : collect();

        $artLocals = $this->originLocalidade
            ? CtCodigoPostal::where('dd', $this->originDD)
                ->where('cc', $this->originCC)
                ->where('localidade', $this->originLocalidade)
                ->whereNotNull('art_local')
                ->where('art_local', '!=', '')
                ->select('art_local')
                ->distinct()
                ->orderBy('art_local')
                ->pluck('art_local')
            : collect();

        return view('livewire.phone-display.guias-tab', [
            'minhasGuias' => $minhasGuias,
            'locaisFrequentes' => $locaisFrequentes,
            'distritos' => $distritos,
            'concelhos' => $concelhos,
            'localidades' => $localidades,
            'artLocals' => $artLocals,
        ]);
    }
}
