<div>
    <div class="flex flex-col gap-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold uppercase text-yellow-600">Gestão de Extintores (Segurança Incêndio)</h1>
                <p class="text-sm text-white/70 mt-0.5 font-medium">Gestão de EPIs, Saúde, Incêndio e Ferramentas</p>
            </div>
            <div class="flex items-center gap-3 self-end md:self-auto">
                <button wire:click="$set('showImport', true)"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white font-semibold text-sm transition-colors shadow-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importar
                </button>
                <a href="{{ route('extintores.crear') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold text-sm transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Extintor
                </a>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4 w-full">
            {{-- Filter --}}
            <div class="flex-none">
                <select wire:model.live="filtro_veiculo" class="w-48 rounded-lg border-white/15 text-sm focus:border-yellow-500 focus:ring-yellow-500 bg-white/8 text-white/80">
                    <option value="">Todos os Veículos</option>
                    @foreach($veiculos as $v)
                        <option value="{{ $v->id }}">{{ $v->matricula }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Search Bar (Right Aligned via Style) --}}
            <div class="w-full md:w-1/3" style="margin-left: auto !important;">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar Nº Série ou Agente..."
                        style="padding-left: 3.25rem;"
                        class="block w-full pr-3 py-2 rounded-lg bg-blue-900/50 border border-blue-700 text-white placeholder-blue-400 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Nº Série</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Agente / Tamanho</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Viatura</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Última Verif.</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Próxima Revisão</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px]">Estado</th>
                            <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-[10px] text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-600">
                        @forelse($extintores as $item)
                        <tr class="hover:bg-red-50/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $item->num_serie }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded bg-white/8 text-[10px] font-black text-white/80 border border-white/15 uppercase">{{ $item->tipo_agente }}</span>
                                <span class="text-gray-700 font-bold ml-1">{{ $item->tamanho }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->veiculo)
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-10 border border-gray-300 rounded bg-white flex items-center justify-center text-[10px] font-bold">
                                            {{ $item->veiculo->matricula }}
                                        </div>
                                        <div class="text-[10px] text-gray-700 font-bold truncate max-w-[100px]">{{ $item->veiculo->model }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-600 font-bold italic">Em Armazém</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $item->data_verificacao?->format('d/m/Y') ?: '—' }}</td>
                            <td class="px-6 py-4">
                                @if($item->proxima_revisao)
                                    <span @class([
                                        'font-bold',
                                        'text-red-600' => $item->proxima_revisao->isPast(),
                                        'text-orange-600' => $item->proxima_revisao->isFuture() && $item->proxima_revisao->diffInDays(now()) < 30,
                                    ])>
                                        {{ $item->proxima_revisao->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-500 font-bold">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->estado === 'Conforme')
                                    <span class="text-[10px] font-bold text-green-600 uppercase">● Conforme</span>
                                @elseif($item->estado === 'Não Conforme')
                                    <span class="text-[10px] font-bold text-red-600 uppercase">● Não Conforme</span>
                                @else
                                    <span class="text-[10px] font-black text-gray-700 uppercase">● Desconhecido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('extintores.editar', $item->id) }}" wire:navigate 
                                       class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5-5l5 5m0 0l-5 5m5-5H12" /></svg>
                                    </a>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Tem a certeza que deseja remover este extintor?"
                                       class="p-1 text-gray-400 hover:text-red-700 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">Nenhum extintor registado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($extintores->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">{{ $extintores->links() }}</div>
            @endif
        </div>

        {{-- Modal Importación --}}
        <div x-data="{ open: @entangle('showImport') }"
             x-show="open"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-blue-950/60 backdrop-blur-sm transition-opacity" 
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                     x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="bg-green-800 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 p-2 rounded-lg">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Extintores</span>
                        </div>
                        <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6 flex flex-col gap-6">
                        {{-- Instucciones --}}
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas Necessárias no Excel</p>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">num_serie</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">tipo</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">tamanho</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5 whitespace-nowrap">data_verificacao</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5 whitespace-nowrap">proxima_revisao</div>
                                </div>
                                <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                    <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                    <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">matricula</div>
                                </div>
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
                                       class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-green-100 file:text-green-800 hover:file:bg-green-200 cursor-pointer text-gray-900 border border-gray-100 rounded-xl bg-gray-50/30">
                                @error('ficheiroImport') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('extintores.plantilla') }}" class="inline-flex items-center gap-2 text-xs text-green-700 hover:text-green-900 font-bold uppercase tracking-wider transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Modelo Excel
                                </a>
                                <button wire:click="importar" wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:opacity-50 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-green-900/20 text-xs uppercase tracking-widest">
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
