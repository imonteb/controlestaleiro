<div class="px-6 py-8 w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-500">
    {{-- Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="truck" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Gestão de Guias de Transporte</span>
                <span class="text-[10px] bg-[rgba(255,211,0,0.15)] text-[#FFD300] px-2 py-1 rounded font-medium tracking-wide">
                    Controlo de Carga e Logística C016
                </span>
            </div>
            <button wire:click="openModal"
                style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; white-space:nowrap; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                + Nova Guia
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="badge-ok px-6 py-4 rounded-xl flex items-center gap-4 mb-4">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filter tabs --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            'todas'    => ['label' => 'Todas',    'bg' => '#E4E2DF', 'color' => '#09143B', 'border' => 'rgba(9,20,59,0.2)'],
            'pendente' => ['label' => 'Pendentes', 'bg' => '#09143B', 'color' => '#FFD300', 'border' => '#09143B'],
            'emitida'  => ['label' => 'Emitidas',  'bg' => '#0F6E56', 'color' => '#ffffff', 'border' => '#0F6E56'],
            'recusada' => ['label' => 'Recusadas', 'bg' => '#A32D2D', 'color' => '#ffffff', 'border' => '#A32D2D'],
        ] as $key => $tab)
            <button wire:click="$set('filtroEstado', '{{ $key }}')"
                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border"
                style="background:{{ $tab['bg'] }}; color:{{ $tab['color'] }}; border-color:{{ $tab['border'] }};
                    {{ $filtroEstado === $key ? 'outline:2px solid #09143B; outline-offset:2px;' : '' }}">
                {{ $tab['label'] }}
                @if($key !== 'todas')
                    <span class="ml-1 opacity-60">({{ $counts[$key] ?? 0 }})</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-[rgba(9,20,59,0.14)] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background:#E4E2DF;">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Tipo / Data</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Origem</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Destino / Matrícula</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Itens</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Estado</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-right" style="color:#4A4845;">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($guias as $g)
                    <tr class="hover:bg-[#F0EEEB] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $g->tipo === 'global' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">{{ $g->tipo }}</span>
                                @if($g->origem === 'colaborador')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-orange-500/20 text-orange-400">PWA</span>
                                @endif
                            </div>
                            <div class="text-sm font-black italic" style="color:#1A1A1A;">{{ $g->data_inicio?->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-black uppercase tracking-widest mt-1" style="color:#7A7775;">{{ $g->hora_inicio }}</div>
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <div class="font-black uppercase tracking-tight" style="color:#1A1A1A;">{{ $g->local_carga_nome }}</div>
                            <div class="text-xs font-medium mt-1 uppercase" style="color:#7A7775;">{{ $g->local_carga_localidade }}</div>
                            @if($g->local_carga_morada)
                                <div class="text-[10px] mt-0.5" style="color:#7A7775;">{{ $g->local_carga_morada }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <div class="font-black uppercase tracking-tight" style="color:#09143B; font-weight:700;">{{ $g->matricula }}</div>
                            <div class="text-xs font-medium mt-1 uppercase" style="color:#4A4845;">{{ $g->destino_localidade }}</div>
                            @if($g->destino_morada)
                                <div class="text-[10px] mt-0.5" style="color:#7A7775;">{{ $g->destino_morada }}</div>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="space-y-1">
                                @foreach($g->items->take(2) as $item)
                                    <div class="text-[10px] font-medium" style="color:#4A4845;">
                                        <span class="font-black" style="color:#09143B; font-weight:700;">{{ $item->quantidade }} {{ $item->unidade }}</span> {{ $item->descricao }}
                                    </div>
                                @endforeach
                                @if($g->items->count() > 2)
                                    <div class="text-[9px] font-black uppercase tracking-widest" style="color:#09143B;">+{{ $g->items->count() - 2 }} mais</div>
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
                            <button wire:click="editarGuia({{ $g->id }})"
                                    class="transition-colors text-xs font-black uppercase tracking-widest"
                                    style="{{ $g->origem === 'colaborador' ? 'color:#854F0B;' : 'color:#09143B;' }}">
                                {{ $g->origem === 'colaborador' ? 'Ver Pedido' : 'Editar' }}
                            </button>
                            <button wire:click="apagarGuia({{ $g->id }})" wire:confirm="Apagar esta guia?" class="transition-colors text-xs font-black uppercase tracking-widest" style="color:#A32D2D;">Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="text-[10px] font-black uppercase tracking-[0.6em]" style="color:#7A7775;">Sem guias registadas</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guias->hasPages())
        <div class="px-8 py-6 border-t" style="background:#F0EEEB; border-color:rgba(9,20,59,0.10);">
            {{ $guias->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MODAL
    ════════════════════════════════════════════════════════════ --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-10 bg-[#09143B]/90 backdrop-blur-xl animate-in fade-in duration-300">
        <div class="bg-blue-900/40 w-full max-w-6xl rounded-[3rem] border border-blue-700/50 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">

            {{-- Modal Header --}}
            <div class="px-10 py-8 border-b border-blue-700/40 flex items-center justify-between bg-blue-800/20 shrink-0">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg
                                {{ $modalMode === 'view' ? 'bg-orange-500 shadow-orange-500/20' : 'bg-yellow-500 shadow-yellow-500/20' }}">
                        @if($modalMode === 'view')
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @else
                            <svg class="w-6 h-6 text-[#09143B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 2v-6m-8-5h10a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" /></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-white tracking-tighter uppercase">
                            @if($modalMode === 'view') Pedido via PWA
                            @elseif($guia_id) Editar Guia
                            @else Nova Guia de Transporte
                            @endif
                        </h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-blue-400 font-bold text-[10px] uppercase tracking-widest">
                                {{ $modalMode === 'view' ? 'Solicitação de colaborador' : 'Documento de Transporte' }}
                            </span>
                            <div class="w-1.5 h-1.5 rounded-full bg-yellow-500"></div>
                            <span class="text-white/40 font-bold text-[10px] uppercase tracking-widest font-mono">ID: {{ $guia_id ?? 'AUTO' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($modalMode === 'view')
                        <button wire:click="switchToEdit"
                                class="px-4 py-2 bg-blue-700/40 hover:bg-blue-600/50 text-blue-300 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-blue-600/30">
                            Editar Pedido
                        </button>
                    @endif
                    <button wire:click="closeModal" class="p-4 bg-white/5 hover:bg-white/10 rounded-2xl text-white/50 hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">

                {{-- ─────────────────────────────────────────────
                     VIEW MODE — Pedido de colaborador (read-only)
                ───────────────────────────────────────────────── --}}
                @if($modalMode === 'view')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {{-- Left: all data as labels --}}
                    <div class="space-y-8">

                        {{-- Requerente --}}
                        @if($requerente_id)
                            @php $req = \App\Models\Colaborador::find($requerente_id); @endphp
                            <div class="p-5 bg-orange-500/10 border border-orange-500/30 rounded-2xl flex items-center gap-4">
                                <div class="w-11 h-11 bg-orange-500 rounded-xl flex items-center justify-center text-sm font-black text-white shrink-0">
                                    {{ substr($req?->nombre ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-[9px] font-black text-orange-400 uppercase tracking-widest mb-0.5">Solicitado via PWA</div>
                                    <div class="text-white font-black">{{ $req?->nombre }} {{ $req?->apellido }}</div>
                                </div>
                                <span class="px-2 py-0.5 bg-orange-500/20 rounded text-[8px] font-black text-orange-400 uppercase shrink-0">Colaborador</span>
                            </div>
                        @endif

                        {{-- Tipo + Matrícula --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1.5">Tipo de Guia</div>
                                <span class="inline-flex px-3 py-1.5 rounded-lg text-xs font-black uppercase {{ $tipo === 'global' ? 'bg-purple-600/30 text-purple-300' : 'bg-blue-600/30 text-blue-300' }}">{{ $tipo }}</span>
                            </div>
                            <div>
                                <div class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1.5">Matrícula</div>
                                <div class="text-yellow-400 font-black text-lg tracking-widest">{{ $matricula }}</div>
                            </div>
                        </div>

                        {{-- Origem --}}
                        <div class="bg-green-500/5 border border-green-500/20 rounded-2xl p-5 space-y-3">
                            <div class="text-[9px] font-black text-green-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Origem / Local de Carga
                            </div>
                            <div class="text-white font-black">{{ $local_carga_nome }}</div>
                            @if($local_carga_morada)
                                <div class="text-white/60 text-sm">{{ $local_carga_morada }}</div>
                            @endif
                            <div class="flex gap-4 text-sm text-white/50">
                                @if($local_carga_localidade)<span>{{ $local_carga_localidade }}</span>@endif
                                @if($local_carga_cpostal)<span>{{ $local_carga_cpostal }}</span>@endif
                            </div>
                            <div class="pt-2 border-t border-green-500/10 flex gap-6 text-xs text-green-300/70 font-bold">
                                <span>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</span>
                                <span>{{ $hora_inicio }}</span>
                            </div>
                        </div>

                        {{-- Destino --}}
                        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5 space-y-3">
                            <div class="text-[9px] font-black text-red-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Destino / Local de Descarga
                            </div>
                            @if($destino_nome)
                                <div class="text-white font-black">{{ $destino_nome }}</div>
                            @else
                                <div class="text-white/20 text-sm italic">Sem destino especificado</div>
                            @endif
                            @if($destino_morada)
                                <div class="text-white/60 text-sm">{{ $destino_morada }}</div>
                            @endif
                            <div class="flex gap-4 text-sm text-white/50">
                                @if($destino_localidade)<span>{{ $destino_localidade }}</span>@endif
                                @if($destino_cpostal)<span>{{ $destino_cpostal }}</span>@endif
                            </div>
                            @if($data_fim)
                            <div class="pt-2 border-t border-red-500/10 flex gap-6 text-xs text-red-300/70 font-bold">
                                <span>{{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}</span>
                                @if($hora_fim)<span>{{ $hora_fim }}</span>@endif
                            </div>
                            @endif
                        </div>

                        {{-- Itens --}}
                        <div class="bg-yellow-500/5 border border-yellow-500/20 rounded-2xl p-5 space-y-3">
                            <div class="text-[9px] font-black text-yellow-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Bens a Transportar
                            </div>
                            <div class="space-y-2">
                                @foreach($items as $item)
                                <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                                    <span class="text-white text-sm">{{ $item['descricao'] }}</span>
                                    <span class="text-yellow-400 font-black text-sm shrink-0 ml-4">{{ $item['quantidade'] }} {{ $item['unidade'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Right: action panel --}}
                    <div class="space-y-6 bg-white/5 p-8 rounded-4xl border border-white/5 self-start">

                        {{-- Status badge --}}
                        @php
                            $badgeClass = match($estado) {
                                'pendente' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
                                'emitida'  => 'bg-green-500/10 text-green-400 border-green-500/30',
                                'recusada' => 'bg-red-500/10 text-red-400 border-red-500/30',
                                default    => 'bg-white/5 text-white/40 border-white/10',
                            };
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-white/30 uppercase tracking-widest">Estado atual</span>
                            <span class="px-3 py-1.5 rounded-full border {{ $badgeClass }} text-[9px] font-black uppercase tracking-widest">{{ $estado }}</span>
                        </div>

                        @if($estado === 'emitida')
                            <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-2xl text-center">
                                <div class="text-[9px] font-black text-green-400 uppercase tracking-widest mb-1">Nº AT Emitido</div>
                                <div class="text-white font-black text-xl tracking-widest">{{ $numero_at }}</div>
                            </div>
                        @endif

                        @if($estado === 'recusada')
                            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-2xl">
                                <div class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">Motivo da Recusa</div>
                                <div class="text-white/80 text-sm">{{ $motivo_recusa }}</div>
                            </div>
                        @endif

                        @if($estado === 'pendente')
                        {{-- Emitir --}}
                        <div class="space-y-3">
                            <h3 class="text-xs font-black text-green-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                Número AT (Autoridade Tributária)
                            </h3>
                            <input type="text" wire:model="numero_at"
                                   placeholder="Ex: 18928526525"
                                   class="w-full bg-green-500/5 border-green-500/30 rounded-xl text-white font-black text-lg focus:ring-green-500 focus:border-green-500 tracking-widest placeholder:text-white/10 placeholder:font-normal">
                            @error('numero_at') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                            <button wire:click="emitirGuia"
                                    class="w-full bg-green-500 hover:bg-green-400 text-white font-black py-4 rounded-2xl shadow-lg transition-all active:scale-95 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                Emitir Guia
                            </button>
                        </div>

                        {{-- Recusar --}}
                        <div class="pt-4 border-t border-white/5 space-y-3">
                            <label class="text-[10px] font-black text-red-400 uppercase tracking-widest">Recusar Pedido</label>
                            <textarea wire:model="motivo_recusa" placeholder="Motivo da recusa..."
                                      class="w-full bg-red-500/5 border-red-500/30 rounded-xl text-white text-sm focus:ring-red-500 focus:border-red-500" rows="3"></textarea>
                            @error('motivo_recusa') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                            <button wire:click="recusarGuia"
                                    class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/30 font-black py-3 rounded-xl transition-all text-[10px] uppercase tracking-[0.2em]">
                                Recusar Guia
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ─────────────────────────────────────────────
                     EDIT MODE — Nova guia / Editar
                ───────────────────────────────────────────────── --}}
                @else
                <form wire:submit.prevent="salvarGuia" class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {{-- Left column: transport data --}}
                    <div class="space-y-8">

                        {{-- Tipo + Matrícula --}}
                        <div class="space-y-5">
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

                        {{-- Requerente badge (PWA) --}}
                        @if($requerente_id)
                            @php $req = \App\Models\Colaborador::find($requerente_id); @endphp
                            <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex items-center gap-4">
                                <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center text-xs font-black text-white shrink-0">{{ substr($req?->nombre ?? '?', 0, 1) }}</div>
                                <div>
                                    <div class="text-[9px] font-black text-orange-400 uppercase tracking-widest">Solicitado via PWA</div>
                                    <div class="text-sm font-black text-white">{{ $req?->nombre }} {{ $req?->apellido }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- ── ORIGEM ─────────────────────────────── --}}
                        <div class="bg-green-500/5 border border-green-500/20 rounded-2xl p-5 space-y-4">
                            <div class="text-[9px] font-black text-green-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Origem / Local de Carga
                            </div>

                            @if($originTab === 'confirmado')
                                <div class="flex items-center justify-between bg-green-500/10 border border-green-500/30 rounded-xl px-4 py-3">
                                    <div>
                                        <div class="text-white font-black text-sm">{{ $local_carga_nome }}</div>
                                        @if($local_carga_morada || $local_carga_localidade)
                                        <div class="text-green-300/60 text-xs mt-0.5">
                                            {{ implode(' · ', array_filter([$local_carga_morada, $local_carga_localidade, $local_carga_cpostal])) }}
                                        </div>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="alterarOrigem"
                                            class="text-white/40 hover:text-white text-[10px] font-black border border-white/15 rounded-lg px-3 py-1.5 shrink-0 transition-colors">
                                        Alterar
                                    </button>
                                </div>
                            @else
                                {{-- Tabs de modo --}}
                                <div class="flex bg-black/20 p-1 rounded-xl gap-1">
                                    @foreach(['frequente' => '⭐ Frequente', 'pesquisa' => '🔍 Pesquisa CT', 'manual' => '✏️ Manual'] as $m => $label)
                                    <button type="button" wire:click="$set('originModo', '{{ $m }}')"
                                            class="flex-1 py-2 rounded-lg text-[10px] font-black border-none transition-colors
                                                   {{ $originModo === $m ? 'bg-green-500/30 text-green-300' : 'bg-transparent text-white/40 hover:text-white/70' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>

                                {{-- Frequente --}}
                                @if($originModo === 'frequente')
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                                    @forelse($locaisFrequentes as $lf)
                                    <button type="button" wire:click="selecionarLocalCarga({{ $lf['id'] }})"
                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-green-500/15 border border-white/8 hover:border-green-500/30 rounded-xl px-4 py-2.5 transition-colors group">
                                        <div>
                                            <div class="text-white text-sm font-semibold">{{ $lf['nome'] }}</div>
                                            @if($lf['localidade'])<div class="text-white/35 text-xs">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>@endif
                                        </div>
                                        <span class="text-white/20 group-hover:text-green-400 text-xs ml-2 shrink-0">→</span>
                                    </button>
                                    @empty
                                    <p class="text-white/30 text-xs text-center py-4">Sem locais frequentes registados.</p>
                                    @endforelse
                                </div>
                                @endif

                                {{-- Pesquisa CT (Nominatim) --}}
                                @if($originModo === 'pesquisa')
                                <div class="space-y-3">
                                    {{-- Distrito --}}
                                    @if($originDD)
                                    <div class="flex items-center justify-between px-3 py-2 bg-green-500/10 border border-green-500/25 rounded-xl">
                                        <div>
                                            <div class="text-white/30 text-[9px] font-black uppercase">Distrito</div>
                                            <div class="text-green-200 text-sm font-bold">{{ $distritos->firstWhere('dd', $originDD)?->desig }}</div>
                                        </div>
                                        <button type="button" wire:click="$set('originDD', '')" class="text-white/30 hover:text-white/60 text-xs border border-white/10 rounded-lg px-2 py-1">✕</button>
                                    </div>
                                    @else
                                    <div x-data="{
                                        open: false, search: '',
                                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                        get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                    }">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between bg-white/5 hover:bg-green-500/8 border border-white/10 hover:border-green-500/30 rounded-xl px-4 py-2.5 transition-colors">
                                            <span class="text-white/40 text-sm" x-text="open ? 'Distrito' : 'Selecionar Distrito'"></span>
                                            <span class="text-white/25 text-xs" x-text="open ? '▲' : '▼'"></span>
                                        </button>
                                        <div x-show="open" x-transition.duration.150ms class="mt-1.5">
                                            <input type="text" x-model="search" placeholder="Filtrar..."
                                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-white/20 focus:outline-none focus:border-green-500/50 mb-1.5">
                                            <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                                <template x-for="d in filtered" :key="d.dd">
                                                    <button type="button" @click="$wire.set('originDD', d.dd); open = false"
                                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-green-500/15 border border-white/8 hover:border-green-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                        <span class="text-white text-sm font-semibold" x-text="d.desig"></span>
                                                        <span class="text-white/20 group-hover:text-green-400 text-xs shrink-0">→</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Concelho --}}
                                    @if($originDD && count($concelhos))
                                        @if($originCC)
                                        <div class="flex items-center justify-between px-3 py-2 bg-green-500/10 border border-green-500/25 rounded-xl">
                                            <div>
                                                <div class="text-white/30 text-[9px] font-black uppercase">Concelho</div>
                                                <div class="text-green-200 text-sm font-bold">{{ $concelhos->firstWhere('cc', $originCC)?->desig }}</div>
                                            </div>
                                            <button type="button" wire:click="$set('originCC', '')" class="text-white/30 hover:text-white/60 text-xs border border-white/10 rounded-lg px-2 py-1">✕</button>
                                        </div>
                                        @else
                                        <div x-data="{
                                            open: true, search: '',
                                            items: @js($concelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                            get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                        }">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center justify-between bg-white/5 hover:bg-green-500/8 border border-white/10 hover:border-green-500/30 rounded-xl px-4 py-2.5 transition-colors">
                                                <span class="text-white/40 text-sm" x-text="open ? 'Concelho' : 'Selecionar Concelho'"></span>
                                                <span class="text-white/25 text-xs" x-text="open ? '▲' : '▼'"></span>
                                            </button>
                                            <div x-show="open" x-transition.duration.150ms class="mt-1.5">
                                                <input type="text" x-model="search" placeholder="Filtrar..."
                                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-white/20 focus:outline-none focus:border-green-500/50 mb-1.5">
                                                <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                                    <template x-for="c in filtered" :key="c.cc">
                                                        <button type="button" @click="$wire.set('originCC', c.cc); open = false"
                                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-green-500/15 border border-white/8 hover:border-green-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                            <span class="text-white text-sm font-semibold" x-text="c.desig"></span>
                                                            <span class="text-white/20 group-hover:text-green-400 text-xs shrink-0">→</span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    {{-- Rua Nominatim --}}
                                    @if($originCC)
                                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                                        <div class="flex gap-2 mb-2">
                                            <input type="text" x-model="q"
                                                   @keydown.enter.prevent="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuas',q).then(r=>{res=r;loading=false;})}"
                                                   placeholder="Ex: Caminho da Levada..."
                                                   class="flex-1 bg-black/20 border border-white/10 rounded-xl px-3 py-2.5 text-white text-sm placeholder-white/20 focus:outline-none focus:border-green-500/50"
                                                   autocomplete="off">
                                            <button type="button"
                                                    @click="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuas',q).then(r=>{res=r;loading=false;})}"
                                                    :disabled="q.length<3||loading"
                                                    class="bg-green-500/20 hover:bg-green-500/30 disabled:opacity-30 text-green-300 border border-green-500/40 rounded-xl px-4 py-2.5 text-sm font-bold transition-colors shrink-0">
                                                <span x-show="!loading">🔍</span>
                                                <span x-show="loading" class="animate-pulse">⟳</span>
                                            </button>
                                        </div>
                                        <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                            <template x-for="r in res" :key="r.road">
                                                <button type="button"
                                                        @click="$wire.call('selecionarRuaOrigem', r.road, r.localidade??'', r.postcode??'')"
                                                        class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-green-500/15 border border-white/8 hover:border-green-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                    <div>
                                                        <div class="text-white text-sm font-semibold" x-text="r.road"></div>
                                                        <div class="text-white/35 text-xs" x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')"></div>
                                                    </div>
                                                    <span class="text-white/20 group-hover:text-green-400 text-xs ml-2 shrink-0">→</span>
                                                </button>
                                            </template>
                                            <template x-if="searched && !loading && res.length===0">
                                                <div class="text-white/25 text-xs text-center py-3">Sem resultados</div>
                                            </template>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                {{-- Manual --}}
                                @if($originModo === 'manual')
                                <div class="space-y-3">
                                    <input type="text" wire:model.blur="local_carga_nome" placeholder="Nome do local *"
                                           class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    @error('local_carga_nome') <span class="text-[10px] text-red-400 font-bold">{{ $message }}</span> @enderror
                                    <input type="text" wire:model.blur="local_carga_morada" placeholder="Morada / Rua"
                                           class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" wire:model.blur="local_carga_localidade" placeholder="Localidade"
                                               class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                        <input type="text" wire:model.blur="local_carga_cpostal" placeholder="Cód. Postal"
                                               class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                    <button type="button" wire:click="$set('originTab', 'confirmado')"
                                            class="w-full bg-green-500/10 hover:bg-green-500/20 text-green-300 border border-green-500/30 rounded-xl py-2.5 text-[10px] font-black uppercase tracking-widest transition-colors">
                                        Confirmar Origem
                                    </button>
                                </div>
                                @endif
                            @endif

                            {{-- Data/Hora carga --}}
                            <div class="grid grid-cols-2 gap-4 pt-1">
                                <div class="space-y-2 border-l-2 border-green-500 pl-3">
                                    <label class="text-[9px] font-black text-green-400 uppercase tracking-widest">Data Início *</label>
                                    <input type="date" wire:model="data_inicio" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                    @error('data_inicio') <span class="text-[10px] text-red-400 font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-green-400 uppercase tracking-widest">Hora *</label>
                                    <input type="time" wire:model="hora_inicio" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                </div>
                            </div>
                        </div>

                        {{-- ── DESTINO ─────────────────────────────── --}}
                        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5 space-y-4">
                            <div class="text-[9px] font-black text-red-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Destino / Local de Descarga
                            </div>

                            @if($destinoTab === 'confirmado')
                                <div class="flex items-center justify-between bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
                                    <div>
                                        <div class="text-white font-black text-sm">{{ $destino_nome ?: '(sem nome)' }}</div>
                                        @if($destino_morada || $destino_localidade)
                                        <div class="text-red-300/60 text-xs mt-0.5">
                                            {{ implode(' · ', array_filter([$destino_morada, $destino_localidade, $destino_cpostal])) }}
                                        </div>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="alterarDestino"
                                            class="text-white/40 hover:text-white text-[10px] font-black border border-white/15 rounded-lg px-3 py-1.5 shrink-0 transition-colors">
                                        Alterar
                                    </button>
                                </div>
                            @else
                                {{-- Tabs de modo --}}
                                <div class="flex bg-black/20 p-1 rounded-xl gap-1">
                                    @foreach(['frequente' => '⭐ Frequente', 'pesquisa' => '🔍 Pesquisa CT', 'manual' => '✏️ Manual'] as $m => $label)
                                    <button type="button" wire:click="$set('destinoModo', '{{ $m }}')"
                                            class="flex-1 py-2 rounded-lg text-[10px] font-black border-none transition-colors
                                                   {{ $destinoModo === $m ? 'bg-red-500/30 text-red-300' : 'bg-transparent text-white/40 hover:text-white/70' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>

                                {{-- Frequente --}}
                                @if($destinoModo === 'frequente')
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                                    @forelse($locaisFrequentes as $lf)
                                    <button type="button" wire:click="selecionarLocalDestino({{ $lf['id'] }})"
                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-4 py-2.5 transition-colors group">
                                        <div>
                                            <div class="text-white text-sm font-semibold">{{ $lf['nome'] }}</div>
                                            @if($lf['localidade'])<div class="text-white/35 text-xs">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>@endif
                                        </div>
                                        <span class="text-white/20 group-hover:text-red-400 text-xs ml-2 shrink-0">→</span>
                                    </button>
                                    @empty
                                    <p class="text-white/30 text-xs text-center py-4">Sem locais frequentes registados.</p>
                                    @endforelse
                                </div>
                                @endif

                                {{-- Pesquisa CT (Nominatim) --}}
                                @if($destinoModo === 'pesquisa')
                                <div class="space-y-3">
                                    {{-- Distrito --}}
                                    @if($destinoDD)
                                    <div class="flex items-center justify-between px-3 py-2 bg-red-500/10 border border-red-500/25 rounded-xl">
                                        <div>
                                            <div class="text-white/30 text-[9px] font-black uppercase">Distrito</div>
                                            <div class="text-red-200 text-sm font-bold">{{ $distritos->firstWhere('dd', $destinoDD)?->desig }}</div>
                                        </div>
                                        <button type="button" wire:click="$set('destinoDD', '')" class="text-white/30 hover:text-white/60 text-xs border border-white/10 rounded-lg px-2 py-1">✕</button>
                                    </div>
                                    @else
                                    <div x-data="{
                                        open: false, search: '',
                                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                        get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                    }">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between bg-white/5 hover:bg-red-500/8 border border-white/10 hover:border-red-500/30 rounded-xl px-4 py-2.5 transition-colors">
                                            <span class="text-white/40 text-sm" x-text="open ? 'Distrito' : 'Selecionar Distrito'"></span>
                                            <span class="text-white/25 text-xs" x-text="open ? '▲' : '▼'"></span>
                                        </button>
                                        <div x-show="open" x-transition.duration.150ms class="mt-1.5">
                                            <input type="text" x-model="search" placeholder="Filtrar..."
                                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-white/20 focus:outline-none focus:border-red-500/50 mb-1.5">
                                            <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                                <template x-for="d in filtered" :key="d.dd">
                                                    <button type="button" @click="$wire.set('destinoDD', d.dd); open = false"
                                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                        <span class="text-white text-sm font-semibold" x-text="d.desig"></span>
                                                        <span class="text-white/20 group-hover:text-red-400 text-xs shrink-0">→</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Concelho --}}
                                    @if($destinoDD && count($destinoConcelhos))
                                        @if($destinoCC)
                                        <div class="flex items-center justify-between px-3 py-2 bg-red-500/10 border border-red-500/25 rounded-xl">
                                            <div>
                                                <div class="text-white/30 text-[9px] font-black uppercase">Concelho</div>
                                                <div class="text-red-200 text-sm font-bold">{{ $destinoConcelhos->firstWhere('cc', $destinoCC)?->desig }}</div>
                                            </div>
                                            <button type="button" wire:click="$set('destinoCC', '')" class="text-white/30 hover:text-white/60 text-xs border border-white/10 rounded-lg px-2 py-1">✕</button>
                                        </div>
                                        @else
                                        <div x-data="{
                                            open: true, search: '',
                                            items: @js($destinoConcelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                            get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                        }">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center justify-between bg-white/5 hover:bg-red-500/8 border border-white/10 hover:border-red-500/30 rounded-xl px-4 py-2.5 transition-colors">
                                                <span class="text-white/40 text-sm" x-text="open ? 'Concelho' : 'Selecionar Concelho'"></span>
                                                <span class="text-white/25 text-xs" x-text="open ? '▲' : '▼'"></span>
                                            </button>
                                            <div x-show="open" x-transition.duration.150ms class="mt-1.5">
                                                <input type="text" x-model="search" placeholder="Filtrar..."
                                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-white text-sm placeholder-white/20 focus:outline-none focus:border-red-500/50 mb-1.5">
                                                <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                                    <template x-for="c in filtered" :key="c.cc">
                                                        <button type="button" @click="$wire.set('destinoCC', c.cc); open = false"
                                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                            <span class="text-white text-sm font-semibold" x-text="c.desig"></span>
                                                            <span class="text-white/20 group-hover:text-red-400 text-xs shrink-0">→</span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    {{-- Rua Nominatim --}}
                                    @if($destinoCC)
                                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                                        <div class="flex gap-2 mb-2">
                                            <input type="text" x-model="q"
                                                   @keydown.enter.prevent="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuasDestino',q).then(r=>{res=r;loading=false;})}"
                                                   placeholder="Ex: Rua João de Deus..."
                                                   class="flex-1 bg-black/20 border border-white/10 rounded-xl px-3 py-2.5 text-white text-sm placeholder-white/20 focus:outline-none focus:border-red-500/50"
                                                   autocomplete="off">
                                            <button type="button"
                                                    @click="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuasDestino',q).then(r=>{res=r;loading=false;})}"
                                                    :disabled="q.length<3||loading"
                                                    class="bg-red-500/20 hover:bg-red-500/30 disabled:opacity-30 text-red-300 border border-red-500/40 rounded-xl px-4 py-2.5 text-sm font-bold transition-colors shrink-0">
                                                <span x-show="!loading">🔍</span>
                                                <span x-show="loading" class="animate-pulse">⟳</span>
                                            </button>
                                        </div>
                                        <div class="space-y-1 max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                            <template x-for="r in res" :key="r.road">
                                                <button type="button"
                                                        @click="$wire.call('selecionarRuaDestino', r.road, r.localidade??'', r.postcode??'')"
                                                        class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                                    <div>
                                                        <div class="text-white text-sm font-semibold" x-text="r.road"></div>
                                                        <div class="text-white/35 text-xs" x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')"></div>
                                                    </div>
                                                    <span class="text-white/20 group-hover:text-red-400 text-xs ml-2 shrink-0">→</span>
                                                </button>
                                            </template>
                                            <template x-if="searched && !loading && res.length===0">
                                                <div class="text-white/25 text-xs text-center py-3">Sem resultados</div>
                                            </template>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                {{-- Manual --}}
                                @if($destinoModo === 'manual')
                                <div class="space-y-3">
                                    <input type="text" wire:model.blur="destino_nome" placeholder="Nome do local"
                                           class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    <input type="text" wire:model.blur="destino_morada" placeholder="Morada / Rua"
                                           class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" wire:model.blur="destino_localidade" placeholder="Localidade"
                                               class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                        <input type="text" wire:model.blur="destino_cpostal" placeholder="Cód. Postal"
                                               class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm">
                                    </div>
                                    <button type="button" wire:click="$set('destinoTab', 'confirmado')"
                                            class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-300 border border-red-500/30 rounded-xl py-2.5 text-[10px] font-black uppercase tracking-widest transition-colors">
                                        Confirmar Destino
                                    </button>
                                </div>
                                @endif
                            @endif

                            {{-- Data/Hora descarga --}}
                            <div class="grid grid-cols-2 gap-4 pt-1">
                                <div class="space-y-2 border-l-2 border-red-500 pl-3">
                                    <label class="text-[9px] font-black text-red-400 uppercase tracking-widest">Data Fim</label>
                                    <input type="date" wire:model="data_fim" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-red-400 uppercase tracking-widest">Hora</label>
                                    <input type="time" wire:model="hora_fim" class="w-full bg-white/5 border-blue-700/40 rounded-xl text-white text-sm scheme-dark">
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

                        <div class="space-y-4 max-h-[420px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($items as $index => $item)
                            <div class="p-5 bg-blue-950/50 rounded-2xl border border-blue-700/30 space-y-3 relative group/item"
                                 x-data="{ q{{ $index }}: @js($item['descricao']) }">
                                <div class="space-y-2 relative">
                                    <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">Descrição</label>
                                    <input type="text"
                                           x-model="q{{ $index }}"
                                           @input.debounce.400ms="$wire.call('searchMateriais', q{{ $index }}, {{ $index }})"
                                           @blur="$wire.set('items.{{ $index }}.descricao', q{{ $index }})"
                                           placeholder="Ex: Cabo LXS 4x70+16"
                                           class="w-full bg-white/5 border-blue-700/30 rounded-xl text-white text-sm font-bold focus:ring-yellow-500 focus:border-yellow-500"
                                           autocomplete="off">
                                    @if(!empty($materiaisResults[$index]))
                                    <div class="absolute z-30 left-0 right-0 mt-1 bg-slate-800 border border-white/20 rounded-xl shadow-xl overflow-hidden">
                                        @foreach($materiaisResults[$index] as $m)
                                        <button type="button"
                                                wire:click="selecionarMaterial({{ $index }}, @js($m['nome']), @js($m['unidade']))"
                                                x-on:click="q{{ $index }} = @js($m['nome'])"
                                                class="w-full text-left px-4 py-2.5 hover:bg-white/10 flex justify-between items-center gap-3 border-b border-white/5 last:border-0">
                                            <span class="text-white text-sm font-semibold">{{ $m['nome'] }}</span>
                                            <span class="text-white/40 text-xs font-mono shrink-0">{{ $m['codigo'] }} · {{ $m['unidade'] }}</span>
                                        </button>
                                        @endforeach
                                    </div>
                                    @endif
                                    @error('items.'.$index.'.descricao') <span class="text-[9px] text-red-400">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">QTD</label>
                                        <input type="number" step="0.01" wire:model.blur="items.{{ $index }}.quantidade"
                                               class="w-full bg-white/5 border-blue-700/30 rounded-xl text-white text-sm font-bold">
                                        @error('items.'.$index.'.quantidade') <span class="text-[9px] text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-white/30 uppercase tracking-widest">Unidade</label>
                                        <select wire:model="items.{{ $index }}.unidade" class="w-full bg-blue-900 border-blue-700/30 rounded-xl text-white text-sm font-bold">
                                            @foreach(\App\Models\Material::UNIDADES as $u)
                                                <option value="{{ $u }}">{{ $u }}</option>
                                            @endforeach
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
                @endif

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
