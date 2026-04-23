<?php

namespace App\Livewire\Ferramentas;

use App\Models\Ferramenta;
use App\Models\FerramentaLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Folha de Verificação')]
class FolhaVerificacao extends Component
{
    public const MAX_LINHAS = 9;

    public const CHECKLIST_ITEMS = [
        'estabilidade_rotura' => 'Estabilidade e Rotura',
        'contacto_mecanico' => 'Riscos Contacto Mecânico',
        'contacto_eletrico' => 'Riscos Contacto Elétrico, Explosão, Incêndio',
        'projecoes' => 'Projeções, Iluminação, Temperatura e Sinalização',
        'manutencao' => 'Manutenção',
        'acessorios_cargas' => 'Específico Acessórios Mov. e Fix. Cargas',
        'maq_nao_automotores' => 'Específico Máq. e Eq. Não Automotores',
    ];

    public const CHECKLIST_TOOLTIPS = [
        'estabilidade_rotura' => "• O equipamento e os respetivos elementos estão estabilizados por fixação ou por outros meios sempre que a segurança ou a saúde dos trabalhadores o justifique?\n• Foram tomadas medidas adequadas de proteção face ao risco de estilhaçamento ou de rotura de elementos do equipamento suscetíveis de pôr em perigo a segurança ou a saúde dos trabalhadores?",
        'contacto_mecanico' => "• O equipamento está provido de dispositivos de proteção que impeçam o acesso às zonas perigosas ou contacto com os elementos móveis?\n• Os protetores são de construção robusta, não ocasionam riscos suplementares e estão situados a distância suficiente da zona perigosa?",
        'contacto_eletrico' => '• O equipamento está concebido de modo que os trabalhadores sejam protegidos contra: Contacto direto ou indireto com a eletricidade? Incêndio, sobreaquecimento ou libertação de gases? Explosão do equipamento?',
        'projecoes' => "• O equipamento está provido de dispositivos de segurança face a quedas ou projeções de objetos?\n• Dispositivos de retenção/extração face a emanações de gases, vapores ou poeiras?\n• As zonas de trabalho estão convenientemente iluminadas?\n• As partes com temperaturas elevadas/muito baixas dispõem de proteção de contacto?",
        'manutencao' => "• As operações de manutenção podem efetuar-se com o equipamento parado?\n• O livrete de manutenção está atualizado?\n• Os trabalhadores têm acesso a todos os locais necessários em segurança?\n• O equipamento está devidamente sinalizado?",
        'acessorios_cargas' => "Cabos, Cintas, Estropos, Ganchos, Roldanas, Porta-paletes, Pull-Lift, Tirfor…\n• Os equipamentos de elevação mantêm solidez e estabilidade durante a utilização?\n• Ostentam indicação visível da carga nominal?\n• Os acessórios de elevação estão marcados com as características essenciais?",
        'maq_nao_automotores' => "Berbequins, Rebarbadoras, Bicicletas de cabos, Compressores, Bombas, Vibradores…\n• Os sistemas de comando com incidência na segurança são claramente visíveis e identificáveis?\n• O equipamento está provido de sistema de paragem geral em condições de segurança?\n• Os dispositivos de alerta podem ser ouvidos e compreendidos facilmente?",
    ];

    // Cabeçalho da folha
    public string $empresaNome = '';

    public string $empresaUnidade = '';

    public string $dataVerificacao;

    public string $verificadoPor = '';

    public string $manutencaoTipo = 'Cada Utiliz./Anual';

    public string $observacoes = '';

    // 9 linhas da tabela
    public array $linhas = [];

    // Painel lateral
    public ?int $painelFerramentaId = null;

    // Autocomplete options (loaded from DB)
    public array $opcoesManutencao = [];

    public array $opcoesDocumento = [];

    public array $opcoesFamilia = [];

    public array $opcoesLocalizacao = [];

    public function mount(): void
    {
        $this->dataVerificacao = now()->format('Y-m-d');
        $this->verificadoPor = auth()->user()->name ?? '';

        // Carregar configurações da empresa
        $settings = \DB::table('app_settings')->pluck('valor', 'chave');
        $this->empresaNome = $settings['empresa_nome'] ?? 'CME';
        $this->empresaUnidade = $settings['empresa_unidade'] ?? 'C016';

        // Inicializar 9 linhas vazias
        $this->linhas = collect(range(1, self::MAX_LINHAS))->map(fn () => [
            'ferramenta_id' => null,
            'checklist' => array_fill_keys(array_keys(self::CHECKLIST_ITEMS), 'ok'),
            'apto' => true,
        ])->toArray();

        $this->carregarOpcoes();
    }

