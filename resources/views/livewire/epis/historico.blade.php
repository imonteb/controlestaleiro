<div class="flex flex-col gap-4 w-full">

    {{-- Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="calendar" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Histórico Mensal EPI</span>
            </div>
            <button wire:click="exportarExcel"
                style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:5px 12px; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar Excel
            </button>
        </div>
    </div>

    {{-- Month Navigator --}}
    <div class="flex items-center justify-center gap-3 my-2">
        <button wire:click="mesAnterior"
            style="background:#09143B; color:white; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <span style="color:#09143B; font-weight:700; font-size:15px; min-width:180px; text-align:center; text-transform:uppercase; letter-spacing:0.05em;">{{ $nombreMes }}</span>
        <button wire:click="mesSeguinte"
            style="background:#09143B; color:white; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Search --}}
    <div>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar colaborador..."
               class="cme-input" style="max-width:360px;">
    </div>

    {{-- Fichas per collaborator --}}
    @if($colaboradoresConEntregas->isEmpty())
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.10);" class="rounded-xl p-12 text-center text-[#7A7775]">
            Nenhuma entrega de EPI registada neste mês.
        </div>
    @else
        @foreach($colaboradoresConEntregas as $colabId => $entregasColab)
            @php
                $colab = $entregasColab->first()->colaborador;
                $riscosUnion = $entregasColab->pluck('epiItem.riscos')->flatten()->unique()->filter()->values();
            @endphp
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);" class="rounded-xl overflow-hidden">
                {{-- Collaborator header --}}
                <div style="background:#09143B;" class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div style="background:rgba(255,211,0,0.15); border-radius:6px; padding:6px;">
                            <svg style="width:16px;height:16px;color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <span style="color:white; font-weight:600; font-size:13px;">{{ $colab->nombre ?? '' }} {{ $colab->apellido ?? '' }}</span>
                            <span style="color:rgba(255,255,255,0.5); font-size:11px; margin-left:8px;">{{ $colab->denominacion_cargo ?? '' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('epis.ficha', $colab) }}" target="_blank"
                           style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:4px 10px; border-radius:6px; font-size:10px; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                            <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Ficha EPI
                        </a>
                        <span style="color:rgba(255,255,255,0.6); font-size:11px;">{{ $entregasColab->count() }} entrega(s)</span>
                    </div>
                </div>

                {{-- Deliveries table --}}
                <div class="overflow-x-auto" style="background:#ffffff;">
                    <table class="w-full text-sm" style="background:#ffffff;">
                        <thead>
                            <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.08);">
                                <th class="px-4 py-2.5 text-center w-10" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">Nº</th>
                                <th class="px-4 py-2.5 text-left" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">EPI</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">CA</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">Qtd</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">Data Entrega</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">Ass.</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#7A7775; text-transform:uppercase; letter-spacing:0.05em;">Data Devolução</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entregasColab->values() as $idx => $e)
                            <tr style="background:{{ $idx % 2 === 0 ? '#ffffff' : '#F0EEEB' }}; border-bottom:1px solid rgba(9,20,59,0.05);">
                                <td class="px-4 py-2 text-center font-mono" style="color:#7A7775; font-size:11px;">{{ $idx + 1 }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex flex-col">
                                        <span style="font-weight:600; color:#1A1A1A; font-size:12px;">
                                            {{ $e->epiItem->nombre ?? '—' }}
                                            @if($e->talla)
                                                <span style="font-size:10px; color:#09143B; font-weight:400; margin-left:4px;">({{ $e->talla }})</span>
                                            @endif
                                        </span>
                                        @if($e->epiItem->codigo ?? null)
                                            <span style="font-size:10px; color:#7A7775; font-family:monospace;">{{ $e->epiItem->codigo }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center" style="color:#4A4845; font-size:12px;">{{ $e->epiItem->ca_numero ?? '—' }}</td>
                                <td class="px-4 py-2 text-center" style="font-weight:600; color:#1A1A1A; font-size:12px;">{{ $e->cantidad }} <span style="font-weight:400; font-size:10px; color:#7A7775;">{{ $e->epiItem->unidade ?? '' }}</span></td>
                                <td class="px-4 py-2 text-center" style="color:#4A4845; font-size:12px;">{{ \Carbon\Carbon::parse($e->fecha_entrega)->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($e->firma)
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background:#d4ede4; color:#0F6E56;">
                                            <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span style="color:#D0CECC;">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center" style="color:#4A4845; font-size:12px;">
                                    @if($e->fecha_devolucion)
                                        {{ \Carbon\Carbon::parse($e->fecha_devolucion)->format('d/m/Y') }}
                                    @else
                                        <span style="color:#D0CECC;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Riscos footer --}}
                @if($riscosUnion->isNotEmpty())
                <div style="background:#fde8e8; border-top:1px solid rgba(163,45,45,0.12); padding:8px 20px;" class="flex flex-wrap items-center gap-2">
                    <span style="font-size:10px; font-weight:700; color:#A32D2D; text-transform:uppercase; letter-spacing:0.05em; margin-right:4px;">Riscos:</span>
                    @foreach($riscosUnion as $risco)
                        <span style="display:inline-flex; padding:2px 8px; border-radius:4px; background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:10px; font-weight:600;">{{ $risco }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
