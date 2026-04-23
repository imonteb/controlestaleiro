<?php

namespace App\Livewire\Ferramentas;

use App\Models\Ferramenta;
use App\Models\FerramentaLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Registo de Verificação')]
class Log extends Component
{
    use \App\Traits\HasMobileSignature;

    public Ferramenta $ferramenta;

    // New Log Fields
    public $data_verificacao;

    public $proxima_verificacao;

    public $verificado_por;

    public $num_registo_verificacao;

    public $manutencao_tipo = 'Anual';

    public $conclusao = 'Conforme';

    public $tipo_movimento = 'verificacao';

    public $unidade = 'Estaleiro';

    public $assinatura;

    public $observacoes;

    // Checklist Fields
    public $checklist = [];

    public $apto = true;

    public ?int $editingLogId = null;

    // Mesmas chaves que FolhaVerificacao::CHECKLIST_ITEMS para compatibilidade com o PDF
    public const CHECKLIST_ITEMS = [
        'estabilidade_rotura',
        'contacto_mecanico',
        'contacto_eletrico',
        'projecoes',
        'manutencao',
        'acessorios_cargas',
        'maq_nao_automotores',
    ];

    public function mount(Ferramenta $ferramenta)
    {
        $this->ferramenta = $ferramenta;
        $this->data_verificacao = now()->format('Y-m-d');
        $this->proxima_verificacao = now()->addMonths($ferramenta->periodicidade_meses ?: 12)->format('Y-m-d');
        $this->verificado_por = auth()->user()?->name ?? 'Sistema';

        // Suggest next global number
        $suggestion = $this->suggestNextRegistoNumber();
        $this->num_registo_verificacao = $suggestion['full'];

        // Initialize checklist as all OK
        foreach (self::CHECKLIST_ITEMS as $item) {
            $this->checklist[$item] = 'ok';
        }
    }

    public function updatedChecklist(): void
    {
        $this->apto = ! collect($this->checklist)->contains('not_ok');
    }

    public function updatedDataVerificacao($value)
    {
        if ($value) {
            try {
                $date = \Carbon\Carbon::parse($value);
                $this->proxima_verificacao = $date->addMonths($this->ferramenta->periodicidade_meses ?: 12)->format('Y-m-d');
            } catch (\Exception) {
                // Silently fail if date is invalid
            }
        }
    }

    private function suggestNextRegistoNumber(): array
    {
        $year = now()->year;

        // Sequencial global para ordering — exclui importações
        $nextSeq = (FerramentaLog::where('tipo_movimento', '!=', 'importacao')->max('num_registo_seq') ?? 0) + 1;
        $folhaId = (int) ceil($nextSeq / 9);

        // Número de registo reinicia por ano — exclui importações
        $countThisYear = FerramentaLog::where('tipo_movimento', '!=', 'importacao')->whereYear('data_verificacao', $year)->count();
        $displayFolhaId = (int) ceil(($countThisYear + 1) / 9);

        return [
            'seq' => $nextSeq,
            'full' => 'RV-'.str_pad($displayFolhaId, 3, '0', STR_PAD_LEFT)."/{$year}",
            'folha_id' => $folhaId,
        ];
    }

    public function editLog(int $logId): void
    {
        $lastId = FerramentaLog::where('tipo_movimento', '!=', 'importacao')
            ->orderBy('num_registo_seq', 'desc')
            ->value('id');

        if ($logId !== $lastId) {
            return;
        }

        $log = FerramentaLog::findOrFail($logId);
        $this->editingLogId = $log->id;
        $this->data_verificacao = $log->data_verificacao->format('Y-m-d');
        $this->proxima_verificacao = $log->proxima_verificacao?->format('Y-m-d');
        $this->verificado_por = $log->verificado_por;
        $this->num_registo_verificacao = $log->num_registo_verificacao;
        $this->manutencao_tipo = $log->manutencao_tipo ?? 'Anual';
        $this->conclusao = $log->conclusao ?? 'Conforme';
        $this->tipo_movimento = $log->tipo_movimento;
        $this->unidade = $log->unidade;
        $this->checklist = $log->checklist ?? [];
        $this->apto = $log->apto;
        $this->observacoes = $log->observacoes;

        $this->dispatch('open-drawer');
    }

    public function deleteLastLog()
    {
        $lastLog = FerramentaLog::where('tipo_movimento', '!=', 'importacao')
            ->orderBy('num_registo_seq', 'desc')
            ->first();

        if ($lastLog) {
            $lastLog->delete();
        }

        session()->flash('success', 'Registo eliminado.');

        return $this->redirect(route('ferramentas.log', $this->ferramenta->id), navigate: true);
    }

    public function save()
    {
        $this->validate([
            'data_verificacao' => 'required|date',
            'proxima_verificacao' => 'nullable|date',
            'verificado_por' => 'required|string',
            'num_registo_verificacao' => 'nullable|string',
            'tipo_movimento' => 'required',
        ]);

        if ($this->editingLogId) {
            FerramentaLog::findOrFail($this->editingLogId)->update([
                'data_verificacao' => $this->data_verificacao,
                'proxima_verificacao' => $this->proxima_verificacao,
                'verificado_por' => $this->verificado_por,
                'num_registo_verificacao' => $this->num_registo_verificacao,
                'manutencao_tipo' => $this->manutencao_tipo,
                'conclusao' => $this->conclusao,
                'tipo_movimento' => $this->tipo_movimento,
                'checklist' => $this->checklist,
                'apto' => $this->apto,
                'unidade' => $this->unidade,
                'observacoes' => $this->observacoes,
            ]);

            session()->flash('success', 'Registo actualizado.');
        } else {
            $signaturePath = auth()->user()?->signature ?: null;
            $suggestion = $this->suggestNextRegistoNumber();

            FerramentaLog::create([
                'ferramenta_id' => $this->ferramenta->id,
                'data_verificacao' => $this->data_verificacao,
                'proxima_verificacao' => $this->proxima_verificacao,
                'verificado_por' => $this->verificado_por,
                'num_registo_verificacao' => $this->num_registo_verificacao,
                'manutencao_tipo' => $this->manutencao_tipo,
                'conclusao' => $this->conclusao,
                'assinatura_path' => $signaturePath,
                'tipo_movimento' => $this->tipo_movimento,
                'checklist' => $this->checklist,
                'folha_id' => $suggestion['folha_id'],
                'apto' => $this->apto,
                'num_registo_seq' => $suggestion['seq'],
                'unidade' => $this->unidade,
                'observacoes' => $this->observacoes,
            ]);

            session()->flash('success', 'Registo adicionado com sucesso!');
        }

        return $this->redirect(route('ferramentas.log', $this->ferramenta->id), navigate: true);
    }

    public function onSignatureCompleted($request)
    {
        $this->assinatura = $request->signature_data;
        $this->dispatch('signature-ready', signature: $request->signature_data);
    }

    public function render()
    {
        // Show global history to reflect the "Book" structure
        $logs = FerramentaLog::with('ferramenta')
            ->where('tipo_movimento', '!=', 'importacao')
            ->orderBy('num_registo_seq', 'desc')
            ->take(60)
            ->get();

        $lastLogId = FerramentaLog::where('tipo_movimento', '!=', 'importacao')
            ->orderBy('num_registo_seq', 'desc')
            ->value('id');

        return view('livewire.ferramentas.log', [
            'logs' => $logs,
            'lastLogId' => $lastLogId,
        ]);
    }
}
