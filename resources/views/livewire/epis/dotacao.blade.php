<div class="flex flex-col gap-4 w-full">

    {{-- Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Dotação EPI</span>
                <span style="color:rgba(255,255,255,0.8); font-size:11px; margin-left:8px;">Histórico acumulado por colaborador</span>
            </div>
            @if ($colaborador)
                <button wire:click="exportarExcel"
                    style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2); padding:5px 12px; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </button>
            @endif
        </div>
    </div>

    {{-- Search box --}}
    <div class="relative" style="max-width:360px;">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-4 w-4" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
        </div>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Pesquisar colaborador por nome ou número..."
            class="cme-input"
            style="padding-left:2.25rem;"
        >
    </div>

    {{-- Search results dropdown --}}
    @if ($resultados->isNotEmpty())
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="max-w-md rounded-xl shadow-xl overflow-hidden -mt-2">
            @foreach ($resultados as $r)
                <button wire:click="selectColaborador({{ $r->id }})"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-[#E4E2DF] transition-colors"
                        style="border-bottom:1px solid rgba(9,20,59,0.06);">
                    <div style="background:rgba(9,20,59,0.08); border-radius:8px; padding:6px;">
                        <svg class="h-4 w-4" style="color:#4A4845;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold" style="color:#1A1A1A;">{{ $r->nombre }} {{ $r->apellido }}</div>
                        <div class="text-xs" style="color:#7A7775;">
                            @if($r->numero_colaborador) Nº {{ $r->numero_colaborador }} · @endif
                            {{ $r->denominacion_cargo ?? '' }}
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    @endif

    {{-- Selected colaborador card --}}
    @if ($colaborador)
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">

            {{-- Colaborador header --}}
            <div style="background:#09143B;" class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div style="background:rgba(255,211,0,0.15); border-radius:6px; padding:6px;">
                        <svg style="width:16px;height:16px;color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span style="color:white; font-weight:600; font-size:13px;">{{ $colaborador->nombre }} {{ $colaborador->apellido }}</span>
                        @if ($colaborador->numero_colaborador)
                            <span style="color:rgba(255,255,255,0.5); font-size:11px; margin-left:8px;">Nº {{ $colaborador->numero_colaborador }}</span>
                        @endif
                        @if ($colaborador->denominacion_cargo)
                            <div style="color:rgba(255,255,255,0.45); font-size:10px; margin-top:2px;">{{ $colaborador->denominacion_cargo }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap justify-end">
                    @if ($totaisPendentes > 0)
                        <span class="badge-danger">{{ $totaisPendentes }} {{ $totaisPendentes == 1 ? 'item' : 'itens' }} por devolver</span>
                    @else
                        <span class="badge-ok">Tudo devolvido</span>
                    @endif
                    <a href="{{ route('epis.ficha', $colaborador) }}" target="_blank" class="btn-cme-ghost">
                        <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Ficha EPI
                    </a>
                    <button wire:click="clearColaborador" class="btn-cme-ghost">
                        Nova pesquisa
                    </button>
                </div>
            </div>

            {{-- Dotação table --}}
            @if ($dotacao->isEmpty())
                <div class="p-12 text-center" style="color:#7A7775;">
                    Nenhuma entrega de EPI registada para este colaborador.
                </div>
            @else
                <div class="overflow-x-auto" style="background:#ffffff;">
                    <table class="w-full text-sm" style="background:#ffffff;">
                        <thead>
                            <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.08);">
                                <th class="px-4 py-2.5 text-left" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">EPI</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">Ref.</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">Última Entrega</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">Total Recebido</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">Devolvido</th>
                                <th class="px-4 py-2.5 text-center" style="font-size:10px; font-weight:700; color:#4A4845; text-transform:uppercase; letter-spacing:0.05em;">Em Seu Poder</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dotacao as $idx => $d)
                                <tr class="hover:bg-[#E4E2DF]"
                                    style="background:{{ $d['en_poder'] > 0 ? 'rgba(255,211,0,0.06)' : ($idx % 2 === 0 ? '#ffffff' : '#F0EEEB') }}; border-bottom:1px solid rgba(9,20,59,0.05);">
                                    <td class="px-4 py-2.5">
                                        <div style="color:#1A1A1A; font-weight:600; font-size:12px;">{{ $d['epi']->nombre ?? '—' }}</div>
                                        @if ($d['epi']->unidade ?? null)
                                            <div style="color:#7A7775; font-size:10px;">{{ $d['epi']->unidade }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-center font-mono" style="color:#4A4845; font-size:11px;">{{ $d['epi']->codigo ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-center" style="color:#4A4845; font-size:12px;">
                                        {{ $d['ultima_entrega'] ? \Carbon\Carbon::parse($d['ultima_entrega'])->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center" style="color:#1A1A1A; font-weight:600; font-size:12px;">{{ $d['total_recibido'] }}</td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-green-700" style="font-size:12px;">{{ $d['total_devuelto'] }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if ($d['en_poder'] > 0)
                                            <span class="badge-danger" style="min-width:2rem; display:inline-flex; justify-content:center;">{{ $d['en_poder'] }}</span>
                                        @else
                                            <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background:#d4ede4; color:#0F6E56;">
                                                <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#E4E2DF; border-top:2px solid rgba(9,20,59,0.14);">
                                <td colspan="3" class="px-4 py-2.5" style="font-weight:700; color:#1A1A1A; text-transform:uppercase; font-size:10px; letter-spacing:0.05em;">Total</td>
                                <td class="px-4 py-2.5 text-center" style="font-weight:700; color:#1A1A1A; font-size:12px;">{{ $dotacao->sum('total_recibido') }}</td>
                                <td class="px-4 py-2.5 text-center font-bold text-green-700" style="font-size:12px;">{{ $dotacao->sum('total_devuelto') }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    @php $totalPendente = $dotacao->sum('en_poder'); @endphp
                                    @if ($totalPendente > 0)
                                        <span class="badge-danger" style="min-width:2rem; display:inline-flex; justify-content:center;">{{ $totalPendente }}</span>
                                    @else
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background:#d4ede4; color:#0F6E56;">
                                            <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    @elseif (! $search)
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14); border-radius:12px;" class="p-12 text-center">
            <svg class="h-12 w-12 mx-auto mb-4" style="color:#7A7775; opacity:0.4;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
            <p class="text-sm" style="color:#7A7775;">Pesquise um colaborador para ver a sua dotação de EPI</p>
        </div>
    @endif

</div>
