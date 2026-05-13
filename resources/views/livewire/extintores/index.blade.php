<div>
    <div class="flex flex-col gap-4">

        {{-- Header CME --}}
        <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
            <div class="bg-[#09143B] px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="fire" class="text-[#FFD300] w-4 h-4" />
                    <span class="text-white font-medium text-sm">Gestão de Extintores (Segurança Incêndio)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="$set('showImport', true)" class="btn-cme-ghost text-[11px]">📂 Importar</button>
                    <a href="{{ route('extintores.crear') }}" wire:navigate
                       style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; white-space:nowrap; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none;">
                        + Novo Extintor
                    </a>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="cme-card rounded-xl border border-[rgba(9,20,59,0.14)] p-3 flex flex-col md:flex-row items-start md:items-center gap-3">
            <div>
                <label class="cme-label">Viatura</label>
                <select wire:model.live="filtro_veiculo" class="cme-input" style="min-width:180px;">
                    <option value="">Todos os Veículos</option>
                    @foreach($veiculos as $v)
                        <option value="{{ $v->id }}">{{ $v->matricula }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:ml-auto relative">
                <label class="cme-label">Pesquisar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4" style="color:#9CA3AF;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar Nº Série ou Agente..."
                        class="cme-input" style="padding-left:2.25rem; min-width:280px;">
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Nº Série</th>
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Agente / Tamanho</th>
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Viatura</th>
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Última Verif.</th>
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Próxima Revisão</th>
                            <th class="px-6 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                            <th class="px-6 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ações</th>
                        </tr>
                    </thead>
                    <tbody style="color:#4A4845;">
                        @forelse($extintores as $item)
                        @php $zebraBase = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB'; @endphp
                        <tr class="hover:bg-[#EEF2FF] transition-colors" style="background:{{ $zebraBase }}; border-bottom:1px solid rgba(9,20,59,0.06);">
                            <td class="px-6 py-3" style="color:#1A1A1A; font-weight:700;">{{ $item->num_serie }}</td>
                            <td class="px-6 py-3">
                                <span class="badge-info" style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px; text-transform:uppercase;">{{ $item->tipo_agente }}</span>
                                <span class="ml-1" style="color:#1A1A1A; font-weight:700;">{{ $item->tamanho }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @if($item->veiculo)
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-10 border border-[rgba(9,20,59,0.18)] rounded flex items-center justify-center" style="background:white; font-size:10px; font-weight:700; color:#09143B;">
                                            {{ $item->veiculo->matricula }}
                                        </div>
                                        <div style="font-size:10px; color:#4A4845; font-weight:700; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $item->veiculo->model }}</div>
                                    </div>
                                @else
                                    <span style="font-size:11px; color:#7A7775; font-style:italic;">Em Armazém</span>
                                @endif
                            </td>
                            <td class="px-6 py-3" style="color:#4A4845;">{{ $item->data_verificacao?->format('d/m/Y') ?: '—' }}</td>
                            <td class="px-6 py-3">
                                @if($item->proxima_revisao)
                                    @if($item->proxima_revisao->isPast())
                                        <span style="color:#A32D2D; font-weight:700;">{{ $item->proxima_revisao->format('d/m/Y') }}</span>
                                    @elseif($item->proxima_revisao->isFuture() && $item->proxima_revisao->diffInDays(now()) < 30)
                                        <span style="color:#854F0B; font-weight:700;">{{ $item->proxima_revisao->format('d/m/Y') }}</span>
                                    @else
                                        <span style="color:#4A4845; font-weight:600;">{{ $item->proxima_revisao->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    <span style="color:#7A7775; font-weight:700;">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if($item->estado === 'Conforme')
                                    <span class="badge-ok">Conforme</span>
                                @elseif($item->estado === 'Não Conforme')
                                    <span class="badge-danger">Não Conforme</span>
                                @else
                                    <span class="badge-neutral">Desconhecido</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('extintores.editar', $item->id) }}" wire:navigate
                                       class="p-1 rounded hover:bg-[#E4E2DF] transition-colors" style="color:#09143B;">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5-5l5 5m0 0l-5 5m5-5H12" /></svg>
                                    </a>
                                    <button wire:click="pedirEliminar({{ $item->id }})"
                                       class="p-1 rounded hover:bg-[#fde8e8] transition-colors" style="color:#A32D2D;">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center cme-muted">Nenhum extintor registado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($extintores->hasPages())
                <div style="border-top:1px solid rgba(9,20,59,0.08); padding:12px 16px;">{{ $extintores->links() }}</div>
            @endif
        </div>

        {{-- Modal confirmar eliminar --}}
        @if($confirmandoEliminar)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-bold mb-2" style="color:#1A1A1A;">Eliminar extintor?</h3>
                <p class="text-sm mb-5" style="color:#7A7775;">Esta acção não pode ser revertida.</p>
                <div class="flex gap-3 justify-end">
                    <button wire:click="cancelarEliminar" class="btn-cme-secondary">Cancelar</button>
                    <button wire:click="confirmarEliminar" style="background:#A32D2D; color:white; font-weight:700; font-size:12px; padding:6px 16px; border-radius:6px; border:none; cursor:pointer;">Eliminar</button>
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
                                <svg class="h-5 w-5 text-[#FFD300]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Extintores</span>
                        </div>
                        <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6 flex flex-col gap-6" style="background:#F0EEEB;">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas Necessárias no Excel</p>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach([['Obrigatório','red','num_serie'],['Opcional','gray','tipo'],['Opcional','gray','tamanho'],['Opcional','gray','data_verificacao'],['Opcional','gray','proxima_revisao'],['Opcional','gray','matricula']] as [$tipo,$cor,$campo])
                                <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                    <div class="text-[9px] font-black text-{{ $cor }}-500 uppercase">{{ $tipo }}</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5 whitespace-nowrap">{{ $campo }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if($importMsg || $importError)
                            <div class="rounded-xl px-4 py-3 text-sm font-bold {{ $importError ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800' }}">
                                {{ $importError ?: $importMsg }}
                            </div>
                        @endif
                        <div class="space-y-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Selecionar Ficheiro (.xlsx, .xls, .csv)</label>
                                <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                                       class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.14)] rounded-xl bg-white">
                                @error('ficheiroImport') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center justify-between gap-4 pt-4 border-t border-[rgba(9,20,59,0.08)]">
                                <a href="{{ route('extintores.plantilla') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-colors hover:opacity-75" style="color:#09143B;">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Modelo Excel
                                </a>
                                <button wire:click="importar" wire:loading.attr="disabled"
                                        class="btn-cme-primary inline-flex items-center gap-2 disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="importar" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <svg wire:loading wire:target="importar" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
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
