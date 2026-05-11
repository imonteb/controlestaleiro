<div class="p-6">

    {{-- Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="bell" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Pedidos de EPI</span>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <flux:radio.group wire:model.live="filtro" variant="segmented">
                    <flux:radio value="pendente" label="Pendentes" />
                    <flux:radio value="aprovado" label="Aprovados" />
                    <flux:radio value="todos" label="Todos" />
                    <flux:radio value="rejeitado" label="Rejeitados" />
                </flux:radio.group>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Procurar..." icon="magnifying-glass" />
            </div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-xl flex items-center gap-3">
            <flux:icon.check-circle class="size-5" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Data</flux:table.column>
                <flux:table.column>Colaborador</flux:table.column>
                <flux:table.column>Item Solicitado</flux:table.column>
                <flux:table.column>Detalhes</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column align="end">Acções</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($pedidos as $pedido)
                    <flux:table.row>
                        <flux:table.cell class="text-xs text-gray-500">
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="size-8 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 rounded-lg flex items-center justify-center font-bold text-xs">
                                    {{ $pedido->colaborador->numero_colaborador }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ $pedido->colaborador->nombre }}</span>
                                    <span class="text-xs text-gray-500">{{ $pedido->colaborador->cargo }}</span>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span style="color:#09143B; font-weight:600;">{{ $pedido->epiItem->nombre }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-col gap-1">
                               @if($pedido->tamanho)
                                    <span class="badge-info w-fit">Tam: {{ $pedido->tamanho }}</span>
                               @endif
                               <span class="cme-muted italic text-xs">"{{ $pedido->motivo_colaborador ?? 'Sem motivo' }}"</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase border {{ $pedido->estado->badgeClass() }}">
                                {{ $pedido->estado->label() }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-2">
                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Pendente)
                                    <flux:button wire:click="aprovar({{ $pedido->id }})" variant="subtle" size="sm" icon="check" class="text-green-500">Aprovar</flux:button>
                                    <flux:button wire:click="rejeitar({{ $pedido->id }})" variant="subtle" size="sm" icon="x-mark" class="text-red-400">Rejeitar</flux:button>
                                @endif

                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Aprovado)
                                    <flux:button wire:click="prepararEntrega({{ $pedido->id }})" variant="primary" size="sm" icon="truck">Entregar Agora</flux:button>
                                    <flux:button wire:click="rejeitar({{ $pedido->id }})" variant="subtle" size="sm" icon="x-mark" class="text-red-400">Rejeitar</flux:button>
                                @endif

                                @if($pedido->estado === \App\Enums\EpiPedidoEstado::Rejeitado)
                                    <flux:tooltip content="Motivo: {{ $pedido->motivo_admin }}">
                                        <flux:icon.information-circle class="size-5 text-gray-500 cursor-help" />
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12 text-gray-500">
                             Nenhum pedido encontrado com este filtro.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="px-6 py-4 border-t" style="border-color:rgba(9,20,59,0.10);">
            {{ $pedidos->links() }}
        </div>
    </div>

    {{-- Modal Rejeição --}}
    @if($rejeitandoId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-2xl p-6 w-full max-w-sm shadow-2xl">
            <h3 class="text-xl font-bold mb-2" style="color:#1A1A1A;">Rejeitar Pedido</h3>
            <p class="cme-muted text-sm mb-4">Indique o motivo da rejeição para o colaborador.</p>

            <flux:textarea wire:model="motivoRejeicao" placeholder="Ex: Equipamento ainda em bom estado, stock esgotado..." class="mb-4" />

            <div class="flex gap-3">
                <flux:button wire:click="$set('rejeitandoId', null)" variant="subtle" class="flex-1">Cancelar</flux:button>
                <flux:button wire:click="confirmarRejeicao" variant="primary" color="red" class="flex-1">Rejeitar Pedido</flux:button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Entrega (pre-confirmação antes de criar) --}}
    @if($entregandoId && $pedidosBatch->isNotEmpty())
    @php $batchColaborador = $pedidosBatch->first()->colaborador; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:key="modal-pre-entrega">
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-2xl p-6 w-full max-w-md shadow-2xl flex flex-col gap-4">
            <div class="flex items-center gap-4">
                <div class="size-12 rounded-xl flex items-center justify-center shrink-0" style="background:#d4ede4; color:#0F6E56;">
                    <flux:icon.truck class="size-7" />
                </div>
                <div>
                    <h3 class="text-xl font-bold" style="color:#1A1A1A;">Confirmar Entrega</h3>
                    <p class="cme-muted text-sm">{{ $pedidosBatch->count() }} item(s) para {{ $batchColaborador->nombre }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                @foreach($pedidosBatch as $bp)
                <div class="flex items-center justify-between rounded-lg px-3 py-1.5" style="background:rgba(9,20,59,0.04);">
                    <span style="color:#09143B; font-weight:600;" class="text-sm">{{ $bp->epiItem->nombre }}</span>
                    @if($bp->tamanho)<span class="badge-info">{{ $bp->tamanho }}</span>@endif
                </div>
                @endforeach
            </div>
            <flux:textarea wire:model="observacoesEntrega" label="Observações Adicionais" placeholder="Alguma nota sobre a entrega..." />
            <div class="flex gap-3 pt-2 border-t" style="border-color:rgba(9,20,59,0.10);">
                <flux:button wire:click="cancelarEntrega" variant="subtle" class="flex-1">Cancelar</flux:button>
                <flux:button wire:click="confirmarEntrega" variant="primary" class="flex-1">Prosseguir</flux:button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal unificado de assinatura --}}
    <livewire:epis.entrega-modal />
</div>
