<div class="px-6 py-8 w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-500">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-blue-700/30 pb-10">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase leading-none">
                Gestão de <span class="text-yellow-500">Guias</span> de Transporte
            </h1>
            <p class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em] flex items-center gap-2">
                <span class="w-4 h-[2px] bg-yellow-500"></span>
                Controlo de Carga e Logística C016
            </p>
        </div>
        <button wire:click="openModal" class="bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black px-8 py-4 rounded-2xl shadow-xl shadow-yellow-500/20 transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-3 group">
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Nova Guia
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl flex items-center gap-4 animate-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filter tabs --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            'todas'    => ['label' => 'Todas',    'color' => 'bg-white/5 text-white/60 hover:bg-white/10'],
            'pendente' => ['label' => 'Pendentes', 'color' => 'bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20'],
            'emitida'  => ['label' => 'Emitidas',  'color' => 'bg-green-500/10 text-green-400 hover:bg-green-500/20'],
            'recusada' => ['label' => 'Recusadas', 'color' => 'bg-red-500/10 text-red-400 hover:bg-red-500/20'],
        ] as $key => $tab)
            <button wire:click="$set('filtroEstado', '{{ $key }}')"
                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border
                    {{ $filtroEstado === $key ? 'ring-2 ring-white/20 border-white/20' : 'border-transparent' }}
                    {{ $tab['color'] }}">
                {{ $tab['label'] }}
                @if($key !== 'todas')
                    <span class="ml-1 opacity-60">({{ $counts[$key] ?? 0 }})</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-blue-900/30 backdrop-blur-md rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-800/50">
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Tipo / Data</th>
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Origem</th>
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Destino / Matrícula</th>
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Itens</th>
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Estado</th>
                        <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-800/30">
                    @forelse($guias as $g)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $g->tipo === 'global' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">{{ $g->tipo }}</span>
                                @if($g->origem === 'colaborador')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-orange-500/20 text-orange-400">PWA</span>
                                @endif
                            </div>
                            <div class="text-sm font-black text-white italic">{{ $g->data_inicio?->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-black text-white/30 uppercase tracking-widest mt-1">{{ $g->hora_inicio }}</div>
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <div class="font-black text-white uppercase tracking-tight">{{ $g->local_carga_nome }}</div>
                            <div class="text-blue-400/60 text-xs font-medium mt-1 uppercase">{{ $g->local_carga_localidade }}</div>
                            @if($g->local_carga_morada)
                                <div class="text-white/30 text-[10px] mt-0.5">{{ $g->local_carga_morada }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <div class="font-black text-yellow-500 uppercase tracking-tight">{{ $g->matricula }}</div>
                            <div class="text-white/60 text-xs font-medium mt-1 uppercase">{{ $g->destino_localidade }}</div>
                            @if($g->destino_morada)
                                <div class="text-white/30 text-[10px] mt-0.5">{{ $g->destino_morada }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="space-y-1">
                                @foreach($g->items->take(2) as $item)
                                    <div class="text-[10px] text-white/60 font-medium">
                                        <span class="text-yellow-500 font-black">{{ $item->quantidade }} {{ $item->unidade }}</span> {{ $item->descricao }}
                                    </div>
                                @endforeach
                                @if($g->items->count() > 2)
                                    <div class="text-[9px] text-blue-400 font-black uppercase tracking-widest">+{{ $g->items->count() - 2 }} mais</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $estadoColor = match($g->estado) {
                                    'pendente' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/30',
                                    'emitida'  => 'bg-green-500/10 text-green-400 border-green-500/30',
                                    'recusada' => 'bg-red-500/10 text-red-400 border-red-500/30',
                                    default    => 'bg-white/5 text-white/40 border-white/10',
                                };
                            @endphp
                            <div class="inline-flex items-center px-3 py-1 rounded-full border {{ $estadoColor }} text-[8px] font-black uppercase tracking-widest">
                                {{ $g->estado }}
                            </div>
                            @if($g->numero_at)
                                <div class="text-[9px] font-black text-green-400/70 mt-1 uppercase tracking-widest">AT: {{ $g->numero_at }}</div>
                            @endif
                            @if($g->requerente)
                                <div class="text-[9px] text-orange-400/60 mt-1">{{ $g->requerente->nombre }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right space-x-4">
                            <button wire:click="editarGuia({{ $g->id }})" class="text-blue-400 hover:text-white transition-colors text-xs font-black uppercase tracking-widest">Editar</button>
                            <button wire:click="apagarGuia({{ $g->id }})" wire:confirm="Apagar esta guia?" class="text-red-400/50 hover:text-red-400 transition-colors text-xs font-black uppercase tracking-widest">Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="text-white/20 text-[10px] font-black uppercase tracking-[0.6em]">Sem guias registadas</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guias->hasPages())
        <div class="px-8 py-6 bg-blue-950/50 border-t border-blue-800/30">
            {{ $guias->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-10 bg-[#09143B]/90 backdrop-blur-xl animate-in fade-in duration-300">
        <div class="bg-blue-900/40 w-full max-w-6xl rounded-[3rem] border border-blue-700/50 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
            <div class="px-10 py-8 border-b border-blue-700/40 flex items-center justify-between bg-blue-800/20">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 bg-yellow-500 rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-500/20">
                        <svg class="w-6 h-6 text-[#09143B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 2v-6m-8-5h10a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-white tracking-tighter uppercase">{{ $guia_id ? 'Editar Guia' : 'Nova Guia de Transporte' }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-blue-400 font-bold text-[10px] uppercase tracking-widest">Documento de Transporte</span>
                            <div class="w-1.5 h-1.5 rounded-full bg-yellow-500"></div>
                            <span class="text-white/40 font-bold text-[10px] uppercase tracking-widest font-mono">ID: {{ $guia_id ?? 'AUTO' }}</span>
                        </div>
                    </div>
                </div>
                <button wire:click="closeModal" class="p-4 bg-white/5 hover:bg-white/10 rounded-2xl text-white/50 hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <form wire:submit.prevent="salvarGuia" class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {{-- Left column: transport data --}}
                    <div class="space-y-10">

                        {{-- Tipo + Matrícula --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Logística de Transporte
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Tipo de Guia</label>
                                    <div class="flex bg-white/5 p-1 rounded-xl border border-white/5">
                                        <button type="button" wire:click="$set('tipo', 'normal')" class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg transition-all {{ $tipo === 'normal' ? 'bg-blue-600 text-white shadow-lg' : 'text-white/30 hover:text-white/60' }}">Normal</button>
                                        <button type="button" wire:click="$set('tipo', 'global')" class="flex-1 py-2 text-[10px] font-black uppercase rounded-lg transition-all {{ $tipo === 'global' ? 'bg-purple-600 text-white shadow-lg' : 'text-white/30 hover:text-white/60' }}">Global</button>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Matrícula</label>
                                    <input type="text" wire:model="matricula" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white font-black text-sm focus:ring-yellow-500 focus:border-yellow-500 uppercase">
                                    @error('matricula') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Requester badge (PWA requests) --}}
                        @if($requerente_id)
                            @php $req = \App\Models\Colaborador::find($requerente_id); @endphp
                            <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex items-center gap-4">
                                <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center text-xs font-black text-white">{{ substr($req?->nombre ?? '?', 0, 1) }}</div>
                                <div>
                                    <div class="text-[9px] font-black text-orange-400 uppercase tracking-widest">Solicitado via PWA</div>
                                    <div class="text-sm font-black text-white">{{ $req?->nombre }} {{ $req?->apellido }}</div>
                                </div>
                                <span class="ml-auto px-2 py-0.5 bg-orange-500/20 rounded text-[8px] font-black text-orange-400 uppercase">Colaborador</span>
                            </div>
                        @endif

                        {{-- Origem --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Origem / Local de Carga
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Nome do Local</label>
                                        <input type="text" wire:model="local_carga_nome" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                        @error('local_carga_nome') <span class="text-[10px] text-red-400 font-bold">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Localidade</label>
                                        <input type="text" wire:model="local_carga_localidade" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2 space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Morada</label>
                                        <input type="text" wire:model="local_carga_morada" placeholder="Rua, nº..." class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">C. Postal</label>
                                        <input type="text" wire:model="local_carga_cpostal" placeholder="9200-047" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-1">
                                    <div class="space-y-2 border-l-2 border-blue-600 pl-4">
                                        <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Data Início</label>
                                        <input type="date" wire:model="data_inicio" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                        @error('data_inicio') <span class="text-[10px] text-red-400 font-bold">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Hora</label>
                                        <input type="time" wire:model="hora_inicio" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Destino --}}
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-blue-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Destino / Local de Descarga
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Nome do Local</label>
                                        <input type="text" wire:model="destino_nome" placeholder="Ex: Obra Santa Cruz" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Localidade</label>
                                        <input type="text" wire:model="destino_localidade" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="col-span-2 space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">Morada</label>
                                        <input type="text" wire:model="destino_morada" placeholder="Rua, nº..." class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-white/40 uppercase tracking-widest">C. Postal</label>
                                        <input type="text" wire:model="destino_cpostal" placeholder="9200-047" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-1">
                                    <div class="space-y-2 border-l-2 border-red-500 pl-4">
                                        <label class="text-[10px] font-black text-red-400 uppercase tracking-widest">Data Fim</label>
                                        <input type="date" wire:model="data_fim" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-red-400 uppercase tracking-widest">Hora</label>
                                        <input type="time" wire:model="hora_fim" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right column: items + AT --}}
                    <div class="space-y-6 bg-white/5 p-8 rounded-4xl border border-white/5 self-start">

                        {{-- Items --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-yellow-500 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                Bens / Materiais a Transportar
                            </h3>
                            <button type="button" wire:click="addItem" class="text-[10px] font-black text-blue-400 uppercase tracking-widest hover:text-white transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Adicionar
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($items as $index => $item)
                            <div class="p-5 bg-blue-950/50 rounded-2xl border border-blue-700/30 space-y-3 relative group/item">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">Descrição</label>
                                    <input type="text"
                                           wire:model="items.{{ $index }}.descricao"
                                           list="item-history"
                                           class="w-full bg-white/5 border-blue-700/30 rounded-xl text-white text-sm font-bold focus:ring-yellow-500 focus:border-yellow-500"
                                           placeholder="Ex: Cabo LXS 4x70+16">
                                    @error('items.'.$index.'.descricao') <span class="text-[9px] text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">QTD</label>
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.quantidade" class="w-full bg-white/5 border-blue-700/30 rounded-xl text-white text-sm font-bold">
                                        @error('items.'.$index.'.quantidade') <span class="text-[9px] text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">Unidade</label>
                                        <select wire:model="items.{{ $index }}.unidade" class="w-full bg-blue-900 border-blue-700/30 rounded-xl text-white text-sm font-bold">
                                            <option value="und">UND</option>
                                            <option value="mts">MTS</option>
                                            <option value="kgs">KGS</option>
                                            <option value="m2">M²</option>
                                            <option value="m3">M³</option>
                                            <option value="lts">LTS</option>
                                        </select>
                                    </div>
                                </div>
                                @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="absolute -top-2 -right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover/item:opacity-100 transition-all shadow-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <datalist id="item-history">
                            @foreach($itemSuggestions as $s)
                                <option value="{{ $s }}">
                            @endforeach
                        </datalist>

                        {{-- AT number --}}
                        <div class="pt-6 border-t border-white/5 space-y-4">
                            <h3 class="text-xs font-black text-green-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                Número AT (Autoridade Tributária)
                            </h3>
                            <div class="space-y-2">
                                <input type="text" wire:model="numero_at"
                                       placeholder="Ex: 18928526525"
                                       class="w-full bg-green-500/5 border-green-500/30 rounded-xl text-white font-black text-lg focus:ring-green-500 focus:border-green-500 tracking-widest placeholder:text-white/10 placeholder:font-normal">
                                <p class="text-[9px] text-white/40 font-medium leading-relaxed italic">
                                    Ao inserir o nº AT e guardar, a guia passa a <span class="text-green-400 font-bold uppercase">Emitida</span> e o colaborador é notificado.
                                </p>
                            </div>

                            @if($estado === 'pendente' && $guia_id)
                            <div class="pt-4 border-t border-white/5 space-y-3">
                                <label class="text-[10px] font-black text-red-400 uppercase tracking-widest">Recusar Pedido</label>
                                <textarea wire:model="motivo_recusa" placeholder="Motivo da recusa..." class="w-full bg-red-500/5 border-red-500/30 rounded-xl text-white text-sm focus:ring-red-500 focus:border-red-500" rows="2"></textarea>
                                <button type="button" wire:click="recusarGuia" class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/30 font-black py-3 rounded-xl transition-all text-[10px] uppercase tracking-[0.2em]">Recusar Guia</button>
                            </div>
                            @endif
                        </div>

                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black py-5 rounded-2xl shadow-2xl shadow-yellow-500/30 transition-all active:scale-95 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            {{ $guia_id ? 'Atualizar Guia' : 'Guardar Guia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59,130,246,.3); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59,130,246,.5); }
</style>
