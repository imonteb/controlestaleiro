<div>
    <div class="flex flex-col gap-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold uppercase text-yellow-600">Gestão de Saúde (Material Primeiros Socorros)</h1>
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
                <a href="{{ route('saude.kit.crear') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold text-sm transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Registar Novo Kit
                </a>
            </div>
        </div>

        {{-- Kits Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kits as $kit)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden hover:border-green-200 transition-all">
                <div class="p-5 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">{{ $kit->identificador_kit ?: 'Kit S/ Ref' }}</div>
                            <h3 class="font-bold text-gray-900">{{ $kit->designacao }}</h3>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="text-sm font-bold text-gray-900">{{ $kit->veiculo->matricula }}</div>
                        <span @class([
                            'px-2 py-0.5 rounded-full text-[9px] font-bold uppercase',
                            'bg-green-100 text-green-700' => $kit->status_saude === 'OK',
                            'bg-red-100 text-red-700' => $kit->status_saude === 'Expirado',
                            'bg-gray-200 text-gray-700' => $kit->status_saude === 'Vazio',
                        ])>
                            {{ $kit->status_saude }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex-1 space-y-3">
                    <div class="text-[10px] font-black text-gray-700 uppercase tracking-widest">Conteúdo Mandatory / Validades</div>
                    <ul class="space-y-2">
                        @forelse($kit->itens->take(4) as $kitItem)
                        <li class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">{{ $kitItem->item->nombre }}</span>
                            <span @class([
                                'font-bold',
                                'text-red-700' => $kitItem->data_validade?->isPast(),
                                'text-gray-700' => !$kitItem->data_validade?->isPast(),
                            ])>
                                {{ $kitItem->data_validade?->format('d/m/Y') ?: 'N/V' }}
                            </span>
                        </li>
                        @empty
                        <li class="text-xs text-gray-600 font-bold italic">Mala vazia ou sem registos</li>
                        @endforelse
                        @if($kit->itens->count() > 4)
                            <li class="text-[10px] text-(--cme-blue) font-bold">+ {{ $kit->itens->count() - 4 }} outros itens...</li>
                        @endif
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 flex items-center justify-between">
                    <button wire:click="deleteKit({{ $kit->id }})" wire:confirm="Eliminar este kit e todo o seu conteúdo?" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                    <a href="{{ route('saude.kit.editar', $kit->id) }}" wire:navigate class="text-xs font-bold text-(--cme-blue) hover:underline flex items-center gap-1 uppercase tracking-widest">
                        Gerir Conteúdo
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 bg-white rounded-2xl border border-dashed border-gray-200 text-center">
                <div class="text-gray-300 mb-4">
                    <svg class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </div>
                <div class="text-gray-500 font-medium">Nenhum kit de saúde registado na frota</div>
                <a href="{{ route('saude.kit.crear') }}" wire:navigate class="mt-4 inline-block text-xs font-bold text-green-600 hover:underline">CLIQUE AQUI PARA REGISTAR O PRIMEIRO KIT</a>
            </div>
            @endforelse
        </div>
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
                        <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Kits de Saúde</span>
                    </div>
                    <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 flex flex-col gap-6">
                    {{-- Instucciones --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas Necessárias no Excel</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">identificador</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">designacao</div>
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
                            <a href="{{ route('saude.plantilla') }}" class="inline-flex items-center gap-2 text-xs text-green-700 hover:text-green-900 font-bold uppercase tracking-wider transition-colors">
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
