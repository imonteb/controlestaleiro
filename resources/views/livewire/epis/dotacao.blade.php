<div class="flex flex-col gap-6 w-full">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">Dotação EPI</h1>
            <p class="text-sm text-white/70 mt-0.5">Histórico acumulado de EPI por colaborador</p>
        </div>
        @if ($colaborador)
            <button wire:click="exportarExcel"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors shadow">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </button>
        @endif
    </div>

    {{-- Search box --}}
    <div class="relative max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-4 w-4 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
        </div>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Pesquisar colaborador por nome ou número..."
            class="w-full rounded-lg pl-9 text-sm"
            style="background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.2);padding:0.625rem 0.75rem 0.625rem 2.25rem;outline:none;"
        >
    </div>

    {{-- Search results dropdown --}}
    @if ($resultados->isNotEmpty())
        <div class="max-w-md -mt-4 bg-blue-950 rounded-xl shadow-xl overflow-hidden border border-white/10">
            @foreach ($resultados as $r)
                <button wire:click="selectColaborador({{ $r->id }})"
                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-left border-b border-white/8 last:border-0 transition-colors">
                    <div class="bg-white/10 rounded-lg p-1.5">
                        <svg class="h-4 w-4 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-semibold text-white/90 text-sm">{{ $r->nombre }} {{ $r->apellido }}</div>
                        <div class="text-xs text-white/45">
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
        <div class="bg-blue-950 rounded-2xl shadow-lg overflow-hidden border border-white/10">

            {{-- Colaborador header --}}
            <div class="bg-(--cme-blue) px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-500 p-2 rounded-lg">
                        <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-semibold text-lg">{{ $colaborador->nombre }} {{ $colaborador->apellido }}</span>
                        @if ($colaborador->numero_colaborador)
                            <span class="text-white/60 text-sm ml-3">Nº {{ $colaborador->numero_colaborador }}</span>
                        @endif
                        @if ($colaborador->denominacion_cargo)
                            <div class="text-white/50 text-xs mt-0.5">{{ $colaborador->denominacion_cargo }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if ($totaisPendentes > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/20 text-red-200 text-xs font-bold border border-red-400/30">
                            {{ $totaisPendentes }} {{ $totaisPendentes == 1 ? 'item' : 'itens' }} por devolver
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-500/20 text-green-200 text-xs font-bold border border-green-400/30">
                            Tudo devolvido
                        </span>
                    @endif
                    <a href="{{ route('epis.ficha', $colaborador) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors border border-white/20">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Ficha EPI
                    </a>
                    <button wire:click="clearColaborador"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors border border-white/20">
                        Nova pesquisa
                    </button>
                </div>
            </div>

            {{-- Dotação table --}}
            @if ($dotacao->isEmpty())
                <div class="p-12 text-center text-white/45">
                    Nenhuma entrega de EPI registada para este colaborador.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/8 bg-white/5">
                                <th class="px-4 py-3 text-left text-xs font-bold text-white/60 uppercase">EPI</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-white/60 uppercase">Ref.</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-white/60 uppercase">Última Entrega</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-white/60 uppercase">Total Recebido</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-white/60 uppercase">Devolvido</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-white/60 uppercase">Em Seu Poder</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dotacao as $d)
                                <tr class="border-b border-white/5 hover:bg-yellow-400/5 {{ $d['en_poder'] > 0 ? 'bg-amber-400/5' : '' }}">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-white/90">{{ $d['epi']->nombre ?? '—' }}</div>
                                        @if ($d['epi']->unidade ?? null)
                                            <div class="text-xs text-white/45">{{ $d['epi']->unidade }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-white/55 font-mono text-xs">{{ $d['epi']->codigo ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-white/65">
                                        {{ $d['ultima_entrega'] ? \Carbon\Carbon::parse($d['ultima_entrega'])->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white/75">{{ $d['total_recibido'] }}</td>
                                    <td class="px-4 py-3 text-center text-green-700 font-semibold">{{ $d['total_devuelto'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($d['en_poder'] > 0)
                                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-sm">
                                                {{ $d['en_poder'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-white/8 border-t-2 border-white/15">
                                <td colspan="3" class="px-4 py-3 font-bold text-white/75 uppercase text-xs tracking-wider">Total</td>
                                <td class="px-4 py-3 text-center font-bold text-white/75">{{ $dotacao->sum('total_recibido') }}</td>
                                <td class="px-4 py-3 text-center font-bold text-green-700">{{ $dotacao->sum('total_devuelto') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php $totalPendente = $dotacao->sum('en_poder'); @endphp
                                    @if ($totalPendente > 0)
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-bold text-sm">
                                            {{ $totalPendente }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
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
        <div class="bg-white/5 rounded-2xl border border-white/10 p-12 text-center text-white/40">
            <svg class="h-12 w-12 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/>
            </svg>
            <p class="text-sm">Pesquise um colaborador para ver a sua dotação de EPI</p>
        </div>
    @endif

</div>
