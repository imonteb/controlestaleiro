<div class="flex flex-col gap-4 w-full max-w-6xl mx-auto px-4 py-6">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex items-center gap-2">
            <flux:icon name="bell" class="text-[#FFD300] w-4 h-4" />
            <span style="color:white;" class="text-sm font-medium">Pedidos de EPI</span>
        </div>
    </div>

    {{-- Filtros + Pesquisa --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14); border-radius:10px; padding:10px 14px;" class="flex items-center justify-between gap-2 flex-wrap">
        <div class="flex items-center gap-2">
            @foreach(['pendente' => 'Pendentes', 'aprovado' => 'Aprovados', 'todos' => 'Todos', 'rejeitado' => 'Rejeitados'] as $val => $label)
                <button wire:click="$set('filtro','{{ $val }}')"
                    style="{{ $filtro === $val ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                    class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <div class="relative flex items-center">
            <svg class="absolute left-2.5 h-3.5 w-3.5 pointer-events-none" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="search"
                   placeholder="Procurar..."
                   style="background:white; color:#1A1A1A; border:1px solid rgba(9,20,59,0.18); border-radius:8px; font-size:12px; padding:6px 28px; width:220px; outline:none;">
            @if($search)
                <button wire:click="$set('search','')" type="button" class="absolute right-2 cursor-pointer text-base leading-none" style="color:#7A7775; background:none; border:none;">✕</button>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session()->has('success'))
        <div style="background:#d4ede4; border:1px solid rgba(15,110,86,0.25); color:#0F6E56;" class="px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">

        {{-- Table header bar --}}
        <div style="background:#09143B;" class="px-5 py-3.5 flex items-center justify-between">
            <span style="color:white;" class="font-semibold text-sm">Listagem de Pedidos</span>
            <span style="background:rgba(255,255,255,0.15); color:white;" class="text-xs font-bold px-3 py-1 rounded-full">
                {{ $pedidos->total() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#E4E2DF;">
                    <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Data</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Colaborador</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Item</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Detalhes</th>
                        <th class="px-5 py-3 text-left" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                        <th class="px-5 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Acções</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($pedidos as $pedido)
                    <tr style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;" class="transition-colors hover:bg-[#E4E2DF]">
                        <td class="px-5 py-3" style="color:#7A7775; font-size:11px;">
                            {{ $pedido->created_at->format('d/m/Y') }}<br>
                            <span style="font-size:10px;">{{ $pedido->created_at->format('H:i') }}</span>
                        </td>

                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div style="width:32px; height:32px; border-radius:8px; background:rgba(9,20,59,0.10); color:#09143B; font-weight:700; font-size:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    {{ $pedido->colaborador->numero_colaborador }}
                                </div>
                                <div class="flex flex-col">
                                    <span style="color:#1A1A1A; font-weight:600; font-size:12px;">{{ $pedido->colaborador->nombre }}</span>
                                    <span style="color:#7A7775; font-size:11px;">{{ $pedido->colaborador->cargo }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3">
                            <span style="color:#09143B; font-weight:600; font-size:12px;">{{ $pedido->epiItem->nombre }}</span>
                        </td>

                        <td class="px-5 py-3">
                            <div class="flex flex-col gap-1">
                                @if($pedido->tamanho)
                                    <span style="display:inline-block; background:rgba(9,20,59,0.08); color:#09143B; border:1px solid rgba(9,20,59,0.14); border-radius:4px; padding:1px 8px; font-size:10px; font-weight:600; width:fit-content;">
                                        Tam: {{ $pedido->tamanho }}
                                    </span>
                                @endif
                                <span style="color:#7A7775; font-size:11px; font-style:italic;">"{{ $pedido->motivo_colaborador ?? 'Sem motivo' }}"</span>
                            </div>
                        </td>

                        <td class="px-5 py-3">
                            @if($pedido->estado === \App\Enums\EpiPedidoEstado::Pendente)
                                <span style="display:inline-block; padding:3px 10px; border-radius:6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);">
                                    {{ $pedido->estado->label() }}
                                </span>
                            @elseif($pedido->estado === \App\Enums\EpiPedidoEstado::Aprovado)
                                <span style="display:inline-block; padding:3px 10px; border-radius:6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25);">
                                    {{ $pedido->estado->label() }}
                                </span>
                            @elseif($pedido->estado === \App\Enums\EpiPedidoEstado::Rejeitado)
                                <span style="display:inline-block; padding:3px 10px; border-radius:6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20);">
                                    {{ $pedido->estado->label() }}
                                </span>
                            @else
                                <span style="display:inline-block; padding:3px 10px; border-radius:6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:#E4E2DF; color:#7A7775; border:1px solid rgba(9,20,59,0.14);">
                                    {{ $pedido->estado->label() }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Pendente)
                                    <button wire:click="aprovar({{ $pedido->id }})" title="Aprovar"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25); cursor:pointer;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button wire:click="rejeitar({{ $pedido->id }})" title="Rejeitar"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif

                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Aprovado)
                                    <button wire:click="prepararEntrega({{ $pedido->id }})" title="Entregar agora"
                                        style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; background:#09143B; color:#FFD300; font-size:11px; font-weight:700; border:none; cursor:pointer; white-space:nowrap;">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
                                        Entregar
                                    </button>
                                    <button wire:click="rejeitar({{ $pedido->id }})" title="Rejeitar"
                                        style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer;">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif

                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Rejeitado && $pedido->motivo_admin)
                                    <flux:tooltip content="Motivo: {{ $pedido->motivo_admin }}">
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#E4E2DF; color:#7A7775; border:1px solid rgba(9,20,59,0.14); cursor:help;">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </span>
                                    </flux:tooltip>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3" style="color:#7A7775;">
                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <p class="text-sm font-medium italic">Nenhum pedido encontrado com este filtro.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidos->hasPages())
        <div class="px-5 py-3" style="border-top:1px solid rgba(9,20,59,0.08);">
            {{ $pedidos->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Rejeição --}}
    @if($rejeitandoId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-white font-medium text-sm">Rejeitar Pedido</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                <p style="color:#4A4845; font-size:13px;" class="mb-4">Indique o motivo da rejeição para o colaborador.</p>
                <textarea wire:model="motivoRejeicao"
                    placeholder="Ex: Equipamento ainda em bom estado, stock esgotado..."
                    rows="3" class="cme-input w-full mb-4" style="resize:none;"></textarea>
                <div class="flex gap-3">
                    <button wire:click="$set('rejeitandoId', null)" class="btn-cme-secondary flex-1">Cancelar</button>
                    <button wire:click="confirmarRejeicao"
                        style="flex:1; background:#A32D2D; color:white; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                        Rejeitar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Entrega (pre-confirmação) --}}
    @if($entregandoId && $pedidosBatch->isNotEmpty())
    @php $batchColaborador = $pedidosBatch->first()->colaborador; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" wire:key="modal-pre-entrega">
        <div class="w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-[rgba(9,20,59,0.16)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
                <span class="text-white font-medium text-sm">Confirmar Entrega — {{ $batchColaborador->nombre }}</span>
            </div>
            <div style="background:white;" class="px-6 py-5 flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    @foreach($pedidosBatch as $bp)
                    <div class="flex items-center justify-between rounded-lg px-3 py-2" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.08);">
                        <span style="color:#09143B; font-weight:600; font-size:12px;">{{ $bp->epiItem->nombre }}</span>
                        @if($bp->tamanho)
                            <span style="background:rgba(9,20,59,0.08); color:#09143B; border-radius:4px; padding:1px 8px; font-size:10px; font-weight:600;">{{ $bp->tamanho }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div>
                    <label class="cme-label">Observações Adicionais</label>
                    <textarea wire:model="observacoesEntrega"
                        placeholder="Alguma nota sobre a entrega..."
                        rows="2" class="cme-input w-full mt-1" style="resize:none;"></textarea>
                </div>
                <div class="flex gap-3 pt-2" style="border-top:1px solid rgba(9,20,59,0.08);">
                    <button wire:click="cancelarEntrega" class="btn-cme-secondary flex-1">Cancelar</button>
                    <button wire:click="confirmarEntrega"
                        style="flex:1; background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                        Prosseguir
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal unificado de assinatura --}}
    <livewire:epis.entrega-modal />
</div>
