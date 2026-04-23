<div class="min-h-screen bg-gray-950 text-white">

    {{-- Flash --}}
    @if(session('success'))
    <div class="fixed top-4 right-4 z-50 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-bold">
        {{ session('success') }}
    </div>
    @endif

    {{-- ── CABEÇALHO STICKY ──────────────────────────────────── --}}
    <div class="sticky top-0 z-30 bg-gray-900 border-b border-white/10 px-6 py-3">
        <div class="max-w-[1400px] mx-auto flex flex-wrap items-center gap-4">

            {{-- Título --}}
            <div class="flex-shrink-0">
                <div class="text-[10px] font-black text-white/30 uppercase tracking-widest">IT.014.I.01(2)</div>
                <div class="text-sm font-black text-white">Relatório de Verificação</div>
            </div>

            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

            {{-- Empreitada/Unidade (configurável) --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-white/40 uppercase font-bold">Empreitada</span>
                <span class="bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 text-xs font-black px-2.5 py-1 rounded-lg">
                    {{ $this->empresaNome }} {{ $this->empresaUnidade }}
                </span>
            </div>

            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

            {{-- Data --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-white/40 uppercase font-bold">Data</span>
                <input wire:model="dataVerificacao" type="date"
                       class="bg-white/5 border border-white/15 text-white text-xs rounded-lg px-2.5 py-1.5 font-bold" />
            </div>

            {{-- Verificado por --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-white/40 uppercase font-bold">Verificado por</span>
                <span class="text-xs text-white/70 font-semibold">{{ $this->verificadoPor }}</span>
            </div>

            {{-- Manutenção --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-white/40 uppercase font-bold">Manutenção</span>
                <input wire:model="manutencaoTipo" type="text"
                       list="opcoes-manutencao"
                       class="bg-white/5 border border-white/15 text-white text-xs rounded-lg px-2.5 py-1.5 w-44"
                       placeholder="Cada Utiliz./Anual" />
                <datalist id="opcoes-manutencao">
                    @foreach($this->opcoesManutencao as $op)
                    <option value="{{ $op }}">
                    @endforeach
                </datalist>
            </div>

            {{-- Submeter --}}
            <div class="ml-auto">
                <button wire:click="submeter" wire:loading.attr="disabled"
                        class="bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white text-xs font-black px-5 py-2.5 rounded-xl transition-all shadow-lg">
                    <span wire:loading.remove wire:target="submeter">✓ Guardar Folha</span>
                    <span wire:loading wire:target="submeter">A guardar...</span>
                </button>
            </div>
        </div>
        @error('geral')
        <div class="max-w-[1400px] mx-auto mt-2 text-red-400 text-xs font-bold">{{ $message }}</div>
        @enderror
    </div>

    {{-- ── CONTEÚDO PRINCIPAL ────────────────────────────────── --}}
    <div class="flex max-w-[1400px] mx-auto px-4 py-4 gap-4">

        {{-- TABELA --}}
        <div class="flex-1 overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-yellow-500/20 border-b-2 border-yellow-500/50">
                        <th class="px-3 py-2.5 text-left font-black text-yellow-300 w-8">#</th>
                        <th class="px-3 py-2.5 text-left font-black text-yellow-300 min-w-[200px]">Designação</th>
                        <th class="px-3 py-2.5 text-left font-black text-yellow-300 w-28">Nº Série / Inv.</th>

                        @foreach(\App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_ITEMS as $key => $label)
                        <th class="px-2 py-2.5 text-center font-black text-yellow-300 w-20 group relative cursor-help"
                            title="{{ \App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_TOOLTIPS[$key] }}">
                            <span class="block truncate max-w-[72px] mx-auto leading-tight" style="font-size:0.6rem;">
                                {{ Str::limit($label, 18) }}
                            </span>
                            {{-- Tooltip --}}
                            <div class="absolute left-1/2 -translate-x-1/2 top-full mt-1 z-50 w-72 bg-gray-800 border border-yellow-500/30 rounded-xl p-3 text-left text-white/80 font-normal leading-relaxed shadow-2xl hidden group-hover:block whitespace-pre-line" style="font-size:0.65rem;">
                                <div class="font-black text-yellow-300 mb-1" style="font-size:0.7rem;">{{ $label }}</div>
                                {{ \App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_TOOLTIPS[$key] }}
                            </div>
                        </th>
                        @endforeach

                        <th class="px-3 py-2.5 text-center font-black text-yellow-300 w-20">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($linhas as $i => $linha)
                    @php
                        $fid  = $linha['ferramenta_id'];
                        $tool = $fid ? ($ferramentasNaFolha[$fid] ?? null) : null;
                        $odd  = $i % 2 === 0;
                    @endphp
                    <tr class="border-b border-white/5 {{ $odd ? 'bg-white/2' : 'bg-transparent' }} hover:bg-white/5 transition-colors">

                        {{-- Número linha --}}
                        <td class="px-3 py-2 text-white/25 font-black">{{ $i + 1 }}</td>

                        {{-- Designação — select pesquisável --}}
                        <td class="px-2 py-1.5">
                            <div class="flex items-center gap-1.5">
                                @if($tool)
                                <button wire:click="abrirPainel({{ $tool->id }})"
                                        class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/20 transition-colors"
                                        title="Ver detalhes">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </button>
                                @endif
                                <select wire:model.live="linhas.{{ $i }}.ferramenta_id"
                                        class="flex-1 bg-white/5 border border-white/10 text-white rounded-lg px-2 py-1.5 text-xs focus:border-indigo-500/60 focus:outline-none">
                                    <option value="">— selecionar —</option>
                                    @foreach($ferramentasActivas as $f)
                                    <option value="{{ $f->id }}" {{ $fid == $f->id ? 'selected' : '' }}>
                                        {{ $f->designacao }}{{ $f->referencia ? ' ('.$f->referencia.')' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        {{-- Nº Série --}}
                        <td class="px-3 py-2 text-white/50 font-mono text-[11px]">
                            {{ $tool?->num_serie ?? '—' }}
                        </td>

                        {{-- Critérios OK/NOK (e NA para elétrico) --}}
                        @foreach(array_keys(\App\Livewire\Ferramentas\FolhaVerificacao::CHECKLIST_ITEMS) as $key)
                        <td class="px-1 py-1.5 text-center">
                            @if($tool)
                            <div class="flex items-center justify-center gap-0.5">
                                {{-- OK --}}
                                <button wire:click="$set('linhas.{{ $i }}.checklist.{{ $key }}', 'ok')"
                                        class="px-1.5 py-0.5 rounded text-[10px] font-black transition-all
                                               {{ ($linha['checklist'][$key] ?? 'ok') === 'ok'
                                                  ? 'bg-emerald-500 text-white shadow-sm'
                                                  : 'bg-white/5 text-white/25 hover:bg-emerald-500/20 hover:text-emerald-400' }}">
                                    OK
                                </button>
                                {{-- NOK --}}
                                <button wire:click="$set('linhas.{{ $i }}.checklist.{{ $key }}', 'not_ok')"
                                        class="px-1 py-0.5 rounded text-[10px] font-black transition-all
                                               {{ ($linha['checklist'][$key] ?? '') === 'not_ok'
                                                  ? 'bg-red-500 text-white shadow-sm'
                                                  : 'bg-white/5 text-white/25 hover:bg-red-500/20 hover:text-red-400' }}">
                                    NOK
                                </button>
                                {{-- NA — apenas contacto elétrico --}}
                                @if($key === 'contacto_eletrico')
                                <button wire:click="$set('linhas.{{ $i }}.checklist.{{ $key }}', 'na')"
                                        class="px-1 py-0.5 rounded text-[10px] font-black transition-all
                                               {{ ($linha['checklist'][$key] ?? '') === 'na'
                                                  ? 'bg-gray-500 text-white shadow-sm'
                                                  : 'bg-white/5 text-white/25 hover:bg-gray-500/20 hover:text-gray-400' }}">
                                    NA
                                </button>
                                @endif
                            </div>
                            @else
                            <span class="text-white/10">—</span>
                            @endif
                        </td>
                        @endforeach

                        {{-- Resultado --}}
                        <td class="px-2 py-1.5 text-center">
                            @if($tool)
                            <span class="px-2 py-1 rounded-lg text-[10px] font-black
                                         {{ $linha['apto'] ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ $linha['apto'] ? 'Apto' : 'Não Apto' }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Observações --}}
            <div class="mt-4 bg-white/3 border border-white/8 rounded-xl p-4">
                <label class="text-[10px] font-black text-white/40 uppercase tracking-widest block mb-2">Observações</label>
                <textarea wire:model="observacoes" rows="2"
                          placeholder="Observações gerais desta folha de verificação..."
                          class="w-full bg-white/5 border border-white/10 text-white/80 rounded-lg px-3 py-2 text-xs resize-none focus:outline-none focus:border-indigo-500/40"></textarea>
            </div>
        </div>

        {{-- ── PAINEL LATERAL ──────────────────────────────────── --}}
        @if($painelDados)
        <div class="w-72 flex-shrink-0">
            <div class="bg-gray-900 border border-white/10 rounded-2xl overflow-hidden sticky top-20">

                {{-- Header painel --}}
                <div class="bg-indigo-500/10 border-b border-indigo-500/20 px-4 py-3 flex items-start justify-between gap-2">
                    <div>
                        <div class="text-indigo-300 text-[10px] font-black uppercase tracking-wider">Detalhes</div>
                        <div class="text-white font-black text-sm leading-tight mt-0.5">{{ $painelDados->designacao }}</div>
                    </div>
                    <button wire:click="fecharPainel" class="text-white/30 hover:text-white/60 mt-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Dados da ferramenta --}}
                <div class="px-4 py-3 space-y-1.5 text-xs border-b border-white/8">
                    @php
                        $ul = $painelDados->logs->first();
                        $diasRestantes = $ul?->proxima_verificacao
                            ? now()->diffInDays(\Carbon\Carbon::parse($ul->proxima_verificacao), false)
                            : null;
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-white/35">Ref.</span>
                        <span class="text-white font-mono">{{ $painelDados->referencia ?? '—' }}</span>
                    </div>
                    @if($painelDados->familia)
                    <div class="flex justify-between">
                        <span class="text-white/35">Família</span>
                        <span class="text-white">{{ $painelDados->familia }}</span>
                    </div>
                    @endif
                    @if($painelDados->marca || $painelDados->modelo)
                    <div class="flex justify-between">
                        <span class="text-white/35">Marca/Modelo</span>
                        <span class="text-white text-right">{{ implode(' ', array_filter([$painelDados->marca, $painelDados->modelo])) ?: '—' }}</span>
                    </div>
                    @endif
                    @if($painelDados->num_serie)
                    <div class="flex justify-between">
                        <span class="text-white/35">Nº Série</span>
                        <span class="text-white font-mono">{{ $painelDados->num_serie }}</span>
                    </div>
                    @endif
                    @if($painelDados->localizacao)
                    <div class="flex justify-between">
                        <span class="text-white/35">Localização</span>
                        <span class="text-white">{{ $painelDados->localizacao }}</span>
                    </div>
                    @endif
                </div>

                {{-- Último registo --}}
                @if($ul)
                <div class="px-4 py-3 space-y-1.5 text-xs border-b border-white/8">
                    <div class="text-[10px] font-black text-white/30 uppercase tracking-wider mb-2">Último Registo</div>
                    <div class="flex justify-between">
                        <span class="text-white/35">Verificação</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($ul->data_verificacao)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/35">Próxima</span>
                        <span class="text-white">{{ $ul->proxima_verificacao ? \Carbon\Carbon::parse($ul->proxima_verificacao)->format('d/m/Y') : '—' }}</span>
                    </div>
                    @if($diasRestantes !== null)
                    <div class="flex justify-between">
                        <span class="text-white/35">Tempo</span>
                        <span class="font-black {{ $diasRestantes < 0 ? 'text-red-400' : ($diasRestantes < 30 ? 'text-amber-400' : 'text-emerald-400') }}">
                            {{ $diasRestantes < 0 ? abs($diasRestantes).' dias ATRASADO' : $diasRestantes.' dias p/vencer' }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-white/35">Manutenção</span>
                        <span class="text-white text-right max-w-[140px]">{{ $ul->manutencao_tipo ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/35">Quem</span>
                        <span class="text-white">{{ $ul->verificado_por ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/35">Documento</span>
                        <span class="text-white">{{ $painelDados->tipo_documentacao ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/35">Registo</span>
                        <span class="text-white font-mono">{{ $ul->num_registo_verificacao ?? '—' }}</span>
                    </div>
                    @if($ul->observacoes)
                    <div class="flex justify-between">
                        <span class="text-white/35">Obs.</span>
                        <span class="text-white/70 text-right max-w-[140px]">{{ $ul->observacoes }}</span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Histórico --}}
                @if($painelDados->logs->count() > 1)
                <div class="px-4 py-3">
                    <div class="text-[10px] font-black text-white/30 uppercase tracking-wider mb-2">Histórico</div>
                    <div class="space-y-1.5">
                        @foreach($painelDados->logs->skip(1) as $log)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-white/50">{{ \Carbon\Carbon::parse($log->data_verificacao)->format('d/m/Y') }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $log->apto ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">
                                {{ $log->apto ? 'Apto' : 'N/A' }}
                            </span>
                            <span class="text-white/35 font-mono text-[10px]">{{ $log->num_registo_verificacao ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif

    </div>
</div>
