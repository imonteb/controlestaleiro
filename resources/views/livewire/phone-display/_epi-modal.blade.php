    <div class="flex-1 overflow-hidden flex flex-col bg-linear-[160deg] from-slate-900 to-[#162555]"
         x-data="{ epiTab: 'solicitar' }"
         x-on:epi-pedido-enviado.window="epiTab = 'pedidos'">

            {{-- HEADER --}}
            <div class="p-5 border-b border-white/8 shrink-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-yellow-300 text-xl font-black m-0 leading-none tracking-[0.01em]">EPI</h3>
                        <p class="text-white/90 text-xs mt-1 m-0">Equipamento de Proteção Individual</p>
                    </div>
                </div>

                {{-- TABS --}}
                <div class="flex gap-2.5 bg-black/30 p-1 rounded-xl mt-4">
                    <button type="button" @click="epiTab = 'solicitar'"
                        class="flex-1 py-2.5 rounded-[10px] border-none text-xs font-extrabold transition-all duration-200"
                        :class="epiTab === 'solicitar' ? 'bg-yellow-300 text-amber-950' : 'bg-transparent text-white/50'">
                        SOLICITAR
                        @if(count($epiCarrito) > 0)
                        <span class="ml-1 bg-emerald-500 text-white text-[0.55rem] font-black px-1.5 py-0.5 rounded-full">{{ count($epiCarrito) }}</span>
                        @endif
                    </button>
                    <button type="button" @click="epiTab = 'pedidos'"
                        class="flex-1 py-2.5 rounded-[10px] border-none text-xs font-extrabold transition-all duration-200 relative"
                        :class="epiTab === 'pedidos' ? 'bg-yellow-300 text-amber-950' : 'bg-transparent text-white/50'">
                        OS MEUS PEDIDOS
                        @if(isset($meusPedidos) && $meusPedidos->count() > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[0.55rem] font-black w-4 h-4 rounded-full flex items-center justify-center leading-none">{{ $meusPedidos->count() }}</span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- SUCCESS BANNER --}}
            <div id="epi-success-banner" wire:ignore style="display:none"
                 class="mx-4 mt-3 bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-4 py-3 flex items-center gap-2.5">
                <svg class="size-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-300 text-sm font-bold">Pedido enviado com sucesso!</span>
            </div>

            {{-- CONTENT AREA --}}
            <div class="flex-1 overflow-y-auto p-5 pb-6">

                {{-- TAB: SOLICITAR --}}
                <div x-show="epiTab === 'solicitar'" class="flex flex-col gap-4">

                    {{-- Seleção de item --}}
                    <div>
                        <label class="text-yellow-300/85 text-[0.6rem] font-black uppercase tracking-[0.08em] mb-1.5 block">Equipamento</label>
                        <div x-data="{
                                search: '',
                                open: false,
                                selectedName: 'Selecione um item...'
                             }"
                             x-init="
                                $watch('open', value => {
                                    if(value) { setTimeout(() => $refs.searchInput.focus(), 100); }
                                    else { search = ''; }
                                });
                                @if($selectedEpiId)
                                    @php $selItem = $epiItems->where('id', $selectedEpiId)->first(); @endphp
                                    selectedName = '{{ $selItem ? addslashes($selItem->nombre) : 'Selecione um item...' }}';
                                @endif
                             "
                             x-on:epi-item-adicionado.window="search = ''; selectedName = 'Selecione um item...'; open = false;"
                             class="relative w-full">

                            <button type="button" @click="open = !open"
                                class="w-full bg-white/8 border border-white/25 rounded-xl px-3.5 py-3 text-white text-[0.9rem] text-left flex justify-between items-center">
                                <span x-text="selectedName" class="truncate"></span>
                                <svg class="h-4 w-4 ml-2 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 x-transition
                                 class="absolute z-[9999] w-full mt-1 bg-[#243356] border border-yellow-300/30 rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.8)] flex flex-col max-h-75 overflow-hidden">

                                <div class="p-2.5 border-b border-yellow-300/15 bg-black/30">
                                    <input x-ref="searchInput" x-model="search" type="text" placeholder="Pesquisar EPI..."
                                           class="w-full bg-white/12 border border-white/30 rounded-lg px-3 py-2.5 text-white text-[0.9rem] outline-none">
                                </div>

                                <div class="overflow-y-auto flex-1 p-1.5 flex flex-col gap-0.5 max-h-60">
                                    @foreach($epiItems as $item)
                                        <div x-show="search === '' || '{{ mb_strtolower($item->nombre, 'UTF-8') }}'.normalize('NFD').replace(/[\u0300-\u036f]/g, '').includes(search.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''))"
                                             @click="$wire.set('selectedEpiId', {{ $item->id }}); selectedName = '{{ addslashes($item->nombre) }}'; open = false;"
                                             class="epi-dropdown-item px-3.5 py-2.5 text-[0.88rem] font-semibold cursor-pointer rounded-lg flex items-center justify-between bg-white/12 border border-white/20"
                                             :class="$wire.selectedEpiId == {{ $item->id }} ? 'bg-yellow-300/18! font-extrabold! border-yellow-300/50!' : 'hover:bg-white/20'">
                                            <span>{{ $item->nombre }}</span>
                                            @if($item->stock_total <= 0)
                                                <span class="text-[0.55rem] font-black uppercase px-1.5 py-0.5 rounded border text-orange-400 bg-orange-400/10 border-orange-400/30 shrink-0 ml-2">Sem stock</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($selectedEpiId)
                        @php $selectedItem = $epiItems->find($selectedEpiId); @endphp
                        @if($selectedItem && $selectedItem->stock_total <= 0)
                        <div class="bg-orange-500/15 border border-orange-500/30 rounded-xl px-3.5 py-3 flex items-start gap-2.5">
                            <svg class="h-4 w-4 text-orange-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            <div>
                                <div class="text-orange-300 text-[0.75rem] font-bold">Sem stock disponível</div>
                                <div class="text-orange-200 text-[0.65rem] mt-0.5">O seu pedido ficará em espera até que o stock seja reposto.</div>
                            </div>
                        </div>
                        @endif
                        @if($selectedItem && $selectedItem->requiere_talla)
                        <div>
                            <label class="text-yellow-300 text-[0.6rem] font-black uppercase tracking-[0.08em] mb-1.5 block">Tamanho / Variante</label>
                            <div class="relative">
                                <select wire:model="selectedTamanho" class="w-full bg-white/8 border border-white/25 rounded-xl px-3 py-3 text-white text-[0.85rem] appearance-none pr-10">
                                     <option value="" class="bg-slate-800 text-white">Selecione...</option>
                                     @foreach($selectedItem->tallas_disponibles ?? [] as $t)
                                        <option value="{{ $t }}" class="bg-slate-800 text-white">{{ $t }}</option>
                                     @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    <div>
                        <label class="text-yellow-300 text-[0.6rem] font-black uppercase tracking-[0.08em] mb-1.5 block">Quantidade</label>
                        <input wire:model="quantidadeEpi" type="number" min="1" class="w-full bg-white/8 border border-white/25 rounded-xl px-3 py-3 text-white text-base">
                    </div>

                    {{-- Botão adicionar ao carrito --}}
                    <button type="button" wire:click="adicionarAoCarrito"
                        class="w-full bg-white/10 border border-yellow-300/40 text-yellow-300 font-extrabold py-3 rounded-xl text-[0.85rem] uppercase tracking-[0.04em] flex items-center justify-center gap-2">
                        <span class="text-lg leading-none">+</span> ADICIONAR AO PEDIDO
                    </button>

                    {{-- Carrito (itens adicionados) --}}
                    @if(count($epiCarrito) > 0)
                    <div class="border-t border-white/10 pt-4 flex flex-col gap-2">
                        <div class="text-white/50 text-[0.6rem] font-black uppercase tracking-widest mb-1">Itens a solicitar</div>
                        @foreach($epiCarrito as $i => $item)
                        <div class="bg-white/6 border border-white/12 rounded-xl px-3 py-2.5 flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-white text-[0.8rem] font-bold truncate">{{ $item['nombre'] }}</div>
                                <div class="text-white/50 text-[0.65rem]">
                                    Qty: {{ $item['quantidade'] }}
                                    @if($item['tamanho']) · {{ $item['tamanho'] }} @endif
                                </div>
                            </div>
                            <button type="button" wire:click="removerDoCarrito({{ $i }})"
                                class="bg-red-500/15 border border-red-500/30 text-red-400 w-7 h-7 rounded-lg flex items-center justify-center text-sm shrink-0">✕</button>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Motivo + Enviar (sempre visíveis) --}}
                    <div class="border-t border-white/10 pt-4 flex flex-col gap-3 mt-1">
                        <div>
                            <label class="text-yellow-300/85 text-[0.6rem] font-black uppercase tracking-[0.08em] mb-1.5 block">Motivo (Opcional)</label>
                            <textarea wire:model="motivoPedido" placeholder="Ex: Desgaste, furto, nova contratação..." class="w-full bg-white/8 border border-white/25 rounded-xl px-3 py-3 text-white text-[0.9rem] min-h-20"></textarea>
                        </div>

                        <button type="button" wire:click="enviarPedidoEpi"
                            class="bg-emerald-500 text-white font-black py-3.5 rounded-xl border-none text-base tracking-[0.03em] uppercase shadow-[0_4px_15px_rgba(16,185,129,0.3)]">
                            @if(count($epiCarrito) > 1)
                                ENVIAR {{ count($epiCarrito) }} PEDIDOS
                            @else
                                ENVIAR PEDIDO
                            @endif
                        </button>
                    </div>

                </div>

                {{-- TAB: OS MEUS PEDIDOS --}}
                <div x-show="epiTab === 'pedidos'">
                    @if(isset($meusPedidos) && $meusPedidos->count() > 0)
                    <div class="flex flex-col gap-2.5">
                        @foreach($meusPedidos as $p)
                        @php
                            $stClasses = match($p->estado) {
                                'pendente'   => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
                                'sem_stock'  => 'text-orange-400 bg-orange-400/10 border-orange-400/30',
                                'aprovado'   => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
                                'entregue'   => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
                                'rejeitado'  => 'text-red-400 bg-red-400/10 border-red-400/30',
                                default      => 'text-gray-400 bg-gray-400/10 border-gray-400/30',
                            };
                            $stLabel = match($p->estado) {
                                'pendente'   => 'Pendente',
                                'sem_stock'  => 'Sem Stock',
                                'aprovado'   => 'Aprovado',
                                'entregue'   => 'Entregue',
                                'rejeitado'  => 'Rejeitado',
                                default      => $p->estado,
                            };
                        @endphp
                        <div class="bg-white/6 border border-white/14 p-3 rounded-2xl">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex-1">
                                    <div class="text-white text-[0.8rem] font-bold">{{ $p->epiItem->nombre }}</div>
                                    <div class="text-white/90 text-[0.65rem]">{{ $p->created_at->format('d/m H:i') }} @if($p->tamanho) — Tam: {{ $p->tamanho }} @endif</div>
                                </div>
                                <div class="text-[0.6rem] font-black uppercase px-1.5 py-0.5 rounded border {{ $stClasses }}">
                                    {{ $stLabel }}
                                </div>
                            </div>
                            @if($p->estado === 'rejeitado' && $p->motivo_admin)
                                <div class="bg-red-500/12 px-2 py-2 rounded-lg text-[0.7rem] text-red-300 mt-2 border-l-2 border-red-500">
                                    <strong>Motivo:</strong> {{ $p->motivo_admin }}
                                </div>
                            @endif
                            @if($p->estado === 'aprovado')
                                <div class="bg-blue-500/12 px-2 py-2 rounded-lg text-[0.7rem] text-blue-300 mt-2 border-l-2 border-blue-500">
                                    Pode levantar o equipamento no armazém.
                                </div>
                            @endif
                            @if($p->estado === 'sem_stock')
                                <div class="bg-orange-500/12 px-2 py-2 rounded-lg text-[0.7rem] text-orange-300 mt-2 border-l-2 border-orange-500">
                                    Aguarda reposição de stock. Será processado automaticamente.
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-white/30 py-16 text-[0.85rem]">Sem pedidos recentes.</p>
                    @endif
                </div>

            </div>
    </div>
