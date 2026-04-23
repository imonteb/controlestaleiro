<div class="flex flex-col gap-6 w-full">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">Histórico Mensal EPI</h1>
            <p class="text-sm text-white/70 mt-0.5">Fichas de entrega por colaborador</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="exportarExcel" class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors shadow">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar Excel
            </button>
        </div>
    </div>

    {{-- Month Navigator --}}
    <div class="flex items-center justify-center gap-4">
        <button wire:click="mesAnterior" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <span class="text-white font-bold text-lg uppercase tracking-wide min-w-[200px] text-center">{{ $nombreMes }}</span>
        <button wire:click="mesSeguinte" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Search --}}
    <div class="max-w-md">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar colaborador..."
               class="w-full rounded-lg text-sm" style="background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.2);padding:0.625rem 0.75rem;outline:none;">
    </div>

    {{-- Fichas per collaborator --}}
    @if($colaboradoresConEntregas->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center text-gray-400">
            Nenhuma entrega de EPI registada neste mês.
        </div>
    @else
        @foreach($colaboradoresConEntregas as $colabId => $entregasColab)
            @php
                $colab = $entregasColab->first()->colaborador;
                $riscosUnion = $entregasColab->pluck('epiItem.riscos')->flatten()->unique()->filter()->values();
            @endphp
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                {{-- Collaborator header --}}
                <div class="bg-(--cme-blue) px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-500 p-2 rounded-lg">
                            <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <span class="text-white font-semibold text-lg">{{ $colab->nombre ?? '' }} {{ $colab->apellido ?? '' }}</span>
                            <span class="text-white/60 text-sm ml-3">{{ $colab->denominacion_cargo ?? '' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('epis.ficha', $colab) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors border border-white/20">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Ficha EPI
                        </a>
                        <span class="text-white/70 text-sm font-medium">{{ $entregasColab->count() }} entrega(s)</span>
                    </div>
                </div>

                {{-- Deliveries table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-12">Nº</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">EPI</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">CA</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qtd</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Data Entrega</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Ass.</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Data Devolução</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entregasColab->values() as $idx => $e)
                            <tr class="border-b border-gray-100 hover:bg-blue-50/30">
                                <td class="px-4 py-2.5 text-center text-gray-500 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900">
                                            {{ $e->epiItem->nombre ?? '—' }}
                                            @if($e->talla)
                                                <span class="ml-1 text-xs text-blue-600 font-normal">({{ $e->talla }})</span>
                                            @endif
                                        </span>
                                        @if($e->epiItem->codigo ?? null)
                                            <span class="text-xs text-gray-500 font-mono">{{ $e->epiItem->codigo }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-center text-gray-600">{{ $e->epiItem->ca_numero ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-center font-semibold text-gray-700">{{ $e->cantidad }} <span class="font-normal text-xs text-gray-400">{{ $e->epiItem->unidade ?? '' }}</span></td>
                                <td class="px-4 py-2.5 text-center text-gray-600">{{ \Carbon\Carbon::parse($e->fecha_entrega)->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($e->firma)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center text-gray-600">
                                    @if($e->fecha_devolucion)
                                        {{ \Carbon\Carbon::parse($e->fecha_devolucion)->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Riscos footer --}}
                @if($riscosUnion->isNotEmpty())
                <div class="px-6 py-3 bg-red-50 border-t border-red-100 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-red-800 uppercase tracking-wider mr-1">Riscos:</span>
                    @foreach($riscosUnion as $risco)
                        <span class="inline-flex px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs font-semibold">{{ $risco }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
