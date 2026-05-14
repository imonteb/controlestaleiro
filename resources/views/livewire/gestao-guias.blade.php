<div class="px-6 py-6 w-full max-w-7xl mx-auto">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="truck" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Gestão de Guias de Transporte</span>
                <span class="text-[10px] bg-[rgba(255,211,0,0.15)] text-[#FFD300] px-2 py-1 rounded font-medium tracking-wide">
                    Controlo de Carga e Logística C016
                </span>
            </div>
            <button wire:click="openModal"
                style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                + Nova Guia
            </button>
        </div>
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-4 gap-1">
            @foreach([
                'todas'    => 'Todas',
                'pendente' => 'Pendentes',
                'emitida'  => 'Emitidas',
                'recusada' => 'Recusadas',
            ] as $key => $label)
            <button wire:click="$set('filtroEstado', '{{ $key }}')"
                    class="text-[11px] px-3 py-2 font-medium transition-colors whitespace-nowrap
                           {{ $filtroEstado === $key
                               ? 'text-[#FFD300] border-b-2 border-[#FFD300]'
                               : 'text-white/40 hover:text-white/70' }}">
                {{ $label }}
                @if($key !== 'todas')
                    <span class="ml-0.5 opacity-60 text-[10px]">({{ $counts[$key] ?? 0 }})</span>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Flash --}}
    @if(session()->has('success'))
    <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-3"
         style="background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.2);">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-[13px] font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background:#E4E2DF;">
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide" style="color:#7A7775;">Tipo / Data</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide" style="color:#7A7775;">Origem</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide" style="color:#7A7775;">Destino / Matrícula</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide" style="color:#7A7775;">Itens</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide" style="color:#7A7775;">Estado</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide text-right" style="color:#7A7775;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guias as $g)
                    <tr class="border-b border-[rgba(9,20,59,0.05)] last:border-0 hover:bg-[#EEECEA] transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 mb-1">
                                @php $tipoBg = $g->tipo === 'global' ? 'background:#e9d5ff; color:#7c3aed;' : 'background:#dbeafe; color:#1d4ed8;'; @endphp
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase" style="{{ $tipoBg }}">{{ $g->tipo }}</span>
                                @if($g->origem === 'colaborador')
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase" style="background:#fdf0c2; color:#854F0B;">PWA</span>
                                @endif
                            </div>
                            <div class="text-[12px] font-medium" style="color:#1A1A1A;">{{ $g->data_inicio?->format('d/m/Y') }}</div>
                            <div class="text-[10px] mt-0.5" style="color:#7A7775;">{{ $g->hora_inicio }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-[12px] font-semibold" style="color:#1A1A1A;">{{ $g->local_carga_nome }}</div>
                            <div class="text-[11px] mt-0.5" style="color:#7A7775;">{{ $g->local_carga_localidade }}</div>
                            @if($g->local_carga_morada)
                                <div class="text-[10px] mt-0.5" style="color:#7A7775;">{{ $g->local_carga_morada }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-[12px] font-semibold tracking-wider" style="color:#09143B;">{{ $g->matricula }}</div>
                            <div class="text-[11px] mt-0.5" style="color:#4A4845;">{{ $g->destino_localidade }}</div>
                            @if($g->destino_morada)
                                <div class="text-[10px] mt-0.5" style="color:#7A7775;">{{ $g->destino_morada }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-0.5">
                                @foreach($g->items->take(2) as $item)
                                    <div class="text-[11px]" style="color:#4A4845;">
                                        <span class="font-semibold" style="color:#09143B;">{{ $item->quantidade }} {{ $item->unidade }}</span>
                                        {{ $item->descricao }}
                                    </div>
                                @endforeach
                                @if($g->items->count() > 2)
                                    <div class="text-[10px] font-medium" style="color:#09143B;">+{{ $g->items->count() - 2 }} mais</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $estadoStyle = match($g->estado) {
                                    'pendente' => 'background:#fdf0c2; color:#854F0B;',
                                    'emitida'  => 'background:#d4ede4; color:#0F6E56;',
                                    'recusada' => 'background:#fde8e8; color:#A32D2D;',
                                    default    => 'background:#E4E2DF; color:#7A7775;',
                                };
                            @endphp
                            <span class="inline-block text-[10px] px-2 py-0.5 rounded font-medium" style="{{ $estadoStyle }}">
                                {{ ucfirst($g->estado) }}
                            </span>
                            @if($g->numero_at)
                                <div class="mt-1 text-[9px] font-medium" style="color:#0F6E56;">AT: {{ $g->numero_at }}</div>
                            @endif
                            @if($g->requerente)
                                <div class="mt-1 text-[10px]" style="color:#854F0B;">{{ $g->requerente->nombre }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="editarGuia({{ $g->id }})" title="{{ $g->origem === 'colaborador' ? 'Ver Pedido' : 'Editar' }}"
                                    style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; cursor:pointer;
                                        {{ $g->origem === 'colaborador'
                                            ? 'background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);'
                                            : 'background:#E4E2DF; color:#09143B; border:1px solid rgba(9,20,59,0.14);' }}">
                                    @if($g->origem === 'colaborador')
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    @endif
                                </button>
                                <button wire:click="pedirEliminar({{ $g->id }})" title="Eliminar"
                                    style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer;">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="text-[11px] font-medium uppercase tracking-widest" style="color:#7A7775;">Sem guias registadas</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guias->hasPages())
        <div class="px-4 py-3 border-t border-[rgba(9,20,59,0.08)]" style="background:#F0EEEB;">
            {{ $guias->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════════ MODAL PRINCIPAL ═══════════════════ --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
        <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl flex flex-col overflow-hidden" style="max-height:90vh;">

            {{-- Modal Header --}}
            <div class="bg-[#09143B] px-5 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                         style="{{ $modalMode === 'view' ? 'background:#fdf0c2;' : 'background:rgba(255,211,0,0.15);' }}">
                        @if($modalMode === 'view')
                            <svg class="w-4 h-4" style="color:#854F0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @else
                            <svg class="w-4 h-4" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 2v-6m-8-5h10a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm">
                            @if($modalMode === 'view') Pedido via PWA
                            @elseif($guia_id) Editar Guia #{{ $guia_id }}
                            @else Nova Guia de Transporte
                            @endif
                        </div>
                        <div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.4);">
                            {{ $modalMode === 'view' ? 'Solicitação de colaborador' : 'Documento de Transporte' }} · ID: {{ $guia_id ?? 'AUTO' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($modalMode === 'view')
                        <button wire:click="switchToEdit"
                                style="padding:6px 12px; border-radius:8px; background:rgba(255,211,0,0.15); color:#FFD300; font-size:11px; font-weight:600; border:1px solid rgba(255,211,0,0.3); cursor:pointer;">
                            Editar Pedido
                        </button>
                    @endif
                    <button wire:click="closeModal"
                            style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,0.08); color:rgba(255,255,255,0.6); border:none; cursor:pointer;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto bg-[#EEECEA] p-5 custom-scrollbar">

                {{-- ── VIEW MODE (PWA read-only) ───────────────── --}}
                @if($modalMode === 'view')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    {{-- Left: dados --}}
                    <div class="space-y-3">

                        @if($requerente_id)
                            @php $req = \App\Models\Colaborador::find($requerente_id); @endphp
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg"
                                 style="background:#fdf0c2; border:1px solid rgba(133,79,11,0.20);">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold text-white shrink-0" style="background:#854F0B;">
                                    {{ substr($req?->nombre ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-medium uppercase tracking-wide mb-0.5" style="color:#854F0B;">Solicitado via PWA</div>
                                    <div class="text-[12px] font-semibold truncate" style="color:#1A1A1A;">{{ $req?->nombre }} {{ $req?->apellido }}</div>
                                </div>
                                <span class="text-[9px] font-semibold uppercase px-1.5 py-0.5 rounded shrink-0" style="background:rgba(133,79,11,0.15); color:#854F0B;">Colaborador</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg px-3 py-2.5">
                                <div class="text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Tipo de Guia</div>
                                @php $tipoBgV = $tipo === 'global' ? 'background:#e9d5ff; color:#7c3aed;' : 'background:#dbeafe; color:#1d4ed8;'; @endphp
                                <span class="inline-block text-[11px] px-2 py-0.5 rounded font-semibold uppercase" style="{{ $tipoBgV }}">{{ $tipo }}</span>
                            </div>
                            <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg px-3 py-2.5">
                                <div class="text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Matrícula</div>
                                <div class="text-[14px] font-bold tracking-widest" style="color:#09143B;">{{ $matricula }}</div>
                            </div>
                        </div>

                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-2 h-2 rounded-full" style="background:#0F6E56;"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide" style="color:#0F6E56;">Origem / Local de Carga</span>
                            </div>
                            <div class="text-[12px] font-semibold" style="color:#1A1A1A;">{{ $local_carga_nome }}</div>
                            @if($local_carga_morada)<div class="text-[11px] mt-0.5" style="color:#4A4845;">{{ $local_carga_morada }}</div>@endif
                            <div class="flex gap-3 mt-0.5 text-[11px]" style="color:#7A7775;">
                                @if($local_carga_localidade)<span>{{ $local_carga_localidade }}</span>@endif
                                @if($local_carga_cpostal)<span>{{ $local_carga_cpostal }}</span>@endif
                            </div>
                            <div class="mt-2 pt-2 border-t border-[rgba(9,20,59,0.06)] flex gap-4 text-[11px] font-medium" style="color:#0F6E56;">
                                <span>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</span>
                                <span>{{ $hora_inicio }}</span>
                            </div>
                        </div>

                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-2 h-2 rounded-full" style="background:#A32D2D;"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide" style="color:#A32D2D;">Destino / Local de Descarga</span>
                            </div>
                            @if($destino_nome)
                                <div class="text-[12px] font-semibold" style="color:#1A1A1A;">{{ $destino_nome }}</div>
                            @else
                                <div class="text-[12px] italic" style="color:#7A7775;">Sem destino especificado</div>
                            @endif
                            @if($destino_morada)<div class="text-[11px] mt-0.5" style="color:#4A4845;">{{ $destino_morada }}</div>@endif
                            <div class="flex gap-3 mt-0.5 text-[11px]" style="color:#7A7775;">
                                @if($destino_localidade)<span>{{ $destino_localidade }}</span>@endif
                                @if($destino_cpostal)<span>{{ $destino_cpostal }}</span>@endif
                            </div>
                            @if($data_fim)
                            <div class="mt-2 pt-2 border-t border-[rgba(9,20,59,0.06)] flex gap-4 text-[11px] font-medium" style="color:#A32D2D;">
                                <span>{{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}</span>
                                @if($hora_fim)<span>{{ $hora_fim }}</span>@endif
                            </div>
                            @endif
                        </div>

                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-2 h-2 rounded-full bg-[#FFD300]"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide text-[#7A7775]">Bens a Transportar</span>
                            </div>
                            <div class="space-y-1.5">
                                @foreach($items as $item)
                                <div class="flex items-center justify-between py-1.5 border-b border-[rgba(9,20,59,0.06)] last:border-0">
                                    <span class="text-[12px]" style="color:#1A1A1A;">{{ $item['descricao'] }}</span>
                                    <span class="text-[12px] font-semibold ml-4 shrink-0" style="color:#09143B;">{{ $item['quantidade'] }} {{ $item['unidade'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Right: action panel --}}
                    <div class="space-y-3">
                        @php
                            $estStyle = match($estado) {
                                'pendente' => 'background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);',
                                'emitida'  => 'background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.20);',
                                'recusada' => 'background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20);',
                                default    => 'background:#E4E2DF; color:#7A7775; border:1px solid rgba(9,20,59,0.10);',
                            };
                        @endphp
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg px-3 py-2.5 flex items-center justify-between">
                            <span class="text-[10px] uppercase tracking-wide" style="color:#7A7775;">Estado atual</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded" style="{{ $estStyle }}">{{ ucfirst($estado) }}</span>
                        </div>

                        @if($estado === 'emitida')
                            <div class="rounded-lg px-3 py-3 text-center" style="background:#d4ede4; border:1px solid rgba(15,110,86,0.20);">
                                <div class="text-[10px] font-medium uppercase tracking-wide mb-1" style="color:#0F6E56;">Nº AT Emitido</div>
                                <div class="text-[18px] font-bold tracking-widest" style="color:#09143B;">{{ $numero_at }}</div>
                            </div>
                        @endif

                        @if($estado === 'recusada')
                            <div class="rounded-lg px-3 py-3" style="background:#fde8e8; border:1px solid rgba(163,45,45,0.20);">
                                <div class="text-[10px] font-medium uppercase tracking-wide mb-1" style="color:#A32D2D;">Motivo da Recusa</div>
                                <div class="text-[12px]" style="color:#1A1A1A;">{{ $motivo_recusa }}</div>
                            </div>
                        @endif

                        @if($estado === 'pendente')
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3 space-y-2">
                            <label class="block text-[10px] uppercase tracking-wide font-medium" style="color:#0F6E56;">Número AT (Autoridade Tributária)</label>
                            <input type="text" wire:model="numero_at" placeholder="Ex: 18928526525"
                                   class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                            @error('numero_at') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                            <button wire:click="emitirGuia"
                                    class="w-full py-2.5 rounded-lg text-[11px] font-semibold uppercase tracking-wide flex items-center justify-center gap-2"
                                    style="background:#0F6E56; color:white; border:none; cursor:pointer;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Emitir Guia
                            </button>
                        </div>

                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3 space-y-2">
                            <label class="block text-[10px] uppercase tracking-wide font-medium" style="color:#A32D2D;">Recusar Pedido</label>
                            <textarea wire:model="motivo_recusa" placeholder="Motivo da recusa..." rows="3"
                                      class="w-full text-[12px] px-2.5 py-1.5 rounded-lg resize-none"
                                      style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;"></textarea>
                            @error('motivo_recusa') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                            <button wire:click="recusarGuia"
                                    class="w-full py-2.5 rounded-lg text-[11px] font-semibold"
                                    style="background:white; color:#A32D2D; border:1px solid rgba(163,45,45,0.30); cursor:pointer;">
                                Recusar Guia
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ── EDIT MODE ───────────────────────────────── --}}
                @else
                <form wire:submit.prevent="salvarGuia" class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    {{-- LEFT column --}}
                    <div class="space-y-3">

                        {{-- Tipo + Matrícula --}}
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="text-[10px] uppercase tracking-wide text-[#7A7775] mb-2">Logística de Transporte</div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Tipo de Guia</label>
                                    <div class="flex bg-[#EEECEA] p-0.5 rounded-lg border border-[rgba(9,20,59,0.10)]">
                                        <button type="button" wire:click="$set('tipo', 'normal')"
                                                class="flex-1 py-1.5 text-[10px] font-medium uppercase rounded transition-colors"
                                                style="{{ $tipo === 'normal' ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;' }}">
                                            Normal
                                        </button>
                                        <button type="button" wire:click="$set('tipo', 'global')"
                                                class="flex-1 py-1.5 text-[10px] font-medium uppercase rounded transition-colors"
                                                style="{{ $tipo === 'global' ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;' }}">
                                            Global
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Matrícula *</label>
                                    <input type="text" wire:model="matricula"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg uppercase"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    @error('matricula') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if($requerente_id)
                                @php $req = \App\Models\Colaborador::find($requerente_id); @endphp
                                <div class="flex items-center gap-2 mt-2 px-2.5 py-2 rounded-lg"
                                     style="background:#fdf0c2; border:1px solid rgba(133,79,11,0.20);">
                                    <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs font-bold text-white shrink-0" style="background:#854F0B;">
                                        {{ substr($req?->nombre ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-medium uppercase tracking-wide" style="color:#854F0B;">Solicitado via PWA</div>
                                        <div class="text-[11px] font-semibold" style="color:#1A1A1A;">{{ $req?->nombre }} {{ $req?->apellido }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ORIGEM --}}
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-2 h-2 rounded-full" style="background:#0F6E56;"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide" style="color:#0F6E56;">Origem / Local de Carga</span>
                            </div>

                            @if($originTab === 'confirmado')
                                <div class="flex items-center justify-between rounded-lg px-3 py-2 mb-2"
                                     style="background:#d4ede4; border:1px solid rgba(15,110,86,0.20);">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[12px] font-semibold truncate" style="color:#1A1A1A;">{{ $local_carga_nome }}</div>
                                        @if($local_carga_morada || $local_carga_localidade)
                                        <div class="text-[10px] mt-0.5 truncate" style="color:#4A4845;">
                                            {{ implode(' · ', array_filter([$local_carga_morada, $local_carga_localidade, $local_carga_cpostal])) }}
                                        </div>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="alterarOrigem"
                                            class="ml-2 shrink-0 text-[10px] px-2 py-1 rounded"
                                            style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">
                                        Alterar
                                    </button>
                                </div>
                            @else
                                <div class="flex bg-[#EEECEA] p-0.5 rounded-lg gap-0.5 mb-2">
                                    @foreach(['frequente' => '⭐ Frequente', 'pesquisa' => '🔍 Pesquisa CT', 'manual' => '✏️ Manual'] as $m => $label)
                                    <button type="button" wire:click="$set('originModo', '{{ $m }}')"
                                            class="flex-1 py-1.5 rounded text-[10px] font-medium transition-colors"
                                            style="{{ $originModo === $m ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>

                                @if($originModo === 'frequente')
                                <div class="space-y-1 max-h-40 overflow-y-auto custom-scrollbar">
                                    @forelse($locaisFrequentes as $lf)
                                    <button type="button" wire:click="selecionarLocalCarga({{ $lf['id'] }})"
                                            class="flex items-center justify-between w-full text-left rounded-lg px-3 py-2 hover:bg-[#EEECEA] transition-colors"
                                            style="background:white; border:1px solid rgba(9,20,59,0.08);">
                                        <div class="min-w-0">
                                            <div class="text-[12px] font-medium truncate" style="color:#1A1A1A;">{{ $lf['nome'] }}</div>
                                            @if($lf['localidade'])<div class="text-[10px] truncate" style="color:#7A7775;">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>@endif
                                        </div>
                                        <span class="text-xs ml-2 shrink-0" style="color:#0F6E56;">→</span>
                                    </button>
                                    @empty
                                    <p class="text-[11px] text-center py-3" style="color:#7A7775;">Sem locais frequentes registados.</p>
                                    @endforelse
                                </div>
                                @endif

                                @if($originModo === 'pesquisa')
                                <div class="space-y-2">
                                    @if($originDD)
                                    <div class="flex items-center justify-between px-3 py-2 rounded-lg"
                                         style="background:#d4ede4; border:1px solid rgba(15,110,86,0.20);">
                                        <div>
                                            <div class="text-[9px] font-medium uppercase tracking-wide" style="color:#7A7775;">Distrito</div>
                                            <div class="text-[12px] font-semibold" style="color:#0F6E56;">{{ $distritos->firstWhere('dd', $originDD)?->desig }}</div>
                                        </div>
                                        <button type="button" wire:click="$set('originDD', '')"
                                                class="text-[11px] px-2 py-1 rounded"
                                                style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">✕</button>
                                    </div>
                                    @else
                                    <div x-data="{
                                        open: false, search: '',
                                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                        get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                    }">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[12px]"
                                                style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#7A7775; cursor:pointer;">
                                            <span>Selecionar Distrito</span>
                                            <span x-text="open ? '▲' : '▼'" class="text-[10px]"></span>
                                        </button>
                                        <div x-show="open" x-transition.duration.150ms class="mt-1">
                                            <input type="text" x-model="search" placeholder="Filtrar..."
                                                   class="w-full text-[12px] px-2.5 py-1.5 rounded-lg mb-1"
                                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                            <div class="max-h-40 overflow-y-auto rounded-lg border border-[rgba(9,20,59,0.10)] bg-white custom-scrollbar">
                                                <template x-for="d in filtered" :key="d.dd">
                                                    <button type="button" @click="$wire.set('originDD', d.dd); open = false"
                                                            class="flex items-center justify-between w-full text-left px-3 py-2 border-b border-[rgba(9,20,59,0.05)] last:border-0 hover:bg-[#F0EEEB] transition-colors text-[12px]"
                                                            style="color:#1A1A1A;">
                                                        <span x-text="d.desig"></span>
                                                        <span class="text-[10px]" style="color:#09143B;">→</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($originDD && count($concelhos))
                                        @if($originCC)
                                        <div class="flex items-center justify-between px-3 py-2 rounded-lg"
                                             style="background:#d4ede4; border:1px solid rgba(15,110,86,0.20);">
                                            <div>
                                                <div class="text-[9px] font-medium uppercase tracking-wide" style="color:#7A7775;">Concelho</div>
                                                <div class="text-[12px] font-semibold" style="color:#0F6E56;">{{ $concelhos->firstWhere('cc', $originCC)?->desig }}</div>
                                            </div>
                                            <button type="button" wire:click="$set('originCC', '')"
                                                    class="text-[11px] px-2 py-1 rounded"
                                                    style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">✕</button>
                                        </div>
                                        @else
                                        <div x-data="{
                                            open: true, search: '',
                                            items: @js($concelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                            get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                        }">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[12px]"
                                                    style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#7A7775; cursor:pointer;">
                                                <span>Selecionar Concelho</span>
                                                <span x-text="open ? '▲' : '▼'" class="text-[10px]"></span>
                                            </button>
                                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                                <input type="text" x-model="search" placeholder="Filtrar..."
                                                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg mb-1"
                                                       style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                                <div class="max-h-40 overflow-y-auto rounded-lg border border-[rgba(9,20,59,0.10)] bg-white custom-scrollbar">
                                                    <template x-for="c in filtered" :key="c.cc">
                                                        <button type="button" @click="$wire.set('originCC', c.cc); open = false"
                                                                class="flex items-center justify-between w-full text-left px-3 py-2 border-b border-[rgba(9,20,59,0.05)] last:border-0 hover:bg-[#F0EEEB] transition-colors text-[12px]"
                                                                style="color:#1A1A1A;">
                                                            <span x-text="c.desig"></span>
                                                            <span class="text-[10px]" style="color:#09143B;">→</span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    @if($originCC)
                                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                                        <div class="flex gap-2 mb-1">
                                            <input type="text" x-model="q"
                                                   @keydown.enter.prevent="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuas',q).then(r=>{res=r;loading=false;})}"
                                                   placeholder="Ex: Caminho da Levada..."
                                                   class="flex-1 text-[12px] px-2.5 py-1.5 rounded-lg"
                                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;"
                                                   autocomplete="off">
                                            <button type="button"
                                                    @click="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuas',q).then(r=>{res=r;loading=false;})}"
                                                    :disabled="q.length<3||loading"
                                                    class="px-3 py-1.5 rounded-lg text-[12px] font-medium disabled:opacity-40 shrink-0"
                                                    style="background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.20); cursor:pointer;">
                                                <span x-show="!loading">🔍</span>
                                                <span x-show="loading" class="animate-pulse">⟳</span>
                                            </button>
                                        </div>
                                        <div class="space-y-0.5 max-h-40 overflow-y-auto custom-scrollbar">
                                            <template x-for="r in res" :key="r.road">
                                                <button type="button"
                                                        @click="$wire.call('selecionarRuaOrigem', r.road, r.localidade??'', r.postcode??'')"
                                                        class="flex items-center justify-between w-full text-left rounded-lg px-3 py-2 hover:bg-[#EEECEA] transition-colors"
                                                        style="background:white; border:1px solid rgba(9,20,59,0.08);">
                                                    <div class="min-w-0">
                                                        <div class="text-[12px] font-medium truncate" x-text="r.road" style="color:#1A1A1A;"></div>
                                                        <div class="text-[10px] truncate" x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')" style="color:#7A7775;"></div>
                                                    </div>
                                                    <span class="text-[10px] ml-2 shrink-0" style="color:#0F6E56;">→</span>
                                                </button>
                                            </template>
                                            <template x-if="searched && !loading && res.length===0">
                                                <div class="text-[11px] text-center py-2" style="color:#7A7775;">Sem resultados</div>
                                            </template>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($originModo === 'manual')
                                <div class="space-y-2">
                                    <input type="text" wire:model.blur="local_carga_nome" placeholder="Nome do local *"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    @error('local_carga_nome') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                                    <input type="text" wire:model.blur="local_carga_morada" placeholder="Morada / Rua"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" wire:model.blur="local_carga_localidade" placeholder="Localidade"
                                               class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                               style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                        <input type="text" wire:model.blur="local_carga_cpostal" placeholder="Cód. Postal"
                                               class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                               style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    </div>
                                    <button type="button" wire:click="$set('originTab', 'confirmado')"
                                            class="w-full py-2 rounded-lg text-[11px] font-medium"
                                            style="background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.20); cursor:pointer;">
                                        Confirmar Origem
                                    </button>
                                </div>
                                @endif
                            @endif

                            <div class="grid grid-cols-2 gap-3 mt-2 pt-2 border-t border-[rgba(9,20,59,0.06)]">
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide mb-1" style="color:#0F6E56;">Data Início *</label>
                                    <input type="date" wire:model="data_inicio"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    @error('data_inicio') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide mb-1" style="color:#0F6E56;">Hora *</label>
                                    <input type="time" wire:model="hora_inicio"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                </div>
                            </div>
                        </div>

                        {{-- DESTINO --}}
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-2 h-2 rounded-full" style="background:#A32D2D;"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide" style="color:#A32D2D;">Destino / Local de Descarga</span>
                            </div>

                            @if($destinoTab === 'confirmado')
                                <div class="flex items-center justify-between rounded-lg px-3 py-2 mb-2"
                                     style="background:#fde8e8; border:1px solid rgba(163,45,45,0.20);">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[12px] font-semibold truncate" style="color:#1A1A1A;">{{ $destino_nome ?: '(sem nome)' }}</div>
                                        @if($destino_morada || $destino_localidade)
                                        <div class="text-[10px] mt-0.5 truncate" style="color:#4A4845;">
                                            {{ implode(' · ', array_filter([$destino_morada, $destino_localidade, $destino_cpostal])) }}
                                        </div>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="alterarDestino"
                                            class="ml-2 shrink-0 text-[10px] px-2 py-1 rounded"
                                            style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">
                                        Alterar
                                    </button>
                                </div>
                            @else
                                <div class="flex bg-[#EEECEA] p-0.5 rounded-lg gap-0.5 mb-2">
                                    @foreach(['frequente' => '⭐ Frequente', 'pesquisa' => '🔍 Pesquisa CT', 'manual' => '✏️ Manual'] as $m => $label)
                                    <button type="button" wire:click="$set('destinoModo', '{{ $m }}')"
                                            class="flex-1 py-1.5 rounded text-[10px] font-medium transition-colors"
                                            style="{{ $destinoModo === $m ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>

                                @if($destinoModo === 'frequente')
                                <div class="space-y-1 max-h-40 overflow-y-auto custom-scrollbar">
                                    @forelse($locaisFrequentes as $lf)
                                    <button type="button" wire:click="selecionarLocalDestino({{ $lf['id'] }})"
                                            class="flex items-center justify-between w-full text-left rounded-lg px-3 py-2 hover:bg-[#EEECEA] transition-colors"
                                            style="background:white; border:1px solid rgba(9,20,59,0.08);">
                                        <div class="min-w-0">
                                            <div class="text-[12px] font-medium truncate" style="color:#1A1A1A;">{{ $lf['nome'] }}</div>
                                            @if($lf['localidade'])<div class="text-[10px] truncate" style="color:#7A7775;">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>@endif
                                        </div>
                                        <span class="text-xs ml-2 shrink-0" style="color:#A32D2D;">→</span>
                                    </button>
                                    @empty
                                    <p class="text-[11px] text-center py-3" style="color:#7A7775;">Sem locais frequentes registados.</p>
                                    @endforelse
                                </div>
                                @endif

                                @if($destinoModo === 'pesquisa')
                                <div class="space-y-2">
                                    @if($destinoDD)
                                    <div class="flex items-center justify-between px-3 py-2 rounded-lg"
                                         style="background:#fde8e8; border:1px solid rgba(163,45,45,0.20);">
                                        <div>
                                            <div class="text-[9px] font-medium uppercase tracking-wide" style="color:#7A7775;">Distrito</div>
                                            <div class="text-[12px] font-semibold" style="color:#A32D2D;">{{ $distritos->firstWhere('dd', $destinoDD)?->desig }}</div>
                                        </div>
                                        <button type="button" wire:click="$set('destinoDD', '')"
                                                class="text-[11px] px-2 py-1 rounded"
                                                style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">✕</button>
                                    </div>
                                    @else
                                    <div x-data="{
                                        open: false, search: '',
                                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                        get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                    }">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[12px]"
                                                style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#7A7775; cursor:pointer;">
                                            <span>Selecionar Distrito</span>
                                            <span x-text="open ? '▲' : '▼'" class="text-[10px]"></span>
                                        </button>
                                        <div x-show="open" x-transition.duration.150ms class="mt-1">
                                            <input type="text" x-model="search" placeholder="Filtrar..."
                                                   class="w-full text-[12px] px-2.5 py-1.5 rounded-lg mb-1"
                                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                            <div class="max-h-40 overflow-y-auto rounded-lg border border-[rgba(9,20,59,0.10)] bg-white custom-scrollbar">
                                                <template x-for="d in filtered" :key="d.dd">
                                                    <button type="button" @click="$wire.set('destinoDD', d.dd); open = false"
                                                            class="flex items-center justify-between w-full text-left px-3 py-2 border-b border-[rgba(9,20,59,0.05)] last:border-0 hover:bg-[#F0EEEB] transition-colors text-[12px]"
                                                            style="color:#1A1A1A;">
                                                        <span x-text="d.desig"></span>
                                                        <span class="text-[10px]" style="color:#09143B;">→</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($destinoDD && count($destinoConcelhos))
                                        @if($destinoCC)
                                        <div class="flex items-center justify-between px-3 py-2 rounded-lg"
                                             style="background:#fde8e8; border:1px solid rgba(163,45,45,0.20);">
                                            <div>
                                                <div class="text-[9px] font-medium uppercase tracking-wide" style="color:#7A7775;">Concelho</div>
                                                <div class="text-[12px] font-semibold" style="color:#A32D2D;">{{ $destinoConcelhos->firstWhere('cc', $destinoCC)?->desig }}</div>
                                            </div>
                                            <button type="button" wire:click="$set('destinoCC', '')"
                                                    class="text-[11px] px-2 py-1 rounded"
                                                    style="background:white; color:#4A4845; border:1px solid rgba(9,20,59,0.14); cursor:pointer;">✕</button>
                                        </div>
                                        @else
                                        <div x-data="{
                                            open: true, search: '',
                                            items: @js($destinoConcelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,''); },
                                            get filtered() { const q = this.norm(this.search); return q ? this.items.filter(i => this.norm(i.desig).includes(q)) : this.items; }
                                        }">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[12px]"
                                                    style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#7A7775; cursor:pointer;">
                                                <span>Selecionar Concelho</span>
                                                <span x-text="open ? '▲' : '▼'" class="text-[10px]"></span>
                                            </button>
                                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                                <input type="text" x-model="search" placeholder="Filtrar..."
                                                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg mb-1"
                                                       style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                                <div class="max-h-40 overflow-y-auto rounded-lg border border-[rgba(9,20,59,0.10)] bg-white custom-scrollbar">
                                                    <template x-for="c in filtered" :key="c.cc">
                                                        <button type="button" @click="$wire.set('destinoCC', c.cc); open = false"
                                                                class="flex items-center justify-between w-full text-left px-3 py-2 border-b border-[rgba(9,20,59,0.05)] last:border-0 hover:bg-[#F0EEEB] transition-colors text-[12px]"
                                                                style="color:#1A1A1A;">
                                                            <span x-text="c.desig"></span>
                                                            <span class="text-[10px]" style="color:#09143B;">→</span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    @if($destinoCC)
                                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                                        <div class="flex gap-2 mb-1">
                                            <input type="text" x-model="q"
                                                   @keydown.enter.prevent="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuasDestino',q).then(r=>{res=r;loading=false;})}"
                                                   placeholder="Ex: Rua João de Deus..."
                                                   class="flex-1 text-[12px] px-2.5 py-1.5 rounded-lg"
                                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;"
                                                   autocomplete="off">
                                            <button type="button"
                                                    @click="if(q.length>=3){loading=true;searched=true;$wire.call('searchRuasDestino',q).then(r=>{res=r;loading=false;})}"
                                                    :disabled="q.length<3||loading"
                                                    class="px-3 py-1.5 rounded-lg text-[12px] font-medium disabled:opacity-40 shrink-0"
                                                    style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer;">
                                                <span x-show="!loading">🔍</span>
                                                <span x-show="loading" class="animate-pulse">⟳</span>
                                            </button>
                                        </div>
                                        <div class="space-y-0.5 max-h-40 overflow-y-auto custom-scrollbar">
                                            <template x-for="r in res" :key="r.road">
                                                <button type="button"
                                                        @click="$wire.call('selecionarRuaDestino', r.road, r.localidade??'', r.postcode??'')"
                                                        class="flex items-center justify-between w-full text-left rounded-lg px-3 py-2 hover:bg-[#EEECEA] transition-colors"
                                                        style="background:white; border:1px solid rgba(9,20,59,0.08);">
                                                    <div class="min-w-0">
                                                        <div class="text-[12px] font-medium truncate" x-text="r.road" style="color:#1A1A1A;"></div>
                                                        <div class="text-[10px] truncate" x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')" style="color:#7A7775;"></div>
                                                    </div>
                                                    <span class="text-[10px] ml-2 shrink-0" style="color:#A32D2D;">→</span>
                                                </button>
                                            </template>
                                            <template x-if="searched && !loading && res.length===0">
                                                <div class="text-[11px] text-center py-2" style="color:#7A7775;">Sem resultados</div>
                                            </template>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($destinoModo === 'manual')
                                <div class="space-y-2">
                                    <input type="text" wire:model.blur="destino_nome" placeholder="Nome do local"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    <input type="text" wire:model.blur="destino_morada" placeholder="Morada / Rua"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" wire:model.blur="destino_localidade" placeholder="Localidade"
                                               class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                               style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                        <input type="text" wire:model.blur="destino_cpostal" placeholder="Cód. Postal"
                                               class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                               style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                    </div>
                                    <button type="button" wire:click="$set('destinoTab', 'confirmado')"
                                            class="w-full py-2 rounded-lg text-[11px] font-medium"
                                            style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); cursor:pointer;">
                                        Confirmar Destino
                                    </button>
                                </div>
                                @endif
                            @endif

                            <div class="grid grid-cols-2 gap-3 mt-2 pt-2 border-t border-[rgba(9,20,59,0.06)]">
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide mb-1" style="color:#A32D2D;">Data Fim</label>
                                    <input type="date" wire:model="data_fim"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase tracking-wide mb-1" style="color:#A32D2D;">Hora</label>
                                    <input type="time" wire:model="hora_fim"
                                           class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                           style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT column: items + AT + guardar --}}
                    <div class="space-y-3">

                        {{-- Items --}}
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#FFD300]"></span>
                                    <span class="text-[10px] font-medium uppercase tracking-wide text-[#7A7775]">Bens / Materiais a Transportar</span>
                                </div>
                                <button type="button" wire:click="addItem"
                                        class="text-[10px] font-medium flex items-center gap-1" style="color:#09143B;">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Adicionar
                                </button>
                            </div>

                            <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
                                @foreach($items as $index => $item)
                                <div class="p-3 rounded-lg border border-[rgba(9,20,59,0.10)] relative group/item"
                                     style="background:#EEECEA;"
                                     x-data="{ q{{ $index }}: @js($item['descricao']) }">
                                    <div class="space-y-1 relative">
                                        <label class="block text-[10px] uppercase tracking-wide text-[#7A7775]">Descrição</label>
                                        <input type="text"
                                               x-model="q{{ $index }}"
                                               @input.debounce.400ms="$wire.call('searchMateriais', q{{ $index }}, {{ $index }})"
                                               @blur="$wire.set('items.{{ $index }}.descricao', q{{ $index }})"
                                               placeholder="Ex: Cabo LXS 4x70+16"
                                               class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                               style="background:white; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;"
                                               autocomplete="off">
                                        @if(!empty($materiaisResults[$index]))
                                        <div class="absolute z-30 left-0 right-0 mt-0.5 rounded-lg border border-[rgba(9,20,59,0.10)] bg-white shadow-lg overflow-hidden">
                                            @foreach($materiaisResults[$index] as $m)
                                            <button type="button"
                                                    wire:click="selecionarMaterial({{ $index }}, @js($m['nome']), @js($m['unidade']))"
                                                    x-on:click="q{{ $index }} = @js($m['nome'])"
                                                    class="w-full text-left px-3 py-2 hover:bg-[#F0EEEB] flex justify-between items-center gap-3 border-b border-[rgba(9,20,59,0.05)] last:border-0 text-[12px]"
                                                    style="color:#1A1A1A;">
                                                <span>{{ $m['nome'] }}</span>
                                                <span class="text-[10px] shrink-0" style="color:#7A7775;">{{ $m['codigo'] }} · {{ $m['unidade'] }}</span>
                                            </button>
                                            @endforeach
                                        </div>
                                        @endif
                                        @error('items.'.$index.'.descricao') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div>
                                            <label class="block text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">QTD</label>
                                            <input type="number" step="0.01" wire:model.blur="items.{{ $index }}.quantidade"
                                                   class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                                   style="background:white; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                            @error('items.'.$index.'.quantidade') <span class="text-[10px]" style="color:#A32D2D;">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Unidade</label>
                                            <select wire:model="items.{{ $index }}.unidade"
                                                    class="w-full text-[12px] px-2.5 py-1.5 rounded-lg"
                                                    style="background:white; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                                                @foreach(\App\Models\Material::UNIDADES as $u)
                                                    <option value="{{ $u }}">{{ $u }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover/item:opacity-100 transition-all shadow-sm"
                                            style="background:#A32D2D; color:white; border:none; cursor:pointer;">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Número AT --}}
                        <div class="bg-[#F0EEEB] border border-[rgba(9,20,59,0.10)] rounded-lg p-3 space-y-2">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" style="background:#0F6E56;"></span>
                                <span class="text-[10px] font-medium uppercase tracking-wide" style="color:#0F6E56;">Número AT (Autoridade Tributária)</span>
                            </div>
                            <input type="text" wire:model="numero_at" placeholder="Ex: 18928526525"
                                   class="w-full text-[12px] px-2.5 py-1.5 rounded-lg tracking-widest"
                                   style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;">
                            <p class="text-[10px] italic" style="color:#7A7775;">
                                Ao inserir o nº AT e guardar, a guia passa a <span style="color:#0F6E56; font-weight:600;">Emitida</span>.
                            </p>

                            @if($estado === 'pendente' && $guia_id)
                            <div class="pt-2 border-t border-[rgba(9,20,59,0.06)] space-y-2">
                                <label class="block text-[10px] uppercase tracking-wide font-medium" style="color:#A32D2D;">Recusar Pedido</label>
                                <textarea wire:model="motivo_recusa" placeholder="Motivo da recusa..." rows="2"
                                          class="w-full text-[12px] px-2.5 py-1.5 rounded-lg resize-none"
                                          style="background:#EEECEA; border:1px solid rgba(9,20,59,0.16); color:#1A1A1A; outline:none;"></textarea>
                                <button type="button" wire:click="recusarGuia"
                                        class="w-full py-2 rounded-lg text-[11px] font-medium"
                                        style="background:white; color:#A32D2D; border:1px solid rgba(163,45,45,0.30); cursor:pointer;">
                                    Recusar Guia
                                </button>
                            </div>
                            @endif
                        </div>

                        <button type="submit"
                                class="w-full py-3 rounded-xl text-[12px] font-bold uppercase tracking-wide flex items-center justify-center gap-2"
                                style="background:#09143B; color:#FFD300; border:none; cursor:pointer;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $guia_id ? 'Atualizar Guia' : 'Guardar Guia' }}
                        </button>
                    </div>
                </form>
                @endif

            </div>
        </div>
    </div>
    @endif

    {{-- Modal eliminar guia --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-5 py-3 flex items-center gap-2" style="background:#09143B;">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="text-white font-medium text-sm">Eliminar Guia</span>
            </div>
            <div class="px-6 py-5">
                <p style="color:#4A4845; font-size:13px;">Tem a certeza que pretende apagar esta guia? Esta ação é irreversível.</p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button wire:click="cancelarEliminar" style="padding:8px 16px; border-radius:8px; border:1px solid rgba(9,20,59,0.16); color:#4A4845; background:white; font-size:13px; cursor:pointer;">Cancelar</button>
                <button wire:click="apagarGuia" style="padding:8px 16px; border-radius:8px; background:#A32D2D; color:white; font-weight:700; font-size:13px; border:none; cursor:pointer;">Sim, eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(9,20,59,0.04); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(9,20,59,0.15); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(9,20,59,0.25); }
</style>
