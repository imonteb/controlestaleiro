<div class="p-4 lg:p-6 flex flex-col gap-5">

    {{-- Cabecera de página --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600">Resumo Mensal</h1>
            <p style="font-size:0.88rem;color:white;font-weight:600;margin-top:2px;">Atribuições por PEP e dia de trabalho</p>
        </div>

        {{-- Controles: mes y filtro de locación --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Filtro de localização --}}
            <select wire:model.live="filterLocalizacao"
                    style="background:white;color:#111827;border:1.5px solid #9ca3af;padding:7px 12px;border-radius:8px;font-size:0.84rem;font-weight:700;cursor:pointer;">
                <option value="">Todas as localizações</option>
                @foreach($locacoes as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->nombre }}</option>
                @endforeach
            </select>

            {{-- Navegación mes --}}
            <div class="flex items-center gap-1">
                <button wire:click="mesAnterior" type="button"
                        style="background:#e5e7eb;border:1.5px solid #9ca3af;color:#111827;padding:7px 11px;border-radius:7px;font-size:0.9rem;cursor:pointer;font-weight:700;line-height:1;"
                        onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">&#8249;</button>
                <div style="background:white;border:1.5px solid #9ca3af;padding:7px 18px;border-radius:7px;font-size:0.9rem;font-weight:800;color:#1e3a8a;white-space:nowrap;min-width:140px;text-align:center;text-transform:capitalize;">
                    {{ $nombreMes }}
                </div>
                <button wire:click="proximoMes" type="button"
                        style="background:#e5e7eb;border:1.5px solid #9ca3af;color:#111827;padding:7px 11px;border-radius:7px;font-size:0.9rem;cursor:pointer;font-weight:700;line-height:1;"
                        onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">&#8250;</button>
            </div>
        </div>
    </div>

    {{-- Leyenda --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:10px;padding:10px 16px;">
        <span style="font-size:0.8rem;font-weight:800;color:#1e3a8a;">Nº colaboradores:</span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#374151;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#f3f4f6;border:1.5px solid #d1d5db;"></span> 0 (sem dados)
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#166534;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#dcfce7;border:1.5px solid #86efac;"></span> 1–3
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#14532d;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#4ade80;border:1.5px solid #22c55e;"></span> 4–7
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#fff;background:#16a34a;padding:3px 8px 3px 4px;border-radius:6px;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#16a34a;border:1.5px solid #15803d;"></span> 8+
        </span>
        <span style="width:1px;height:20px;background:#cbd5e1;margin:0 2px;"></span>
            <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#92400e;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#fef3c7;border:1.5px solid #fcd34d;"></span> Estado especial
        </span>
        <span style="width:1px;height:20px;background:#cbd5e1;margin:0 2px;"></span>
        <span style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:700;color:#7f1d1d;">
            <span style="display:inline-block;width:26px;height:20px;border-radius:4px;background:#7f1d1d;border:1.5px solid #991b1b;"></span> Fim de semana (com dados)
        </span>
    </div>

    {{-- Tabla --}}
    <div class="flex-1 min-h-0 overflow-x-auto overflow-y-auto" style="border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
        <table style="border-collapse:collapse;width:100%;min-width:{{  count($diasLaborais) * 52 + 280 }}px;">

            {{-- Cabecera --}}
            <thead style="position:sticky;top:0;z-index:20;">
                <tr style="position:sticky;top:0;z-index:20;">
                    {{-- Primera celda: etiqueta PEP --}}
                    <th style="position:sticky;left:0;top:0;z-index:12;background:#1e3a8a;color:white;font-size:0.75rem;font-weight:700;padding:10px 14px;text-align:left;white-space:nowrap;min-width:240px;border-right:2px solid #1e40af;">
                        PEP / Localização
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
                    <th style="background:{{ $bgDia }};color:{{ $textDia }};font-size:0.72rem;font-weight:700;padding:6px 4px;text-align:center;min-width:46px;max-width:54px;border-right:1px solid rgba(255,255,255,0.1);">
                        <div>{{ $diaSemana }}</div>
                        <div style="font-size:0.85rem;font-weight:800;margin-top:1px;">{{ $dia->format('d') }}</div>
                        @if($esWeekend)<div style="font-size:0.55rem;letter-spacing:0.03em;margin-top:1px;opacity:0.8;">FDS</div>@endif
                    </th>
                    @endforeach
                    {{-- Total columna --}}
                    <th style="position:sticky;right:0;top:0;z-index:12;background:#1e3a8a;color:#fde047;font-size:0.72rem;font-weight:800;padding:6px 10px;text-align:center;min-width:56px;border-left:2px solid #1e40af;white-space:nowrap;">
                        TOTAL<br><span style="font-size:0.65rem;color:#93c5fd;font-weight:600;">mês</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                {{-- Filas de PEPs --}}
                @forelse($peps as $i => $pep)
                @php $rowBg = $i % 2 === 0 ? '#ffffff' : '#f8fafc'; @endphp
                <tr style="background:{{ $rowBg }};" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='{{ $rowBg }}'">
                    {{-- PEP + locacion --}}
                    <td style="position:sticky;left:0;z-index:10;background:inherit;padding:8px 14px;border-right:2px solid #e5e7eb;min-width:240px;border-bottom:1px solid #f1f5f9;">
                        <div style="font-size:0.82rem;font-weight:700;color:#1e3a8a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;" title="{{ $pep->nombre }}">
                            {{ $pep->nombre }}
                        </div>
                        @if($pep->localizacao)
                        <div style="font-size:0.7rem;color:#6b7280;margin-top:1px;">
                            📍 {{ $pep->localizacao->nombre }}
                        </div>
                        @endif
                    </td>

                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr   = $dia->toDateString();
                        $cell       = $matrix[$pep->id][$fechaStr] ?? null;
                        $numCol     = $cell['col'] ?? 0;
                        $numVeh     = $cell['veh'] ?? 0;
                        $esHoy      = $dia->isToday();
                        $esWeekend  = $dia->isWeekend();

                        if ($numCol === 0)      { $cellBg = $esWeekend ? '#fef2f2' : 'transparent'; $cellColor = '#d1d5db'; $cellFw = '400'; }
                        elseif ($numCol <= 3)   { $cellBg = '#dcfce7'; $cellColor = '#166534'; $cellFw = '700'; }
                        elseif ($numCol <= 7)   { $cellBg = '#4ade80'; $cellColor = '#14532d'; $cellFw = '800'; }
                        else                    { $cellBg = '#16a34a'; $cellColor = 'white'; $cellFw = '800'; }

                        $borderRight = $esHoy ? '1px solid #fde047' : ($esWeekend ? '1px solid #fecaca' : '1px solid #f1f5f9');
                        $borderLeft  = $esHoy ? '1px solid #fde047' : ($esWeekend ? '1px solid #fecaca' : 'none');
                        $tdBg        = $esWeekend && $numCol === 0 ? 'background:#fef2f2;' : '';
                    @endphp
                    <td style="{{ $tdBg }}text-align:center;padding:6px 2px;border-bottom:1px solid #f1f5f9;border-right:{{ $borderRight }};border-left:{{ $borderLeft }};">
                        @if($numCol > 0)
                        <div style="display:inline-flex;flex-direction:column;align-items:center;justify-content:center;background:{{ $cellBg }};color:{{ $cellColor }};font-weight:{{ $cellFw }};font-size:0.8rem;border-radius:6px;min-width:30px;padding:3px 4px;line-height:1.2;">
                            {{ $numCol }}
                            @if($numVeh > 0)
                            <span style="font-size:0.6rem;font-weight:600;color:{{ $numCol > 7 ? 'rgba(255,255,255,0.75)' : '#6b7280' }};margin-top:1px;">🚗{{ $numVeh }}</span>
                            @endif
                        </div>
                        @else
                        <span style="color:#e5e7eb;font-size:0.75rem;">–</span>
                        @endif
                    </td>
                    @endforeach

                    {{-- Total mes para este PEP --}}
                    <td style="position:sticky;right:0;z-index:10;background:inherit;text-align:center;padding:6px 10px;border-left:2px solid #e5e7eb;border-bottom:1px solid #f1f5f9;font-size:0.85rem;font-weight:800;color:#1e3a8a;">
                        {{ $totalesPep[$pep->id] > 0 ? $totalesPep[$pep->id] : '–' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($diasLaborais) + 2 }}" style="text-align:center;padding:32px;color:#9ca3af;font-size:0.88rem;">
                        Nenhum PEP ativo corresponde ao filtro.
                    </td>
                </tr>
                @endforelse

                {{-- Separador antes de estados especiales --}}
                @if(count($estadosActivos) > 0)
                <tr>
                    <td colspan="{{ count($diasLaborais) + 2 }}"
                        style="background:#f8fafc;padding:5px 14px;font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:0.08em;text-transform:uppercase;border-top:2px solid #e5e7eb;border-bottom:1px solid #e5e7eb;">
                        Estados especiais
                    </td>
                </tr>
                @endif

                {{-- Filas de estados especiales --}}
                @foreach($estadosActivos as $estado)
                @php
                    $estadoColors = [
                        'baixa'           => ['bg'=>'#fef2f2','fg'=>'#991b1b','label'=>'🤒'],
                        'licenca'         => ['bg'=>'#fffbeb','fg'=>'#92400e','label'=>'📜'],
                        'ferias'          => ['bg'=>'#ecfdf5','fg'=>'#065f46','label'=>'🏖'],
                        'consulta_medica' => ['bg'=>'#eff6ff','fg'=>'#1e40af','label'=>'🏥'],
                        'formacao'        => ['bg'=>'#f5f3ff','fg'=>'#5b21b6','label'=>'📚'],
                        'reparacao'       => ['bg'=>'#fff7ed','fg'=>'#9a3412','label'=>'🔧'],
                    ];
                    $ec = $estadoColors[$estado] ?? ['bg'=>'#f3f4f6','fg'=>'#374151','label'=>''];
                @endphp
                <tr style="background:{{ $ec['bg'] }};">
                    <td style="position:sticky;left:0;z-index:10;background:{{ $ec['bg'] }};padding:7px 14px;border-right:2px solid #e5e7eb;min-width:240px;border-bottom:1px solid rgba(0,0,0,0.04);">
                        <div style="font-size:0.8rem;font-weight:700;color:{{ $ec['fg'] }};">
                            {{ $ec['label'] }} {{ $estadoLabels[$estado] }}
                        </div>
                    </td>
                    @foreach($diasLaborais as $dia)
                    @php
                        $fechaStr = $dia->toDateString();
                        $cnt = $estadoMatrix[$estado][$fechaStr] ?? 0;
                    @endphp
                    <td style="text-align:center;padding:6px 2px;border-bottom:1px solid rgba(0,0,0,0.04);border-right:1px solid rgba(0,0,0,0.05);">
                        @if($cnt > 0)
                        <span style="display:inline-block;background:{{ $ec['fg'] }};color:white;font-size:0.75rem;font-weight:800;border-radius:5px;min-width:22px;padding:2px 4px;">{{ $cnt }}</span>
                        @else
                        <span style="color:#d1d5db;font-size:0.72rem;">–</span>
                        @endif
                    </td>
                    @endforeach
                    <td style="position:sticky;right:0;z-index:10;background:{{ $ec['bg'] }};text-align:center;padding:6px 10px;border-left:2px solid #e5e7eb;border-bottom:1px solid rgba(0,0,0,0.04);font-size:0.85rem;font-weight:800;color:{{ $ec['fg'] }};">
                        {{ array_sum($estadoMatrix[$estado] ?? []) ?: '–' }}
                    </td>
                </tr>
                @endforeach

                {{-- Fila de totales diarios --}}
                <tr style="background:#1e3a8a;">
                    <td style="position:sticky;left:0;z-index:10;background:#1e3a8a;padding:9px 14px;border-right:2px solid #1e40af;border-top:2px solid #1e40af;font-size:0.78rem;font-weight:800;color:#fde047;white-space:nowrap;">
                        TOTAL COLABORADORES / DIA
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
                        {{ array_sum($totalesDia) ?: '–' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Footer info --}}
    <p class="text-xs text-gray-400 text-right">
        {{ collect($diasLaborais)->filter(fn($d) => !$d->isWeekend())->count() }} dias úteis em {{ $nombreMes }} &middot; Os fins de semana com dados são mostrados a vermelho escuro &middot; Apenas são mostrados PEPs ativos
    </p>

</div>
