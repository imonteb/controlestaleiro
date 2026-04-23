<?php

namespace App\Livewire\Ferramentas;

use App\Exports\FerramentasExport;
use App\Imports\FerramentasImport;
use App\Models\Ferramenta;
use App\Models\FerramentaLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $filtro_familia = '';

    public $filtro_estado_operacional = '';

    public $filtro_preventivo = '';

    // Import properties
    public $showImport = false;

    public $ficheiroImport;

    public string $importMsg = '';

    public string $importError = '';

    public int $importedCount = 0;

    public int $updatedCount = 0;

    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $importErrorRows = [];

    // Verification modal
    public ?int $verificarId = null;

    public array $verificacaoChecklist = [];

    public bool $verificacaoApto = true;

    public string $verificacaoData = '';

    public string $verificacaoManuTipo = 'Cada Utiliz./Anual';

    public string $verificacaoObservacoes = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function importar(): void
    {
        $this->validate([
            'ficheiroImport' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $this->importMsg = '';
        $this->importError = '';
        $this->importErrorRows = [];

        try {
            $import = new FerramentasImport;
            Excel::import($import, $this->ficheiroImport);

            $this->importedCount = $import->importedCount;
            $this->updatedCount = $import->updatedCount;
            $this->skippedCount = $import->skippedCount;
            $this->importErrorRows = $import->errorRows;

            $this->importMsg = "Concluído: {$this->importedCount} criadas, {$this->updatedCount} actualizadas, {$this->skippedCount} ignoradas.";
            $this->ficheiroImport = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->importError = 'Erro crítico na importação: '.$e->getMessage();
        }
    }

    public function exportar()
    {
        $query = $this->getQuery();

        return Excel::download(new FerramentasExport($query), 'ferramentas_export_'.now()->format('Y-m-d').'.xlsx');
    }

    private function getQuery()
    {
        return Ferramenta::with(['ultimoLog'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('designacao', 'like', '%'.$this->search.'%')
                        ->orWhere('referencia', 'like', '%'.$this->search.'%')
                        ->orWhere('num_serie', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filtro_familia, fn ($q) => $q->where('familia', $this->filtro_familia))
            ->when($this->filtro_estado_operacional, fn ($q) => $q->where('estado_operacional', $this->filtro_estado_operacional))
            ->when($this->filtro_preventivo, function ($q) {
                if ($this->filtro_preventivo === 'ABATIDO') {
                    $q->where('estado_operacional', 'Abate');
                } elseif ($this->filtro_preventivo === 'Sem registo') {
                    $q->whereDoesntHave('logs', fn ($sq) => $sq->whereNotNull('proxima_verificacao'));
                } elseif ($this->filtro_preventivo === 'ATRASADO') {
                    $q->whereHas('ultimoLog', fn ($sq) => $sq->where('proxima_verificacao', '<', now()->format('Y-m-d')));
                } elseif ($this->filtro_preventivo === 'CONFORME') {
                    $q->where('estado_operacional', '!=', 'Abate')
                        ->whereHas('ultimoLog', fn ($sq) => $sq->where('proxima_verificacao', '>=', now()->format('Y-m-d')));
                }
            });
    }

    public function abrirVerificacao(int $id): void
    {
        $this->verificarId = $id;
        $this->verificacaoData = now()->format('Y-m-d');
        $this->verificacaoManuTipo = 'Cada Utiliz./Anual';
        $this->verificacaoObservacoes = '';
        $this->verificacaoChecklist = array_fill_keys(array_keys(FolhaVerificacao::CHECKLIST_ITEMS), 'ok');
        $this->verificacaoApto = true;
    }

    public function fecharVerificacao(): void
    {
        $this->verificarId = null;
    }

    public function setChecklistItem(string $key, string $value): void
    {
        $this->verificacaoChecklist[$key] = $value;
        $this->verificacaoApto = ! in_array('not_ok', $this->verificacaoChecklist);
    }

    public function guardarVerificacao(): void
    {
        $this->validate([
            'verificacaoData' => 'required|date',
            'verificacaoManuTipo' => 'required|string|max:100',
        ]);

        $ferramenta = Ferramenta::findOrFail($this->verificarId);

        $ano = Carbon::parse($this->verificacaoData)->year;

        // Sequencial global (para ordering/agrupamento interno) — exclui importações
        $lastSeq = FerramentaLog::where('tipo_movimento', '!=', 'importacao')->max('num_registo_seq') ?? 0;
        $lastSeq++;
        $folhaId = (int) ceil($lastSeq / 9);

        // Número de registo baseado no ano da verificação (reinicia em Jan) — exclui importações
        $countThisYear = FerramentaLog::where('tipo_movimento', '!=', 'importacao')->whereYear('data_verificacao', $ano)->count();
        $displayFolhaId = (int) ceil(($countThisYear + 1) / 9);
        $numRegisto = 'RV-'.str_pad($displayFolhaId, 3, '0', STR_PAD_LEFT)."/{$ano}";

        $proxima = Carbon::parse($this->verificacaoData)
            ->addMonths($ferramenta->periodicidade_meses ?: 12)
            ->format('Y-m-d');

        $unidade = \DB::table('app_settings')->where('chave', 'empresa_unidade')->value('valor') ?? 'C016';

        $signaturePath = Auth::user()?->signature ?: null;

        FerramentaLog::create([
            'ferramenta_id' => $ferramenta->id,
            'data_verificacao' => $this->verificacaoData,
            'proxima_verificacao' => $proxima,
            'verificado_por' => Auth::user()?->name ?? '',
            'num_registo_verificacao' => $numRegisto,
            'manutencao_tipo' => $this->verificacaoManuTipo,
            'checklist' => $this->verificacaoChecklist,
            'apto' => $this->verificacaoApto,
            'folha_id' => $folhaId,
            'num_registo_seq' => $lastSeq,
            'tipo_movimento' => 'verificacao',
            'observacoes' => $this->verificacaoObservacoes ?: null,
            'unidade' => $unidade,
            'assinatura_path' => $signaturePath,
        ]);

        $this->fecharVerificacao();
        session()->flash('success', "Verificação guardada — Registo I-{$folhaId}/{$ano}");
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->getQuery();
        $ferramentas = $query->paginate(20);

        $familias = Ferramenta::whereNotNull('familia')->distinct()->pluck('familia');

        $ferramentaVerificando = $this->verificarId
            ? Ferramenta::with(['ultimoLog'])->find($this->verificarId)
            : null;

        $opcoesManutencao = FerramentaLog::whereNotNull('manutencao_tipo')
            ->distinct()->orderBy('manutencao_tipo')->pluck('manutencao_tipo')->toArray();
        if (! in_array('Cada Utiliz./Anual', $opcoesManutencao)) {
            array_unshift($opcoesManutencao, 'Cada Utiliz./Anual');
        }

        return view('livewire.ferramentas.index', [
            'ferramentas' => $ferramentas,
            'familias' => $familias,
            'ferramentaVerificando' => $ferramentaVerificando,
            'opcoesManutencao' => $opcoesManutencao,
        ]);
    }
}
