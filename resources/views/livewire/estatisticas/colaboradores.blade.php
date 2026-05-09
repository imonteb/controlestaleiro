<div class="p-4 flex flex-col gap-4 h-full table-scroll-page">

    {{-- HEADER --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="users" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Estatísticas de Colaboradores</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="Procurar colaborador..."
                       style="background:white; color:#1A1A1A; border:1px solid rgba(9,20,59,0.18); border-radius:6px; padding:5px 10px; font-size:11px; outline:none; min-width:160px;" />
                <label style="display:flex; align-items:center; gap:5px; cursor:pointer; font-size:11px; font-weight:600; color:white;">
                    <input type="checkbox" wire:model.live="apenasAtivos" style="accent-color:#FFD300;" />
                    Só ativos
                </label>
                <div class="flex items-center gap-1">
                    <button wire:click="mesAnterior" type="button"
                            style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:5px 10px; border-radius:6px; font-size:13px; cursor:pointer; font-weight:700; line-height:1;">&#8249;</button>
                    <div style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); padding:5px 16px; border-radius:6px; font-size:11px; font-weight:700; color:white; white-space:nowrap; min-width:120px; text-align:center; text-transform:capitalize;">
                        {{ $nomeMes }}
                    </div>
                    <button wire:click="proximoMes" type="button"
                            style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:5px 10px; border-radius:6px; font-size:13px; cursor:pointer; font-weight:700; line-height:1;">&#8250;</button>
                </div>
                <a href="{{ route('exportar.estatisticas.colaboradores', ['mes' => $mes, 'ano' => $ano]) }}"
                   style="display:inline-flex; align-items:center; gap:6px; background:#FFD300; color:#09143B; font-size:11px; font-weight:700; padding:5px 12px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                    📥 Exportar Excel
                </a>
            </div>
        </div>
        {{-- Leyenda --}}
        <div style="background:#F0EEEB; border-top:1px solid rgba(9,20,59,0.08);" class="px-4 py-2.5 flex flex-wrap items-center gap-4">
            <span style="font-size:10px; font-weight:700; color:#09143B;">Legenda:</span>
            <span style="display:flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#1e40af;">
                <span style="display:inline-block; width:22px; height:16px; border-radius:3px; background:#dbeafe; border:1px solid #93c5fd;"></span> PEP atribuído
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#92400e;">
                <span style="display:inline-block; width:22px; height:16px; border-radius:3px; background:#fef3c7; border:1px solid #fcd34d;"></span> Estado especial
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#7A7775;">
                <span style="display:inline-block; width:22px; height:16px; border-radius:3px; background:#F0EEEB; border:1px solid rgba(9,20,59,0.18);"></span> Sem atribuição
            </span>
            <span style="display:flex; align-items:center; gap:5px; font-size:10px; font-weight:600; color:#7f1d1d;">
                <span style="display:inline-block; width:22px; height:16px; border-radius:3px; background:#7f1d1d; border:1px solid #991b1b;"></span> Fim de semana
            </span>
        </div>
    </div>

    <style>
        .col-row-hover td, .col-row-hover { background: #E4E2DF !important; }
    </style>

    {{-- Tabela --}}
    <div class="flex-1 min-h-0 overflow-x-auto overflow-y-auto" style="border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.08);border:1px solid rgba(9,20,59,0.14);">
        <table style="border-collapse:collapse;width:100%;min-width:{{ count($diasLaborais) * 100 + 300 }}px;">

            <thead style="position:sticky;top:0;z-index:20;">
                <tr style="position:sticky;top:0;z-index:20;">
                    <th style="position:sticky;left:0;top:0;z-index:12;background:#09143B;color:white;font-size:0.75rem;font-weight:700;padding:10px 14px;text-align:left;white-space:nowrap;min-width:240px;border-right:2px solid rgba(255,255,255,0.12);">
                        Colaborador
                    </th>
                    @foreach($diasLaborais as $dia)
                    @php
                        $esWeekend = $dia->isWeekend();
                        $diaSemana = $esWeekend
                            ? ($dia->isSaturday() ? 'Sáb' : 'Dom')
                            : (['Seg','Ter','Qua','Qui','Sex'][$dia->dayOfWeekIso - 1] ?? '');
                        $esHoy  = $dia->isToday();
                        $bgDia  = $esHoy ? '#FFD300' : ($esWeekend ? '#7f1d1d' : '#09143B');
                        $textDia = $esHoy ? '#09143B' : 'white';
                    @endphp
                    <th style="background:{{ $bgDia }};color:{{ $textDia }};font-size:0.72rem;font-weight:700;padding:6px 4px;text-align:center;min-width:90px;border-right:1px solid rgba(255,255,255,0.1);">
                        <div>{{ $diaSemana }}</div>
                        <div style="font-size:0.85rem;font-weight:800;margin-top:1px;">{{ $dia->format('d') }}</div>
                        @if($esWeekend)<div style="font-size:0.55rem;letter-spacing:0.03em;margin-top:1px;opacity:0.8;">FDS</div>@endif
                    </th>
                    @endforeach
                    <th style="position:sticky;right:0;top:0;z-index:12;background:#09143B;color:#FFD300;font-size:0.72rem;font-weight:800;padding:6px 10px;text-align:center;min-width:56px;border-left:2px solid rgba(255,255,255,0.12);white-space:nowrap;">
                        TOTAL<br><span style="font-size:0.65rem;color:rgba(255,255,255,0.5);font-weight:600;">dias</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($colaboradores as $i => $col)
                @php $rowBg = $i % 2 === 0 ? '#FFFFFF' : '#F0EEEB'; @endphp
                <tr style="background:{{ $rowBg }} !important;{{ !$col->activo ? 'opacity:0.5;' : '' }}"
                    onmouseover="this.classList.add('col-row-hover')"
                    onmouseout="this.classList.remove('col-row-hover')">
                    {{-- Nome do colaborador --}}
                    <td style="position:sticky;left:0;z-index:10;background:{{ $rowBg }} !important;padding:8px 14px;border-right:2px solid rgba(9,20,59,0.10);min-width:240px;border-bottom:1px solid rgba(9,20,59,0.06);">
                        <div style="font-size:12px;font-weight:700;color:#09143B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;" title="{{ $col->apellido }}, {{ $col->nombre }}">
                            {{ $col->apellido }}, {{ $col->nombre }}
                        </div>
                        @if($col->denominacion_cargo)
                        <div style="font-size:10px;color:#7A7775;margin-top:1px;">{{ $col->denominacion_cargo }}</div>
                        @endif
                    </td>

                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr   = $dia->toDateString();
                        $cell       = $matrix[$col->id][$fechaStr] ?? [];
                        $esHoy      = $dia->isToday();
                        $esWeekend  = $dia->isWeekend();
                        $borderRight = $esHoy ? '1px solid #FFD300' : ($esWeekend ? '1px solid #fecaca' : '1px solid rgba(9,20,59,0.06)');
                        $borderLeft  = $esHoy ? '1px solid #FFD300' : ($esWeekend ? '1px solid #fecaca' : 'none');
                        $tdBg        = $esWeekend && empty($cell) ? 'background:#fef2f2;' : '';
                    @endphp
                    <td style="{{ $tdBg }}text-align:center;padding:4px 2px;border-bottom:1px solid rgba(9,20,59,0.06);border-right:{{ $borderRight }};border-left:{{ $borderLeft }};vertical-align:middle;">
                        @if(count($cell) > 0)
                            @php
                                $first = $cell[0];
                                $rest  = count($cell) - 1;
                                $allLabels = implode("\n", array_map(fn($c) => $c['label'], $cell));
                                $isPep = $first['tipo'] === 'pep';
                            @endphp
                            <div style="display:inline-flex;flex-direction:column;align-items:center;gap:1px;" title="{{ $allLabels }}">
                                <span style="display:inline-block;font-size:0.6rem;font-weight:700;padding:2px 5px;border-radius:4px;white-space:normal;word-break:break-word;text-align:center;line-height:1.3;{{ $isPep ? 'background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;' : 'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;' }}">
                                    {{ $first['label'] }}
                                </span>
                                @if($rest > 0)
                                <span style="font-size:0.5rem;font-weight:800;color:#7A7775;">+{{ $rest }}</span>
                                @endif
                            </div>
                        @else
                            <span style="color:rgba(9,20,59,0.20);font-size:0.75rem;">–</span>
                        @endif
                    </td>
                    @endforeach

                    {{-- Total dias --}}
                    <td style="position:sticky;right:0;z-index:10;background:{{ $rowBg }} !important;text-align:center;padding:6px 10px;border-left:1px solid rgba(9,20,59,0.14);border-bottom:1px solid rgba(9,20,59,0.06);font-size:13px;font-weight:800;color:#09143B;">
                        {{ ($totalesColab[$col->id] ?? 0) > 0 ? $totalesColab[$col->id] : '–' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($diasLaborais) + 2 }}" style="text-align:center;padding:32px;color:#7A7775;font-size:12px;">
                        Nenhum colaborador corresponde ao filtro.
                    </td>
                </tr>
                @endforelse

                {{-- Fila de totais diários --}}
                <tr style="background:#09143B !important;">
                    <td style="position:sticky;left:0;z-index:10;background:#09143B !important;padding:9px 14px;border-right:2px solid rgba(255,255,255,0.10);border-top:2px solid rgba(255,255,255,0.10);font-size:0.78rem;font-weight:800;color:#FFD300;white-space:nowrap;">
                        TOTAL COLABORADORES / DIA
                    </td>
                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr = $dia->toDateString();
                        $tot = $totalesDia[$fechaStr] ?? 0;
                        $esHoy = $dia->isToday();
                        $totBg = $esHoy ? '#FFD300' : 'transparent';
                        $totColor = $esHoy ? '#09143B' : ($tot > 0 ? '#FFD300' : 'rgba(255,255,255,0.25)');
                    @endphp
                    <td style="text-align:center;padding:7px 2px;border-top:2px solid rgba(255,255,255,0.10);border-right:1px solid rgba(255,255,255,0.08);background:{{ $totBg }};">
                        <span style="font-size:0.82rem;font-weight:800;color:{{ $totColor }};">{{ $tot > 0 ? $tot : '–' }}</span>
                    </td>
                    @endforeach
                    <td style="position:sticky;right:0;z-index:10;background:#09143B !important;text-align:center;padding:7px 10px;border-left:2px solid rgba(255,255,255,0.10);border-top:2px solid rgba(255,255,255,0.10);font-size:0.88rem;font-weight:800;color:#FFD300;">
                        {{ $colaboradores->count() }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Rodapé --}}
    <p class="text-xs text-right" style="color:#7A7775;">
        {{ collect($diasLaborais)->filter(fn($d) => !$d->isWeekend())->count() }} dias úteis em {{ $nomeMes }} &middot; {{ $colaboradores->count() }} colaboradores{{ $apenasAtivos ? ' ativos' : '' }}
    </p>

</div>
