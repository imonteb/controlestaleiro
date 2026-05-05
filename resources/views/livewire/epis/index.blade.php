<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-500 tracking-wide">Inventário Modular</h1>
            <p class="text-sm text-white/80 mt-0.5">Gestão de EPIs, Saúde, Incêndio e Ferramentas</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$set('showImport', true)"
                class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-5 rounded-lg transition-colors shadow-md whitespace-nowrap text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importar
            </button>
            <a href="{{ route('epis.crear') }}" wire:navigate
               class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-(--cme-blue) font-bold py-2.5 px-5 rounded-lg transition-colors shadow-md whitespace-nowrap">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Novo Item
            </a>
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
            <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
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
                        <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Itens para o Catálogo</span>
                    </div>
                    <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 flex flex-col gap-6">
                    {{-- Instucciones --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Colunas Geométricas no Excel</p>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">nome</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">codigo</div>
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
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5 whitespace-nowrap">unidade</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">stock_minimo</div>
                            </div>
                        </div>
                    </div>

                    @if($importMsg)
                        <div class="rounded-xl px-4 py-3 text-sm font-bold {{ $importError ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800' }}">
                            {{ $importMsg }}
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
                            <a href="{{ route('epis.plantilla') }}" class="inline-flex items-center gap-2 text-xs text-green-700 hover:text-green-900 font-bold uppercase tracking-wider transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Modelo Excel
                            </a>
                            <button wire:click="importar" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 disabled:opacity-50 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-green-900/20 text-xs uppercase tracking-widest">
                                <svg wire:loading.remove wire:target="importar" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <svg wire:loading wire:target="importar" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                <span wire:loading.remove wire:target="importar">Importar Itens</span>
                                <span wire:loading wire:target="importar">A processar...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtro + Búsqueda --}}
    <div class="flex flex-col md:flex-row items-center gap-4 w-full">
        {{-- Filters --}}
        <div class="flex items-center gap-2 flex-none">
            <button wire:click="$set('filtro','activos')"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
                style="{{ $filtro === 'activos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
                Ativos
            </button>
            <button wire:click="$set('filtro','inactivos')"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
                style="{{ $filtro === 'inactivos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
                Inativos
            </button>
            <button wire:click="$set('filtro','todos')"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
                style="{{ $filtro === 'todos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
                Todos
            </button>
        </div>
        
        {{-- Search Bar (Right Aligned via Style) --}}
        <div class="w-full md:w-1/3" style="margin-left: auto !important;">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar..."
                       style="padding-left: 3.25rem;"
                       class="block w-full pr-10 py-2 rounded-lg bg-blue-900/50 border border-blue-700 text-white placeholder-blue-400 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 lg:shadow-sm">
                @if($search)
                <button wire:click="$set('search','')" type="button" class="absolute right-3 inset-y-0 flex items-center text-blue-400 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-500 p-2 rounded-lg">
                    <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-white font-semibold text-lg">Catálogo de Itens</span>
@if($search)
<span class="text-white/60 text-xs ml-2 font-normal">| Resultados para "{{ $search }}"</span>
@endif
            </div>
            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
                {{ $items->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-(--cme-blue)/20">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Item / Esp. Técnica</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Categoria / Tipo</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Ref / Cert.</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Riscos</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                    <tr class="{{ !$item->activo ? 'opacity-60 bg-red-50/40' : ($loop->index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50') }} hover:bg-blue-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-semibold text-gray-800">{{ $item->nombre }}</span>
                                @if($item->marca || $item->modelo)
                                    <span class="text-[11px] text-blue-600 font-medium">{{ $item->marca }} {{ $item->modelo }}</span>
                                @endif
                                @if($item->numero_serie)
                                    <span class="text-[10px] text-gray-500 font-mono">SN: {{ $item->numero_serie }}</span>
                                @endif
                                @if(!$item->activo)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center min-w-22.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter
                                {{ $item->tipo === 'epi' ? 'bg-blue-100 text-blue-800' :
                                   ($item->tipo === 'saude' ? 'bg-green-100 text-green-800' :
                                   ($item->tipo === 'incendio' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800')) }}">
                                {{ $item->tipo === 'epi' ? 'EPI' : 
                                   ($item->tipo === 'saude' ? 'Saúde' : 
                                   ($item->tipo === 'incendio' ? 'Incêndio' : 'Ferramenta')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex flex-col gap-0.5">
                            <span class="font-mono text-xs font-bold text-gray-700">{{ $item->codigo ?? '—' }}</span>
                            @if($item->ca_numero)
                                <span class="text-[10px] text-gray-400">CA: {{ $item->ca_numero }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->alerta_stock)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $item->stock_total }} <span class="font-normal">{{ $item->unidade }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    {{ $item->stock_total }} <span class="font-normal">{{ $item->unidade }}</span>
                                </span>
                            @endif
                            @if($item->requiere_talla)
                                <span class="text-xs text-gray-400 block mt-1">c/ tamanhos</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->riscos)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($item->riscos, 0, 2) as $risco)
                                        <span class="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-800">{{ $risco }}</span>
                                    @endforeach
                                    @if(count($item->riscos) > 2)
                                        <span class="px-2 py-0.5 rounded text-xs bg-white/8 text-white/70 border border-white/15">+{{ count($item->riscos) - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('epis.editar', $item->id) }}" wire:navigate
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-200 text-xs font-semibold transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </a>
                                @if($item->activo)
                                    <button wire:click="desativar({{ $item->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border"
                                        style="background:#fff7ed;color:#c2410c;border-color:#fed7aa;">
                                        Desativar
                                    </button>
                                @else
                                    <button wire:click="reactivar({{ $item->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border"
                                        style="background:#f0fdf4;color:#15803d;border-color:#bbf7d0;">
                                        Reativar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Panel inline: motivo de baixa --}}
                    @if($desativandoId === $item->id)
                    <tr class="border-l-4 border-orange-400" style="background:#fff7ed;">
                        <td colspan="6" class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start gap-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-orange-700">Desativar: <strong>{{ $item->nombre }}</strong></p>
                                    <label class="text-xs font-bold text-orange-700 uppercase tracking-wider mt-2 block">Motivo <span class="font-normal text-orange-500">(opcional)</span></label>
                                    <textarea wire:model="motivoBaixa" rows="2" placeholder="Motivo da desativação..."
                                        class="w-full text-sm border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none mt-1"
                                        style="background:white;color:#111827;"></textarea>
                                </div>
                                <div class="flex gap-2 sm:mt-6">
                                    <button wire:click="confirmarDesativar" class="px-4 py-2 rounded-lg text-sm font-bold text-white transition-colors" style="background:#c2410c;">Confirmar</button>
                                    <button wire:click="$set('desativandoId', null)" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <p class="text-sm font-medium">Nenhum EPI registado</p>
                                <a href="{{ route('epis.crear') }}" wire:navigate class="text-(--cme-blue) hover:underline text-xs">Criar o primeiro →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Confirmar eliminación --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 flex items-center gap-3" style="background:#7f1d1d;">
                <svg class="h-6 w-6 text-red-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <h3 class="text-white font-bold text-lg">Eliminar EPI</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-gray-700 text-sm">Esta ação <strong>não pode ser desfeita</strong>. Só é possível eliminar EPIs sem entregas ou recepções registadas.</p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button wire:click="cancelarEliminar" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">Cancelar</button>
                <button wire:click="eliminarPermanente" class="px-4 py-2 rounded-lg text-white text-sm font-bold transition-colors" style="background:#dc2626;">Sim, eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>
