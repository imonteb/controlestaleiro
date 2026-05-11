<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="inbox-arrow-down" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Recepções de EPI</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="$set('showImport', true)" class="btn-cme-ghost text-[11px]">
                    📥 Importar
                </button>
                <a href="{{ route('epis.rececoes.crear') }}" wire:navigate
                   style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                    + Nova Recepção
                </a>
            </div>
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
                
                <div style="background:#09143B !important;" class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div style="background:rgba(255,211,0,0.15);" class="p-2 rounded-lg">
                            <svg class="h-5 w-5 text-[#FFD300]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <span class="text-white font-semibold text-lg uppercase tracking-wider">Importar Recepções de Stock</span>
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
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">quantidade</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-amber-600 uppercase">Necessário*</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">referencia</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-amber-600 uppercase">Necessário*</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5 whitespace-nowrap">designacao</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">tamanho</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">fornecedor</div>
                            </div>
                            <div class="p-2 border border-gray-100 rounded-lg bg-gray-50/50">
                                <div class="text-[9px] font-black text-gray-400 uppercase">Opcional</div>
                                <div class="text-xs font-mono font-bold text-gray-800 mt-0.5">fatura</div>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-400 mt-2">* Use o <strong>codigo</strong> do inventário ou a <strong>designacao</strong> exata para identificar o item.</p>
                    </div>

                    @if($mensagemImport)
                        <div class="rounded-xl px-4 py-3 text-sm font-bold {{ $importError ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-green-50 border border-green-200 text-green-800' }}">
                            {{ $mensagemImport }}
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
                            <a href="{{ route('epis.rececoes.plantilla') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-colors" style="color:#09143B;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Modelo Excel
                            </a>
                            <button wire:click="importar" wire:loading.attr="disabled"
                                    class="btn-cme-primary inline-flex items-center gap-2 disabled:opacity-50 py-3 px-8 text-xs uppercase tracking-widest">
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

    {{-- Filtros --}}
    <div class="cme-card rounded-xl border border-[rgba(9,20,59,0.14)] p-3 flex items-center justify-between gap-2 flex-wrap">
        <select wire:model.live="filtroEpi" class="rounded-lg text-sm" style="background:white;color:#111827;border:1px solid #d1d5db;padding:8px 12px;outline:none;">
            <option value="">Todos os EPIs</option>
            @foreach($epiItems as $epi)
                <option value="{{ $epi->id }}">{{ $epi->nombre }}{{ $epi->codigo ? ' ('.$epi->codigo.')' : '' }}</option>
            @endforeach
        </select>
        <div style="position:relative;display:flex;align-items:center;">
            <svg style="position:absolute;left:10px;color:#9ca3af;pointer-events:none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar..."
                   style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 10px 8px 34px;border-radius:8px;font-size:0.875rem;width:240px;outline:none;">
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-500 p-2 rounded-lg">
                    <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-white font-semibold text-lg">Recepções de Stock</span>
            </div>
            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">{{ $rececoes->count() }} registos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-(--cme-blue)/20">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">Data</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">EPI</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-(--cme-blue) uppercase">Qtd</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">Tamanho</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">Fornecedor</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">Fatura</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-(--cme-blue) uppercase">Registado por</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-(--cme-blue) uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rececoes as $r)
                    <tr class="{{ $loop->index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }} hover:bg-blue-50/60 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $r->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800">{{ $r->epiItem->nombre ?? '—' }}</span>
                                @if($r->epiItem->codigo ?? null)
                                    <span class="text-xs text-gray-500 font-mono">{{ $r->epiItem->codigo }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">+{{ $r->cantidad }} {{ $r->epiItem->unidade ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $r->talla ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $r->proveedor ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $r->numero_factura ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $r->registradoPor->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="pedirEliminar({{ $r->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-semibold transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="text-sm font-medium">Nenhuma recepção registada</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal eliminar --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 flex items-center gap-3" style="background:#7f1d1d;">
                <h3 class="text-white font-bold text-lg">Eliminar recepção</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-gray-700 text-sm">Tem a certeza? O stock será recalculado automaticamente.</p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button wire:click="cancelarEliminar" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50">Cancelar</button>
                <button wire:click="eliminarPermanente" class="px-4 py-2 rounded-lg text-white text-sm font-bold" style="background:#dc2626;">Sim, eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>
