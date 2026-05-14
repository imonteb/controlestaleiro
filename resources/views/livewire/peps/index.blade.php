<div class="flex flex-col gap-4 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="clipboard" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">PEPs</span>
            </div>
            <a href="{{ route('peps.crear') }}" wire:navigate
               style="display:inline-flex; align-items:center; gap:6px; background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                + Novo PEP
            </a>
        </div>
    </div>

    {{-- Filtro de estado + Pesquisa --}}
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
            <input wire:model.live.debounce.300ms="pesquisa" type="search"
                   id="pep-search"
                   placeholder="Pesquisar..."
                   style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px; font-size:12px; padding:6px 28px; width:220px; outline:none;">
            @if($pesquisa)
            <button wire:click="$set('pesquisa','')" type="button" class="absolute right-2 cursor-pointer text-base leading-none" style="color:#7A7775; background:none; border:none;">✕</button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">

        {{-- Table header bar --}}
        <div style="background:#09143B !important;" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div style="background:#FFD300;" class="p-2 rounded-lg">
                    <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span style="color:white;" class="font-semibold text-lg">Listagem de PEPs</span>
            </div>
            <span style="background:rgba(255,255,255,0.15); color:white;" class="text-xs font-bold px-3 py-1 rounded-full">
                {{ $peps->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#E4E2DF !important;">
                    <tr style="background:#E4E2DF !important;">
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Nome / PEP</th>
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Localização</th>
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider" style="color:#7A7775 !important;">Tipo de Trabalho</th>
                        <th class="px-6 py-3.5 text-right border-b border-[rgba(9,20,59,0.08)] text-xs font-bold uppercase tracking-wider whitespace-nowrap" style="color:#7A7775 !important;">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]" style="background:#FFFFFF !important;">
                    @forelse($peps as $pep)
                    <tr class="{{ !$pep->activo ? 'opacity-50' : '' }} transition-colors {{ $loop->index % 2 === 0 ? 'pep-row-even' : 'pep-row-odd' }}"
                        style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span style="color:#1A1A1A; font-weight:600;">{{ $pep->nombre }}</span>
                                @if(!$pep->activo)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Inativo
                                    </span>
                                @endif
                                @if($pep->motivo_baja)
                                    <div class="text-xs text-red-500">{{ $pep->motivo_baja }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($pep->localizacao)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                      style="background:rgba(9,20,59,0.08); color:#09143B; border:1px solid rgba(9,20,59,0.15);">
                                    {{ $pep->localizacao->nombre ?? $pep->localizacao->name ?? '—' }}
                                </span>
                            @else
                                <span style="color:#7A7775;">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($pep->tipoTrabalho)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white shadow-sm"
                                      style="background-color: {{ $pep->tipoTrabalho->color ?? '#ca8a04' }};">
                                    {{ $pep->tipoTrabalho->nombre }}
                                </span>
                            @else
                                <span style="color:#7A7775;">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('peps.editar', $pep->id) }}" wire:navigate
                                   title="Editar PEP"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14); text-decoration:none; flex-shrink:0;">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                @if($pep->activo)
                                    <button wire:click="desativar({{ $pep->id }})"
                                        title="Desativar PEP"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20); cursor:pointer; flex-shrink:0;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                @else
                                    <button wire:click="reactivar({{ $pep->id }})"
                                        title="Reativar PEP"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25); cursor:pointer; flex-shrink:0;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <button wire:click="pedirEliminar({{ $pep->id }})"
                                        title="Eliminar PEP"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer; flex-shrink:0;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Panel inline: motivo de baixa --}}
                    @if($desativandoId === $pep->id)
                    <tr class="border-l-4 border-orange-400" style="background:#fff7ed;">
                        <td colspan="4" class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start gap-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-orange-700">
                                        Dar baixa: <strong>{{ $pep->nombre }}</strong>
                                    </p>
                                    <p class="text-xs text-orange-600 mt-0.5 mb-2">O PEP ficará marcado como inativo e não aparecerá no painel de atribuição. Pode ser reativado a qualquer momento.</p>
                                    <label class="text-xs font-bold text-orange-700 uppercase tracking-wider">Motivo da baixa <span class="font-normal text-orange-500">(opcional)</span></label>
                                    <textarea wire:model="motivoBaixa" rows="2"
                                        placeholder="Ex: Projeto finalizado, cancelado, em pausa..."
                                        class="w-full text-sm border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none mt-1"
                                        style="background:white; color:#111827;"></textarea>
                                </div>
                                <div class="flex gap-2 sm:mt-6">
                                    <button wire:click="confirmarDesativar"
                                        class="px-4 py-2 rounded-lg text-sm font-bold text-white transition-colors"
                                        style="background:#c2410c;">
                                        Confirmar Baixa
                                    </button>
                                    <button wire:click="$set('desativandoId', null)"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3" style="color:#7A7775;">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm font-medium">Nenhum PEP registado</p>
                                <a href="{{ route('peps.crear') }}" wire:navigate class="text-xs hover:underline" style="color:#09143B;">Criar o primeiro →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Confirmar eliminación permanente --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="text-white font-medium text-sm">Eliminar PEP</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                <p class="text-sm mb-2" style="color:#4A4845;">Esta ação <strong style="color:#1A1A1A;">não pode ser desfeita</strong>. O PEP será eliminado definitivamente.</p>
                <p class="text-xs mb-4" style="color:#A32D2D;">Tem a certeza de que deseja continuar?</p>
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
.pep-row-even { background: #FFFFFF !important; }
.pep-row-odd  { background: #F0EEEB !important; }
.pep-row-even:hover, .pep-row-odd:hover { background: #E4E2DF !important; }
#pep-search::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
