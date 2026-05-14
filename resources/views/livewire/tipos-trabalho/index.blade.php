<div class="w-full max-w-3xl mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="tag" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Tipos de Trabalho</span>
                <span style="color:rgba(255,255,255,0.4); font-size:11px;">— Categorias usadas nos PEPs</span>
            </div>
            <a href="{{ route('tipos-trabalho.crear') }}" wire:navigate
               style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:6px 16px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Novo Tipo
            </a>
        </div>
    </div>

    {{-- Pesquisa --}}
    <div class="relative mb-4 w-72">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 pointer-events-none" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="search"
               placeholder="Pesquisar tipo de trabalho..."
               class="cme-input pl-9 w-full">
        @if($search)
            <button wire:click="$set('search','')" type="button"
                    style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:#7A7775; cursor:pointer; font-size:1rem; line-height:1;">✕</button>
        @endif
    </div>

    {{-- Tabela --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        @if($tipos->isEmpty())
            <div style="background:#F0EEEB;" class="flex flex-col items-center justify-center py-16">
                <svg class="h-10 w-10 mb-3 cme-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="cme-muted italic text-sm">Não há tipos de trabalho registados.</p>
                <a href="{{ route('tipos-trabalho.crear') }}" wire:navigate
                   class="mt-3 text-sm font-semibold hover:underline" style="color:#09143B;">
                    Criar o primeiro →
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Cor</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Nome</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">PEPs</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @foreach($tipos as $tipo)
                    <tr class="transition-colors hover:bg-[#E4E2DF]" style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full shadow-sm block border border-[rgba(9,20,59,0.10)]"
                                      style="background-color: {{ $tipo->color ?? '#3B82F6' }};"></span>
                                <code class="text-xs" style="color:#7A7775;">{{ $tipo->color }}</code>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-3 py-1 rounded-full text-white text-xs font-bold shadow-sm"
                                  style="background-color: {{ $tipo->color ?? '#3B82F6' }};">
                                {{ $tipo->nombre }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span style="color:#4A4845; font-weight:600;">{{ $tipo->peps_count }}</span>
                            @if($tipo->peps_count > 0)
                                <span class="cme-muted text-xs ml-1">PEP{{ $tipo->peps_count !== 1 ? 's' : '' }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('tipos-trabalho.editar', $tipo) }}" wire:navigate
                                   title="Editar" class="btn-cme-secondary p-2 rounded-lg inline-flex">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <button wire:click="pedirEliminar({{ $tipo->id }})"
                                        title="Eliminar"
                                        style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); padding:8px; border-radius:8px; cursor:pointer;">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Modal confirmar eliminação --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="text-white font-medium text-sm">Eliminar tipo de trabalho</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                @php $tipo = $tipos->find($eliminandoId); @endphp
                <p class="text-sm mb-2" style="color:#4A4845;">
                    Eliminar <strong style="color:#1A1A1A;">{{ $tipo?->nombre }}</strong>?
                </p>
                @if($tipo?->peps_count > 0)
                    <p class="text-xs mb-4" style="color:#A32D2D;">
                        ⚠ Tem {{ $tipo->peps_count }} PEP{{ $tipo->peps_count !== 1 ? 's' : '' }} associado{{ $tipo->peps_count !== 1 ? 's' : '' }} — ficarão sem tipo de trabalho.
                    </p>
                @else
                    <p class="text-xs mb-4 cme-muted">Esta ação não pode ser desfeita.</p>
                @endif
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
