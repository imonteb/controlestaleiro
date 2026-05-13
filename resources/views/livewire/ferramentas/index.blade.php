<div>
    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-4 right-4 z-50 badge-ok px-5 py-3 rounded-xl shadow-xl text-sm font-bold">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col gap-4">

        {{-- Header CME --}}
        <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
            <div class="bg-[#09143B] px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="wrench" class="text-[#FFD300] w-4 h-4" />
                    <span class="text-white font-medium text-sm">Gestão de Ferramentas e Equipamentos</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button wire:click="exportar" class="btn-cme-ghost text-[11px]">📥 Exportar</button>
                    <button wire:click="$set('showImport', true)" class="btn-cme-ghost text-[11px]">📂 Importar</button>
                    <a href="{{ route('ferramentas.imprimir-folha', 'all') }}" target="_blank" class="btn-cme-ghost text-[11px]">🖨 Imprimir Folhas</a>
                    <a href="{{ route('ferramentas.crear') }}" wire:navigate
                       style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; white-space:nowrap; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none;">
                        + Nova Ferramenta
                    </a>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="cme-card rounded-xl border border-[rgba(9,20,59,0.14)] p-3 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <div>
                    <label class="cme-label">Família</label>
                    <select wire:model.live="filtro_familia"
                        style="background:white; color:#1A1A1A; border:1px solid rgba(9,20,59,0.18); border-radius:6px; padding:6px 10px; font-size:12px; outline:none; min-width:160px;">
                        <option value="">Todas as Famílias</option>
                        @foreach ($familias as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="cme-label">Estado</label>
                    <select wire:model.live="filtro_estado_operacional"
                        style="background:white; color:#1A1A1A; border:1px solid rgba(9,20,59,0.18); border-radius:6px; padding:6px 10px; font-size:12px; outline:none; min-width:140px;">
                        <option value="">Todos os Estados</option>
                        <option value="Apto">Apto</option>
                        <option value="Não Apto">Não Apto</option>
                        <option value="Condicionado">Condicionado</option>
                        <option value="Abate">Abate</option>
                    </select>
                </div>
                <div>
                    <label class="cme-label">Tempo/Verif.</label>
                    <select wire:model.live="filtro_preventivo"
                        style="background:white; color:#1A1A1A; border:1px solid rgba(9,20,59,0.18); border-radius:6px; padding:6px 10px; font-size:12px; outline:none; min-width:140px;">
                        <option value="">Todos</option>
                        <option value="CONFORME">Em dia</option>
                        <option value="ATRASADO">Atrasados</option>
                        <option value="ABATIDO">Abatidos</option>
                        <option value="Sem registo">Sem Registo</option>
                    </select>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4" style="color:#9CA3AF;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Pesquisar designação, ref ou nº série..."
                    class="cme-input" style="padding-left:2.25rem; min-width:260px;">
            </div>
        </div>

        {{-- Tabela --}}
        <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm border-collapse">
                    <thead>
                        <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Código</th>
                            <th class="px-4 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Designação</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Verificação</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Próxima Ver.</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Última Ação</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Quem/Verif.</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Docum.</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Obs.</th>
                            <th class="px-3 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Registo Ver.</th>
                            <th class="px-4 py-3 text-center border-r" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Tempo/Verif.</th>
                            <th class="px-4 py-3 text-center" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ferramentas as $item)
                            @php
                                $zebraBase = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB';
                            @endphp
                            <tr class="hover:bg-[#EEF2FF] transition-colors text-center border-b border-[rgba(9,20,59,0.06)]"
                                style="background:{{ $zebraBase }};">
                                <td class="px-3 py-3 border-r" style="color:#4A4845; font-weight:700; font-size:11px;">{{ $item->referencia ?: '—' }}</td>
                                <td class="px-4 py-3 border-r text-left">
                                    <div style="color:#1A1A1A; font-weight:700; font-size:12px; line-height:1.3;">{{ $item->designacao }}</div>
                                    <div style="color:#7A7775; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-top:2px;">{{ $item->familia }}</div>
                                </td>
                                <td class="px-3 py-3 border-r">
                                    @php
                                        $estadoBadge = match ($item->estado_operacional) {
                                            'Apto'         => 'badge-ok',
                                            'Não Apto'     => 'badge-danger',
                                            'Condicionado' => 'badge-warn',
                                            'Abate'        => 'badge-neutral',
                                            default        => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $estadoBadge }} inline-block">{{ $item->estado_operacional }}</span>
                                </td>
                                <td class="px-3 py-3 border-r" style="color:#4A4845; font-weight:700; font-size:11px; white-space:nowrap;">
                                    {{ $item->ultimoLog?->data_verificacao?->format('d/m/Y') ?: '—' }}
                                </td>
                                @php
                                    $proxData = $item->ultimoLog?->proxima_verificacao;
                                    $proxStyle = 'color:#4A4845;';
                                    if ($proxData) {
                                        if ($proxData->startOfDay()->isPast()) $proxStyle = 'color:#A32D2D; font-weight:700;';
                                        elseif ($proxData->lte(now()->addDays(30))) $proxStyle = 'color:#854F0B; font-weight:700;';
                                    }
                                @endphp
                                <td class="px-3 py-3 border-r" style="{{ $proxStyle }} font-size:11px; white-space:nowrap;">
                                    {{ $proxData?->format('d/m/Y') ?: '—' }}
                                </td>
                                <td class="px-3 py-3 border-r" style="color:#7A7775; font-size:10px; font-weight:700; text-transform:uppercase; line-height:1.3;">
                                    {{ $item->ultimoLog?->manutencao_tipo ?: '—' }}
                                </td>
                                <td class="px-3 py-3 border-r" style="color:#4A4845; font-weight:700; font-size:10px; text-transform:uppercase;">
                                    {{ $item->ultimoLog?->verificado_por ?: '—' }}
                                </td>
                                <td class="px-3 py-3 border-r">
                                    <span class="badge-neutral inline-block">{{ $item->tipo_documentacao ?: 'Manual' }}</span>
                                </td>
                                <td class="px-3 py-3 border-r max-w-[120px] truncate" style="color:#7A7775; font-size:10px; font-style:italic;"
                                    title="{{ $item->ultimoLog?->conclusao }}">
                                    {{ $item->ultimoLog?->conclusao ?: '—' }}
                                </td>
                                <td class="px-3 py-3 border-r text-center">
                                    @if ($item->ultimoLog?->num_registo_verificacao)
                                        <a href="{{ route('ferramentas.log', $item) }}" wire:navigate
                                            class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-[#E4E2DF] transition-colors"
                                            style="border:1px solid rgba(9,20,59,0.14);">
                                            <div class="h-1.5 w-1.5 rounded-full" style="background:#09143B;"></div>
                                            <span style="font-size:10px; color:#09143B; font-weight:700; font-family:monospace; text-transform:uppercase;">{{ $item->ultimoLog->num_registo_verificacao }}</span>
                                        </a>
                                    @else
                                        <span style="color:#D0CECC;">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center border-r">
                                    @php
                                        $status = $item->status_preventivo;
                                        $isAtrasado = str_contains($status, 'ATRASADO');
                                        $isAbatido  = str_contains($status, 'ABATIDO');
                                        $statusBadge = $isAtrasado ? 'badge-danger' : ($isAbatido ? 'badge-neutral' : 'badge-ok');
                                    @endphp
                                    <span class="{{ $statusBadge }} inline-block">{{ $status }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('ferramentas.log', $item) }}" wire:navigate
                                            class="p-1.5 rounded-lg hover:bg-[#dbeafe] transition-all"
                                            style="color:#1e40af;" title="Registo de Verificação">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('ferramentas.editar', $item) }}" wire:navigate
                                            class="p-1.5 rounded-lg hover:bg-[#fef9c3] transition-all"
                                            style="color:#854F0B;" title="Editar Ferramenta">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($item->estado_operacional !== 'Abate')
                                        <button wire:click="abrirVerificacao({{ $item->id }})"
                                            class="p-1.5 rounded-lg hover:bg-[#d4ede4] transition-all"
                                            style="color:#0F6E56;" title="Verificar Ferramenta">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-14 w-14 rounded-full flex items-center justify-center" style="background:#E4E2DF;">
                                            <svg class="h-7 w-7" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div class="cme-muted font-bold uppercase tracking-widest text-xs">Sem ferramentas registadas</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($ferramentas->hasPages())
                <div style="border-top:1px solid rgba(9,20,59,0.08); padding:12px 16px;">
                    {{ $ferramentas->links() }}
                </div>
            @endif
        </div>

        {{-- ── MODAL VERIFICAÇÃO ─────────────────────────────────── --}}
        @if($verificarId && $ferramentaVerificando)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data>
            <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm" wire:click="fecharVerificacao"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-gray-900 rounded-2xl shadow-2xl border border-white/10 overflow-hidden">
                    <div class="bg-emerald-900/50 border-b border-emerald-700/30 px-6 py-4 flex items-start justify-between gap-3">
                        <div>
                            <div class="text-emerald-400 text-[10px] font-black uppercase tracking-widest">IT.014.I.01(2) — Verificação</div>
                            <div class="text-white font-black text-lg leading-tight mt-0.5">{{ $ferramentaVerificando->designacao }}</div>
                            <div class="flex items-center gap-3 mt-1 text-xs text-white/50">
                                @if($ferramentaVerificando->referencia)
                                <span class="font-mono">{{ $ferramentaVerificando->referencia }}</span>
                                @endif
                                @if($ferramentaVerificando->num_serie)
                                <span>Nº Série: <span class="font-mono text-white/70">{{ $ferramentaVerificando->num_serie }}</span></span>
                                @endif
                                @if($ferramentaVerificando->familia)
                                <span class="bg-white/10 px-2 py-0.5 rounded text-white/60">{{ $ferramentaVerificando->familia }}</span>
                                @endif
                            </div>
                        </div>
                        <button wire:click="fecharVerificacao" class="text-white/30 hover:text-white/70 flex-shrink-0 mt-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-white/40 uppercase font-bold">Data</span>
                                <input wire:model="verificacaoData" type="date"
                                       class="bg-white/5 border border-white/15 text-white text-xs rounded-lg px-2.5 py-1.5 font-bold focus:outline-none focus:border-emerald-500/60" />
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-white/40 uppercase font-bold">Manutenção</span>
                                <input wire:model="verificacaoManuTipo" type="text"
                                       list="opcoes-manutencao-modal"
                                       class="bg-white/5 border border-white/15 text-white text-xs rounded-lg px-2.5 py-1.5 w-44 focus:outline-none focus:border-emerald-500/60"
                                       placeholder="Cada Utiliz./Anual" />
                                <datalist id="opcoes-manutencao-modal">
                                    @foreach($opcoesManutencao as $op)
                                    <option value="{{ $op }}">
                                    @endforeach
                                </datalist>
                            </div>
                            @if($ferramentaVerificando->ultimoLog?->proxima_verificacao)
                            @php $dias = now()->diffInDays($ferramentaVerificando->ultimoLog->proxima_verificacao, false); @endphp
                            <div class="flex items-center">
                                <span class="text-xs font-black px-3 py-1 rounded-lg
                                    {{ $dias < 0 ? 'bg-red-500/20 text-red-300' : ($dias < 30 ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/15 text-emerald-400') }}">
                                    {{ $dias < 0 ? abs($dias).' dias ATRASADO' : $dias.' dias p/vencer' }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <div class="text-[10px] font-black text-white/30 uppercase tracking-widest">Critérios de Verificação</div>
                            @foreach(\App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_ITEMS as $key => $label)
                            @php $val = $verificacaoChecklist[$key] ?? 'ok'; @endphp
                            <div class="flex items-center gap-3 py-2 border-b border-white/5">
                                <div class="flex-1 text-xs text-white/80 font-semibold leading-tight">{{ $label }}</div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button wire:click="setChecklistItem('{{ $key }}', 'ok')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'ok' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-emerald-500/20 hover:text-emerald-400' }}">OK</button>
                                    <button wire:click="setChecklistItem('{{ $key }}', 'not_ok')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'not_ok' ? 'bg-red-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-red-500/20 hover:text-red-400' }}">NOK</button>
                                    @if($key === 'contacto_eletrico')
                                    <button wire:click="setChecklistItem('{{ $key }}', 'na')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'na' ? 'bg-gray-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-gray-500/20 hover:text-gray-400' }}">NA</button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-white/40 uppercase font-bold">Resultado</span>
                                <span class="px-3 py-1.5 rounded-lg text-xs font-black
                                             {{ $verificacaoApto ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                    {{ $verificacaoApto ? 'APTO' : 'NÃO APTO' }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <textarea wire:model="verificacaoObservacoes" rows="2"
                                          placeholder="Observações (opcional)..."
                                          class="w-full bg-white/5 border border-white/10 text-white/80 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:border-emerald-500/40"></textarea>
                            </div>
                        </div>
                        @error('verificacaoData') <div class="text-red-400 text-xs">{{ $message }}</div> @enderror
                        <div class="flex justify-end gap-3 pt-2 border-t border-white/8">
                            <button wire:click="fecharVerificacao" class="px-5 py-2.5 text-xs font-bold text-white/50 hover:text-white/80 transition-colors">Cancelar</button>
                            <button wire:click="guardarVerificacao" wire:loading.attr="disabled"
                                    class="bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white text-xs font-black px-6 py-2.5 rounded-xl transition-all shadow-lg">
                                <span wire:loading.remove wire:target="guardarVerificacao">✓ Guardar Verificação</span>
                                <span wire:loading wire:target="guardarVerificacao">A guardar...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Importação --}}
        <div x-data="{ open: @entangle('showImport') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-blue-950/60 backdrop-blur-sm transition-opacity" x-show="open"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                    x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="px-6 py-4 flex items-center justify-between" style="background:#09143B !important;">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg" style="background:rgba(255,211,0,0.15);">
                                <svg class="h-5 w-5 text-[#FFD300]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Ferramentas</span>
                        </div>
                        <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 flex flex-col gap-6" style="background:#F0EEEB;">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas Necessárias no Excel</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach([['Obrigatório','red','referencia'],['Obrigatório','red','designacao'],['Opcional','gray','marca'],['Opcional','gray','modelo'],['Opcional','gray','num_serie'],['Opcional','gray','periodicidade'],['Opcional','gray','documentacao'],['Histórico','blue','data_verificacao'],['Histórico','blue','registo'],['Opcional','gray','familia']] as [$tipo,$cor,$campo])
                                <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                    <div class="text-[9px] font-black text-{{ $cor }}-500 uppercase">{{ $tipo }}</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">{{ $campo }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($importError)
                            <div class="rounded-xl px-4 py-3 text-sm font-bold bg-red-50 border border-red-200 text-red-800">{{ $importError }}</div>
                        @endif
                        @if ($importMsg)
                            <div class="rounded-xl px-4 py-3 text-sm font-bold bg-green-50 border border-green-200 text-green-800">{{ $importMsg }}</div>
                            @if (count($importErrorRows) > 0)
                            <div class="rounded-xl px-4 py-3 bg-amber-50 border border-amber-200">
                                <div class="text-xs font-black text-amber-800 uppercase mb-2">{{ count($importErrorRows) }} linha(s) com erro:</div>
                                <ul class="space-y-1">
                                    @foreach ($importErrorRows as $err)
                                    <li class="text-xs text-amber-700">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endif
                        <div class="space-y-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Selecionar Ficheiro (.xlsx, .xls, .csv)</label>
                                <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                                    class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.14)] rounded-xl bg-white">
                                @error('ficheiroImport') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center justify-between gap-4 pt-4 border-t border-[rgba(9,20,59,0.08)]">
                                <a href="{{ route('ferramentas.plantilla') }}" style="color:#09143B;" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-opacity hover:opacity-75">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Modelo Excel
                                </a>
                                <button wire:click="importar" wire:loading.attr="disabled"
                                    class="btn-cme-primary inline-flex items-center gap-2 disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="importar" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    <svg wire:loading wire:target="importar" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" /></svg>
                                    <span wire:loading.remove wire:target="importar">Importar Dados</span>
                                    <span wire:loading wire:target="importar">A processar...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
