<div class="flex flex-col gap-4 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Catálogo de EPIs</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="$set('showImport', true)"
                    style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:5px 12px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                    📥 Importar
                </button>
                <a href="{{ route('epis.crear') }}" wire:navigate
                   style="display:inline-flex; align-items:center; gap:6px; background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                    + Novo Item
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
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <span style="color:white;" class="font-semibold text-lg uppercase tracking-wider">Importar Itens para o Catálogo</span>
                    </div>
                    <button @click="open = false" class="text-white/70 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 flex flex-col gap-6" style="background:#F0EEEB;">
                    {{-- Instrucciones --}}
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest mb-3" style="color:#7A7775 !important;">Colunas Geométricas no Excel</p>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold mt-0.5" style="color:#1A1A1A;">nome</div>
                            </div>
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black text-red-500 uppercase">Obrigatório</div>
                                <div class="text-xs font-mono font-bold mt-0.5" style="color:#1A1A1A;">codigo</div>
                            </div>
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black uppercase" style="color:#7A7775;">Opcional</div>
                                <div class="text-xs font-mono font-bold mt-0.5" style="color:#1A1A1A;">tipo</div>
                            </div>
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black uppercase" style="color:#7A7775;">Opcional</div>
                                <div class="text-xs font-mono font-bold mt-0.5" style="color:#1A1A1A;">tamanho</div>
                            </div>
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black uppercase" style="color:#7A7775;">Opcional</div>
                                <div class="text-xs font-mono font-bold mt-0.5 whitespace-nowrap" style="color:#1A1A1A;">unidade</div>
                            </div>
                            <div class="p-2 rounded-lg" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                                <div class="text-[9px] font-black uppercase" style="color:#7A7775;">Opcional</div>
                                <div class="text-xs font-mono font-bold mt-0.5" style="color:#1A1A1A;">stock_minimo</div>
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
                            <label class="text-[10px] font-black uppercase tracking-widest" style="color:#4A4845 !important;">Selecionar Ficheiro (.xlsx, .xls, .csv)</label>
                            <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                                   class="block w-full text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#09143B] file:text-[#FFD300] cursor-pointer"
                                   style="background:white; border:1px solid rgba(9,20,59,0.18); border-radius:8px; color:#1A1A1A;">
                            @error('ficheiroImport') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4 pt-4" style="border-top:1px solid rgba(9,20,59,0.08);">
                            <a href="{{ route('epis.plantilla') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-colors hover:opacity-70"
                               style="color:#09143B;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Modelo Excel
                            </a>
                            <button wire:click="importar" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 font-bold py-3 px-8 rounded-xl transition-all shadow-md text-xs uppercase tracking-widest disabled:opacity-50"
                                    style="background:#09143B; color:#FFD300;">
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

    {{-- Filtro + Pesquisa --}}
    <div style="background:#F0EEEB !important; border:1px solid rgba(9,20,59,0.14); border-radius:10px; padding:10px 14px;" class="flex items-center justify-between gap-2 flex-wrap">
        <div class="flex items-center gap-2">
            <button wire:click="$set('filtro','activos')"
                style="{{ $filtro === 'activos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Ativos
            </button>
            <button wire:click="$set('filtro','inactivos')"
                style="{{ $filtro === 'inactivos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Inativos
            </button>
            <button wire:click="$set('filtro','todos')"
                style="{{ $filtro === 'todos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Todos
            </button>
        </div>
        <div class="relative flex items-center">
            <svg class="absolute left-2.5 h-3.5 w-3.5 pointer-events-none" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="search"
                   id="epi-search"
                   placeholder="Pesquisar..."
                   style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px; font-size:12px; padding:6px 28px; width:220px; outline:none;">
            @if($search)
            <button wire:click="$set('search','')" type="button" class="absolute right-2 cursor-pointer text-base leading-none" style="color:#7A7775; background:none; border:none;">✕</button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">

        {{-- Table header bar --}}
        <div style="background:#09143B !important;" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div style="background:#FFD300;" class="p-2 rounded-lg">
                    <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span style="color:white;" class="font-semibold text-lg">Catálogo de Itens</span>
                @if($search)
                <span style="color:rgba(255,255,255,0.6);" class="text-xs ml-2 font-normal">| Resultados para "{{ $search }}"</span>
                @endif
            </div>
            <span style="background:rgba(255,255,255,0.15); color:white;" class="text-xs font-bold px-3 py-1 rounded-full">
                {{ $items->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#E4E2DF !important;">
                    <tr style="background:#E4E2DF !important;">
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Item / Esp. Técnica</th>
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Categoria / Tipo</th>
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Ref / Cert.</th>
                        <th class="px-6 py-3.5 text-center border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Stock</th>
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Riscos</th>
                        <th class="px-6 py-3.5 text-right border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]" style="background:#FFFFFF !important;">
                    @forelse($items as $item)
                    <tr class="{{ !$item->activo ? 'opacity-60' : '' }} transition-colors {{ $loop->index % 2 === 0 ? 'epi-row-even' : 'epi-row-odd' }}"
                        style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span style="color:#1A1A1A; font-weight:600;">{{ $item->nombre }}</span>
                                @if($item->marca || $item->modelo)
                                    <span class="text-[11px] font-medium" style="color:#09143B;">{{ $item->marca }} {{ $item->modelo }}</span>
                                @endif
                                @if($item->numero_serie)
                                    <span class="text-[10px] font-mono" style="color:#7A7775;">SN: {{ $item->numero_serie }}</span>
                                @endif
                                @if(!$item->activo)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter"
                                  style="{{ $item->tipo === 'epi' ? 'background:#dbeafe; color:#1e40af;' : 'background:#dcfce7; color:#166534;' }}">
                                {{ $item->tipo === 'epi' ? 'EPI' : 'Saúde' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex flex-col gap-0.5">
                            <span class="font-mono text-xs font-bold" style="color:#1A1A1A;">{{ $item->codigo ?? '—' }}</span>
                            @if($item->ca_numero)
                                <span class="text-[10px]" style="color:#7A7775;">CA: {{ $item->ca_numero }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->alerta_stock)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold" style="background:#fde8e8; color:#A32D2D;">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $item->stock_total }} <span class="font-normal">{{ $item->unidade }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold" style="background:#d4ede4; color:#0F6E56;">
                                    {{ $item->stock_total }} <span class="font-normal">{{ $item->unidade }}</span>
                                </span>
                            @endif
                            @if($item->requiere_talla)
                                <span class="text-xs block mt-1" style="color:#7A7775;">c/ tamanhos</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->riscos)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($item->riscos, 0, 2) as $risco)
                                        <span class="px-2 py-0.5 rounded text-xs bg-amber-100 text-amber-800">{{ $risco }}</span>
                                    @endforeach
                                    @if(count($item->riscos) > 2)
                                        <span class="px-2 py-0.5 rounded text-xs" style="background:rgba(9,20,59,0.08); color:#4A4845; border:1px solid rgba(9,20,59,0.14);">+{{ count($item->riscos) - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span style="color:#7A7775;">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('epis.editar', $item->id) }}" wire:navigate
                                   title="Editar EPI"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14); text-decoration:none; flex-shrink:0;">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                @if($item->activo)
                                    <button wire:click="desativar({{ $item->id }})"
                                        title="Desativar EPI"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20); cursor:pointer; flex-shrink:0;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                @else
                                    <button wire:click="reactivar({{ $item->id }})"
                                        title="Reativar EPI"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25); cursor:pointer; flex-shrink:0;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                                        style="background:white; color:#111827;"></textarea>
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
                            <div class="flex flex-col items-center gap-3" style="color:#7A7775;">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <p class="text-sm font-medium">Nenhum EPI registado</p>
                                <a href="{{ route('epis.crear') }}" wire:navigate class="text-xs hover:underline" style="color:#09143B;">Criar o primeiro →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($items->hasPages())
            <div style="background:#F0EEEB; border-top:1px solid rgba(9,20,59,0.08);" class="px-4 py-3">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modal: Confirmar eliminación --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="text-white font-medium text-sm">Eliminar EPI</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                <p class="text-sm mb-4" style="color:#4A4845;">Esta ação <strong style="color:#1A1A1A;">não pode ser desfeita</strong>. Só é possível eliminar EPIs sem entregas ou recepções registadas.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelarEliminar" class="btn-cme-secondary flex-1">Cancelar</button>
                    <button wire:click="eliminarPermanente"
                            style="flex:1; background:#A32D2D; color:white; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                        Sim, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
.epi-row-even { background: #FFFFFF !important; }
.epi-row-odd  { background: #F0EEEB !important; }
.epi-row-even:hover, .epi-row-odd:hover { background: #E4E2DF !important; }
#epi-search::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