    private function carregarOpcoes(): void
    {
        $this->opcoesManutencao = FerramentaLog::whereNotNull('manutencao_tipo')
            ->distinct()->orderBy('manutencao_tipo')->pluck('manutencao_tipo')->toArray();

        if (! in_array('Cada Utiliz./Anual', $this->opcoesManutencao)) {
            array_unshift($this->opcoesManutencao, 'Cada Utiliz./Anual');
        }

        $this->opcoesDocumento = Ferramenta::whereNotNull('tipo_documentacao')
            ->distinct()->orderBy('tipo_documentacao')->pluck('tipo_documentacao')->toArray();

        if (! in_array('Manual', $this->opcoesDocumento)) {
            array_unshift($this->opcoesDocumento, 'Manual');
        }

        $this->opcoesFamilia = Ferramenta::whereNotNull('familia')
            ->distinct()->orderBy('familia')->pluck('familia')->toArray();

        $this->opcoesLocalizacao = Ferramenta::whereNotNull('localizacao')
            ->distinct()->orderBy('localizacao')->pluck('localizacao')->toArray();
    }

    public function updatedLinhas($value, $key): void
    {
        // key format: "N.checklist.item"
        $parts = explode('.', $key);
        if (count($parts) === 3 && $parts[1] === 'checklist') {
            $idx = (int) $parts[0];
            $hasNok = in_array('not_ok', $this->linhas[$idx]['checklist'] ?? []);
            $this->linhas[$idx]['apto'] = ! $hasNok;
        }
    }

    public function abrirPainel(int $ferramentaId): void
    {
        $this->painelFerramentaId = ($this->painelFerramentaId === $ferramentaId) ? null : $ferramentaId;
    }

    public function fecharPainel(): void
    {
        $this->painelFerramentaId = null;
    }

    public function submeter(): void
    {
        $linhasPreenchidas = collect($this->linhas)->filter(fn ($l) => ! empty($l['ferramenta_id']));

        if ($linhasPreenchidas->isEmpty()) {
            $this->addError('geral', 'Adicione pelo menos uma ferramenta à folha.');

            return;
        }

        $lastSeq = FerramentaLog::where('tipo_movimento', '!=', 'importacao')->max('num_registo_seq') ?? 0;
        $folhaId = (int) ceil(($lastSeq + 1) / self::MAX_LINHAS);
        $ano = now()->year;

        foreach ($linhasPreenchidas as $linha) {
            $lastSeq++;
            $ferramenta = Ferramenta::find($linha['ferramenta_id']);
            $proxima = \Carbon\Carbon::parse($this->dataVerificacao)
                ->addMonths($ferramenta->periodicidade_meses ?: 12)
                ->format('Y-m-d');

            FerramentaLog::create([
                'ferramenta_id' => $linha['ferramenta_id'],
                'data_verificacao' => $this->dataVerificacao,
                'proxima_verificacao' => $proxima,
                'verificado_por' => $this->verificadoPor,
                'num_registo_verificacao' => 'RV-'.str_pad($folhaId, 3, '0', STR_PAD_LEFT)."/{$ano}",
                'manutencao_tipo' => $this->manutencaoTipo,
                'checklist' => $linha['checklist'],
                'apto' => $linha['apto'],
                'folha_id' => $folhaId,
                'num_registo_seq' => $lastSeq,
                'tipo_movimento' => 'verificacao',
                'observacoes' => $this->observacoes ?: null,
                'unidade' => $this->empresaUnidade,
            ]);
        }

        session()->flash('success', "Folha I-{$folhaId}/{$ano} guardada com {$linhasPreenchidas->count()} ferramenta(s).");

        $this->redirect(route('ferramentas.index'), navigate: true);
    }

    public function render(): mixed
    {
        $ferramentaIds = collect($this->linhas)->pluck('ferramenta_id')->filter()->unique()->values();
        $ferramentasNaFolha = Ferramenta::whereIn('id', $ferramentaIds)->get()->keyBy('id');

        $painelDados = null;
        if ($this->painelFerramentaId) {
            $painelDados = Ferramenta::with(['logs' => fn ($q) => $q->orderBy('data_verificacao', 'desc')->limit(5)])
                ->find($this->painelFerramentaId);
        }

        $ferramentasActivas = Ferramenta::where('activo', true)->orderBy('designacao')->get();

        return view('livewire.ferramentas.folha-verificacao', [
            'ferramentasNaFolha' => $ferramentasNaFolha,
            'ferramentasActivas' => $ferramentasActivas,
            'painelDados' => $painelDados,
        ]);
    }
}
