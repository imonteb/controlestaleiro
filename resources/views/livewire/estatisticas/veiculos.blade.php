<div class="p-4 lg:p-6 flex flex-col gap-5">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600">Estatísticas de Veículos</h1>
            <p style="font-size:0.88rem;color:white;font-weight:600;margin-top:2px;">PEP atribuído por veículo e dia de trabalho</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="buscar" placeholder="🔍 Procurar veículo..."
                   style="background:white;color:#111827;border:1.5px solid #9ca3af;padding:7px 12px;border-radius:8px;font-size:0.84rem;font-weight:600;min-width:180px;" />

            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:0.82rem;font-weight:700;color:white;">
                <input type="checkbox" wire:model.live="apenasAtivos" style="accent-color:#fde047;" />
                Só ativos
            </label>

            <div class="flex items-center gap-1">
                <button wire:click="mesAnterior" type="button"
                        style="background:#e5e7eb;border:1.5px solid #9ca3af;color:#111827;padding:7px 11px;border-radius:7px;font-size:0.9rem;cursor:pointer;font-weight:700;line-height:1;"
                        onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">&#8249;</button>
                <div style="background:white;border:1.5px solid #9ca3af;padding:7px 18px;border-radius:7px;font-size:0.9rem;font-weight:800;color:#1e3a8a;white-space:nowrap;min-width:140px;text-align:center;text-transform:capitalize;">
                    {{ $nomeMes }}
                </div>
                <button wire:click="proximoMes" type="button"
                        style="background:#e5e7eb;border:1.5px solid #9ca3af;color:#111827;padding:7px 11px;border-radius:7px;font-size:0.9rem;cursor:pointer;font-weight:700;line-height:1;"
                        onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">&#8250;</button>
            </div>

            <a href="{{ route('exportar.estatisticas.veiculos', ['mes' => $mes, 'ano' => $ano]) }}"
               style="display:inline-flex;align-items:center;gap:6px;background:#16a34a;color:white;font-size:0.84rem;font-weight:700;padding:7px 14px;border-radius:8px;text-decoration:none;border:1.5px solid #15803d;"
               onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                📥 Exportar Excel
            </a>
        </div>
    </div>

    {{-- Legenda --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:10px;padding:10px 16px;">
        <span style="font-size:0.8rem;font-weight:800;color:#1e3a8a;">Legenda:</span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#1e40af;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#dbeafe;border:1.5px solid #93c5fd;"></span> PEP atribuído
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#92400e;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#fef3c7;border:1.5px solid #fcd34d;"></span> Estado especial
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#6b7280;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#f3f4f6;border:1.5px solid #d1d5db;"></span> Sem atribuição
        </span>
        <span style="width:1px;height:20px;background:#cbd5e1;margin:0 2px;"></span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#7f1d1d;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#7f1d1d;border:1.5px solid #991b1b;"></span> Fim de semana
        </span>
    </div>

    {{-- Tabela --}}
    <div style="overflow-x:auto;overflow-y:visible;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
        <table style="border-collapse:collapse;width:100%;min-width:{{ count($diasLaborais) * 100 + 280 }}px;">

            <thead>
                <tr>
                    <th style="position:sticky;left:0;top:0;z-index:12;background:#1e3a8a;color:white;font-size:0.75rem;font-weight:700;padding:10px 14px;text-align:left;white-space:nowrap;min-width:220px;border-right:2px solid #1e40af;">
                        🚗 Veículo
                    </th>
                    @foreach($diasLaborais as $dia)
                    @php
                        $esWeekend = $dia->isWeekend();
                        $diaSemana = $esWeekend
                            ? ($dia->isSaturday() ? 'Sáb' : 'Dom')
                            : (['Seg','Ter','Qua','Qui','Sex'][$dia->dayOfWeekIso - 1] ?? '');
                        $esHoy  = $dia->isToday();
                        $bgDia  = $esHoy ? '#fde047' : ($esWeekend ? '#7f1d1d' : '#1e40af');
                        $textDia = $esHoy ? '#1e3a8a' : 'white';
                    @endphp
                    <th style="background:{{ $bgDia }};color:{{ $textDia }};font-size:0.72rem;font-weight:700;padding:6px 4px;text-align:center;min-width:90px;border-right:1px solid rgba(255,255,255,0.1);">
                        <div>{{ $diaSemana }}</div>
                        <div style="font-size:0.85rem;font-weight:800;margin-top:1px;">{{ $dia->format('d') }}</div>
                        @if($esWeekend)<div style="font-size:0.55rem;letter-spacing:0.03em;margin-top:1px;opacity:0.8;">FDS</div>@endif
                    </th>
                    @endforeach
                    <th style="position:sticky;right:0;top:0;z-index:12;background:#1e3a8a;color:#fde047;font-size:0.72rem;font-weight:800;padding:6px 10px;text-align:center;min-width:56px;border-left:2px solid #1e40af;white-space:nowrap;">
                        TOTAL<br><span style="font-size:0.65rem;color:#93c5fd;font-weight:600;">dias</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($veiculos as $i => $veh)
                @php $rowBg = $i % 2 === 0 ? '#ffffff' : '#f8fafc'; @endphp
                <tr style="background:{{ $rowBg }};{{ !$veh->activo ? 'opacity:0.5;' : '' }}" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='{{ $rowBg }}'">
                    {{-- Matrícula del vehículo --}}
                    <td style="position:sticky;left:0;z-index:10;background:inherit;padding:8px 14px;border-right:2px solid #e5e7eb;min-width:220px;border-bottom:1px solid #f1f5f9;">
                        <div style="font-size:0.82rem;font-weight:700;color:#1e3a8a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;" title="{{ $veh->matricula }} — {{ $veh->marca }} {{ $veh->modelo }}">
                            {{ $veh->matricula }}
                        </div>
                        @if($veh->marca || $veh->modelo)
                        <div style="font-size:0.68rem;color:#6b7280;margin-top:1px;">{{ $veh->marca }} {{ $veh->modelo }}</div>
                        @endif
                    </td>

                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr   = $dia->toDateString();
                        $cell       = $matrix[$veh->id][$fechaStr] ?? [];
                        $esHoy      = $dia->isToday();
                        $esWeekend  = $dia->isWeekend();
                        $borderRight = $esHoy ? '1px solid #fde047' : ($esWeekend ? '1px solid #fecaca' : '1px solid #f1f5f9');
                        $borderLeft  = $esHoy ? '1px solid #fde047' : ($esWeekend ? '1px solid #fecaca' : 'none');
                        $tdBg        = $esWeekend && empty($cell) ? 'background:#fef2f2;' : '';
                    @endphp
                    <td style="{{ $tdBg }}text-align:center;padding:4px 2px;border-bottom:1px solid #f1f5f9;border-right:{{ $borderRight }};border-left:{{ $borderLeft }};vertical-align:middle;">
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
                                <span style="font-size:0.5rem;font-weight:800;color:#6b7280;">+{{ $rest }}</span>
                                @endif
                            </div>
                        @else
                            <span style="color:#e5e7eb;font-size:0.75rem;">–</span>
                        @endif
                    </td>
                    @endforeach

                    {{-- Total dias --}}
                    <td style="position:sticky;right:0;z-index:10;background:inherit;text-align:center;padding:6px 10px;border-left:2px solid #e5e7eb;border-bottom:1px solid #f1f5f9;font-size:0.85rem;font-weight:800;color:#1e3a8a;">
                        {{ ($totalesVeiculos[$veh->id] ?? 0) > 0 ? $totalesVeiculos[$veh->id] : '–' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($diasLaborais) + 2 }}" style="text-align:center;padding:32px;color:#9ca3af;font-size:0.88rem;">
                        Nenhum veículo corresponde ao filtro.
                    </td>
                </tr>
                @endforelse

                {{-- Fila de totais diários --}}
                <tr style="background:#1e3a8a;">
                    <td style="position:sticky;left:0;z-index:10;background:#1e3a8a;padding:9px 14px;border-right:2px solid #1e40af;border-top:2px solid #1e40af;font-size:0.78rem;font-weight:800;color:#fde047;white-space:nowrap;">
                        TOTAL VEÍCULOS / DIA
                    </td>
                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr = $dia->toDateString();
                        $tot = $totalesDia[$fechaStr] ?? 0;
                        $esHoy = $dia->isToday();
                        $totBg = $esHoy ? '#fde047' : 'transparent';
                        $totColor = $esHoy ? '#1e3a8a' : ($tot > 0 ? '#fde047' : '#374151');
                    @endphp
                    <td style="text-align:center;padding:7px 2px;border-top:2px solid #1e40af;border-right:1px solid #1e40af;background:{{ $totBg }};">
                        <span style="font-size:0.82rem;font-weight:800;color:{{ $totColor }};">{{ $tot > 0 ? $tot : '–' }}</span>
                    </td>
                    @endforeach
                    <td style="position:sticky;right:0;z-index:10;background:#1e3a8a;text-align:center;padding:7px 10px;border-left:2px solid #1e40af;border-top:2px solid #1e40af;font-size:0.88rem;font-weight:800;color:#fde047;">
                        {{ $veiculos->count() }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Rodapé --}}
    <p class="text-xs text-gray-400 text-right">
        {{ collect($diasLaborais)->filter(fn($d) => !$d->isWeekend())->count() }} dias úteis em {{ $nomeMes }} &middot; {{ $veiculos->count() }} veículos{{ $apenasAtivos ? ' ativos' : '' }}
    </p>

</div>
