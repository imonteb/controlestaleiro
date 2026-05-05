<div>
    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="fixed top-4 right-4 z-50 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-bold">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col gap-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold uppercase text-yellow-500">Gestão de Ferramentas e Equipamentos</h1>
                <p class="text-sm text-white/80 mt-0.5 font-medium italic">Gestão de EPIs, Saúde, Incêndio e Ferramentas</p>
            </div>
            <div class="flex items-center gap-3 self-end md:self-auto uppercase">
                <button wire:click="exportar"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm transition-colors shadow-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
                <button wire:click="$set('showImport', true)"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white font-semibold text-sm transition-colors shadow-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importar
                </button>
                <a href="{{ route('ferramentas.imprimir-folha', 'all') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition-colors shadow-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Imprimir Folhas
                </a>
                <a href="{{ route('ferramentas.crear') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-white/90 font-semibold text-sm transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Ferramenta
                </a>
            </div>
        </div>
        <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; width:100%;">
            {{-- Filter --}}
            <div class="flex-none">
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-white/8 rounded-xl border border-white/10 p-1">
                        <select wire:model.live="filtro_familia"
                            class="w-44 border-none bg-transparent text-[11px] font-bold uppercase tracking-wider text-white/80 focus:ring-0 cursor-pointer">
                            <option value="">Famílias</option>
                            @foreach ($familias as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <select wire:model.live="filtro_estado_operacional"
                            class="w-44 border-none bg-transparent text-[11px] font-bold uppercase tracking-wider text-white/80 focus:ring-0 cursor-pointer">
                            <option value="">Estados</option>
                            <option value="Apto">Apto</option>
                            <option value="Não Apto">Não Apto</option>
                            <option value="Condicionado">Condicionado</option>
                            <option value="Abate">Abate</option>
                        </select>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <select wire:model.live="filtro_preventivo"
                            class="w-44 border-none bg-transparent text-[11px] font-bold uppercase tracking-wider text-white/80 focus:ring-0 cursor-pointer">
                            <option value="">Tempo/Verif.</option>
                            <option value="CONFORME">Em dia</option>
                            <option value="ATRASADO">Atrasados</option>
                            <option value="ABATIDO">Abatidos</option>
                            <option value="Sem registo">Sem Registo</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Search Bar (Right Aligned) --}}
            <div class="w-full md:w-1/3">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Pesquisar designação, ref ou nº série..." style="padding-left: 3.25rem;"
                        class="block w-full pr-3 py-2 rounded-lg bg-blue-900/50 border border-blue-700 text-white placeholder-blue-400 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 lg:shadow-sm">
                </div>
            </div>
        </div>
        {{-- Tools Table --}}
        <div class="bg-white/0 rounded-xl border border-white/8 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm border-collapse">
                    <thead>
                        <tr class="bg-white/0 border-b border-white/8">
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Código</th>
                            <th
                                class="px-4 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Designação</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Estado</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Verificação</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Próxima Ver.</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Última Ação</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Quem/Verif.</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Docum.</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Obs.</th>
                            <th
                                class="px-3 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] border-r text-center">
                                Registo Ver.</th>
                            <th
                                class="px-4 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] text-center border-r">
                                Tempo/Verif.</th>
                            <th class="px-4 py-4 font-bold text-white/60 uppercase tracking-wider text-[10px] text-center">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-white/80">
                        @forelse($ferramentas as $item)
                            <tr
                                class="odd:bg-white/0 even:bg-white/3 hover:bg-yellow-400/5 transition-colors border-b border-white/5 text-center">
                                <td class="px-3 py-4 font-bold text-white/80 border-r">{{ $item->referencia ?: '—' }}
                                </td>
                                <td class="px-4 py-4 border-r text-left">
                                    <div class="font-bold text-white/90 leading-tight">{{ $item->designacao }}</div>
                                    <div class="text-[9px] uppercase text-white/55 font-bold mt-1 tracking-tight">
                                        {{ $item->familia }}</div>
                                </td>
                                <td class="px-3 py-4 border-r">
                                    @php
                                        $color = match ($item->estado_operacional) {
                                            'Apto' => 'bg-emerald-50 text-emerald-700 border-emerald-100 ring-emerald-500/10',
                                            'Não Apto' => 'bg-rose-50 text-rose-700 border-rose-100 ring-rose-500/10',
                                            'Condicionado' => 'bg-amber-50 text-amber-700 border-amber-100 ring-amber-500/10',
                                            'Abate' => 'bg-slate-800 text-slate-100 border-slate-700 ring-slate-500/10',
                                            default => 'bg-gray-50 text-white/65 border-gray-100',
                                        };
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border ring-2 ring-offset-0 {{ $color }}">
                                        {{ $item->estado_operacional }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 border-r text-white/70 font-bold whitespace-nowrap text-[11px]">
                                    {{ $item->ultimoLog?->data_verificacao?->format('d/m/Y') ?: '—' }}
                                </td>
                                @php
                                    $proxData = $item->ultimoLog?->proxima_verificacao;
                                    $proxColor = 'text-white/70';
                                    if ($proxData) {
                                        if ($proxData->startOfDay()->isPast()) $proxColor = 'text-red-400';
                                        elseif ($proxData->lte(now()->addDays(30))) $proxColor = 'text-yellow-400';
                                    }
                                @endphp
                                <td class="px-3 py-4 border-r font-bold whitespace-nowrap text-[11px] {{ $proxColor }}">
                                    {{ $proxData?->format('d/m/Y') ?: '—' }}
                                </td>
                                <td
                                    class="px-3 py-4 border-r text-white/60 text-[10px] font-bold leading-tight uppercase">
                                    {{ $item->ultimoLog?->manutencao_tipo ?: '—' }}
                                </td>
                                <td class="px-3 py-4 border-r text-white/70 font-bold text-[10px] uppercase">
                                    {{ $item->ultimoLog?->verificado_por ?: '—' }}
                                </td>
                                <td class="px-3 py-4 border-r">
                                    <span
                                        class="px-2 py-0.5 bg-white/8 text-white/70 border border-white/10 rounded text-[9px] font-bold uppercase">{{ $item->tipo_documentacao ?: 'Manual' }}</span>
                                </td>
                                <td class="px-3 py-4 border-r text-white/50 text-[10px] font-medium max-w-[120px] truncate italic"
                                    title="{{ $item->ultimoLog?->conclusao }}">
                                    {{ $item->ultimoLog?->conclusao ?: '—' }}
                                </td>
                                <td class="px-3 py-4 border-r text-center">
                                    @if ($item->ultimoLog?->num_registo_verificacao)
                                        <a href="{{ route('ferramentas.log', $item) }}" wire:navigate
                                            class="inline-flex items-center gap-1.5 px-2 py-1 bg-blue-50/50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors group">
                                            <div class="h-1.5 w-1.5 rounded-full bg-blue-500 group-hover:bg-blue-600"></div>
                                            <span
                                                class="font-mono text-[10px] text-white/75 font-black tracking-tight uppercase">{{ $item->ultimoLog->num_registo_verificacao }}</span>
                                        </a>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center border-r">
                                    @php
                                        $status = $item->status_preventivo;
                                        $isAtrasado = str_contains($status, 'ATRASADO');
                                        $isAbatido = str_contains($status, 'ABATIDO');
                                        $statusColor = $isAtrasado
                                            ? 'bg-rose-100/50 text-rose-700 border-rose-200'
                                            : ($isAbatido
                                                ? 'bg-slate-800 text-white border-slate-700'
                                                : 'bg-emerald-100/50 text-emerald-700 border-emerald-200');
                                    @endphp
                                    <div
                                        class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border backdrop-blur-sm shadow-sm {{ $statusColor }}">
                                        {{ $status }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('ferramentas.log', $item) }}" wire:navigate
                                            class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                            title="Registo de Verificación">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('ferramentas.editar', $item) }}" wire:navigate
                                            class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-500 hover:text-white transition-all shadow-sm"
                                            title="Editar Ferramenta">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($item->estado_operacional !== 'Abate')
                                        <button wire:click="abrirVerificacao({{ $item->id }})"
                                            class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-500 hover:text-white transition-all shadow-sm"
                                            title="Verificar Ferramenta">
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
                                <td colspan="12" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="h-16 w-16 rounded-full bg-white/8 flex items-center justify-center text-white/55">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div class="text-white/65 font-bold uppercase tracking-widest text-xs">Sem
                                            ferramentas registadas</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($ferramentas->hasPages())
                <div class="px-4 py-3 border-t border-white/5
                            [&_nav]:text-white/50
                            [&_a]:bg-white/5 [&_a]:border-white/10 [&_a]:text-white/70
                            [&_a:hover]:bg-yellow-400/10 [&_a:hover]:text-yellow-400
                            [&_[aria-current]>span]:bg-yellow-400
                            [&_[aria-current]>span]:text-blue-950
                            [&_[aria-current]>span]:border-yellow-400">
                    {{ $ferramentas->links() }}
                </div>
            @endif
        </div>

        {{-- ── MODAL VERIFICAÇÃO ─────────────────────────────────── --}}
        @if($verificarId && $ferramentaVerificando)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data>
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm" wire:click="fecharVerificacao"></div>

            {{-- Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-gray-900 rounded-2xl shadow-2xl border border-white/10 overflow-hidden">

                    {{-- Header --}}
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

                        {{-- Linha de controlo --}}
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
                            @php
                                $dias = now()->diffInDays($ferramentaVerificando->ultimoLog->proxima_verificacao, false);
                            @endphp
                            <div class="flex items-center">
                                <span class="text-xs font-black px-3 py-1 rounded-lg
                                    {{ $dias < 0 ? 'bg-red-500/20 text-red-300' : ($dias < 30 ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/15 text-emerald-400') }}">
                                    {{ $dias < 0 ? abs($dias).' dias ATRASADO' : $dias.' dias p/vencer' }}
                                </span>
                            </div>
                            @endif
                        </div>

                        {{-- Checklist --}}
                        <div class="space-y-2">
                            <div class="text-[10px] font-black text-white/30 uppercase tracking-widest">Critérios de Verificação</div>
                            @foreach(\App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_ITEMS as $key => $label)
                            @php $val = $verificacaoChecklist[$key] ?? 'ok'; @endphp
                            <div class="flex items-center gap-3 py-2 border-b border-white/5">
                                <div class="flex-1 text-xs text-white/80 font-semibold leading-tight">{{ $label }}</div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button wire:click="setChecklistItem('{{ $key }}', 'ok')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'ok' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-emerald-500/20 hover:text-emerald-400' }}">
                                        OK
                                    </button>
                                    <button wire:click="setChecklistItem('{{ $key }}', 'not_ok')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'not_ok' ? 'bg-red-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-red-500/20 hover:text-red-400' }}">
                                        NOK
                                    </button>
                                    @if($key === 'contacto_eletrico')
                                    <button wire:click="setChecklistItem('{{ $key }}', 'na')"
                                            class="px-3 py-1 rounded-lg text-xs font-black transition-all
                                                   {{ $val === 'na' ? 'bg-gray-500 text-white shadow-sm' : 'bg-white/5 text-white/25 hover:bg-gray-500/20 hover:text-gray-400' }}">
                                        NA
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Resultado + Obs --}}
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

                        {{-- Footer --}}
                        <div class="flex justify-end gap-3 pt-2 border-t border-white/8">
                            <button wire:click="fecharVerificacao"
                                    class="px-5 py-2.5 text-xs font-bold text-white/50 hover:text-white/80 transition-colors">
                                Cancelar
                            </button>
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

        {{-- Modal Importación --}}
        <div x-data="{ open: @entangle('showImport') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-blue-950/60 backdrop-blur-sm transition-opacity" x-show="open"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                    x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="bg-green-800 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 p-2 rounded-lg">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar
                                Ferramentas</span>
                        </div>
                        <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 flex flex-col gap-6">
                        {{-- Instucciones --}}
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas
                                Necessárias no Excel</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">referencia</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">designacao</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">marca</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">modelo</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">num_serie</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">periodicidade</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">documentacao</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-blue-500 uppercase">Histórico</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">data_verificacao</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-blue-500 uppercase">Histórico</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">registo</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">familia</div>
                                </div>
                            </div>
                        </div>

                        @if ($importError)
                            <div class="rounded-xl px-4 py-3 text-sm font-bold bg-red-50 border border-red-200 text-red-800">
                                {{ $importError }}
                            </div>
                        @endif

                        @if ($importMsg)
                            <div class="rounded-xl px-4 py-3 text-sm font-bold bg-green-50 border border-green-200 text-green-800">
                                {{ $importMsg }}
                            </div>
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
                                <label
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Selecionar
                                    Ficheiro (.xlsx, .xls, .csv)</label>
                                <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                                    class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-green-100 file:text-green-800 hover:file:bg-green-200 cursor-pointer text-gray-900 border border-gray-100 rounded-xl bg-gray-50/30">
                                @error('ficheiroImport')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('ferramentas.plantilla') }}"
                                    class="inline-flex items-center gap-2 text-xs text-green-700 hover:text-green-900 font-bold uppercase tracking-wider transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Modelo Excel
                                </a>
                                <button wire:click="importar" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:opacity-50 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-green-900/20 text-xs uppercase tracking-widest">
                                    <svg wire:loading.remove wire:target="importar" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg wire:loading wire:target="importar" class="h-4 w-4 animate-spin"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v8H4z" />
                                    </svg>
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
