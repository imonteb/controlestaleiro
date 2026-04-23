<div>
    <h1 class="text-2xl font-bold text-(--cme-blue) mb-2">Centros de Custo (PEP)</h1>
    <div class="grid gap-3 md:grid-cols-3 sm:grid-cols-2 grid-cols-1">
        @forelse ($peps as $pep)
        <div class="bg-white rounded-xl border border-(--cme-gray-medium) shadow overflow-hidden flex flex-col min-h-[220px]">
            <div class="px-4 py-3" style="background-color:#1e40af; border-bottom:1px solid #1e3a8a;">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-semibold text-white">{{ $pep->nombre }}</span>
                    <span class="ml-auto px-2 py-0.5 rounded text-white text-xs font-bold"
                          style="background-color: {{ $pep->tipoTrabajo->color ?? '#ca8a04' }};">
                        {{ $pep->tipoTrabajo->nombre ?? 'N/A' }}
                    </span>
                </div>
                <div class="text-xs text-blue-200 mt-1">{{ $pep->locacion->nombre ?? 'N/A' }}</div>
            </div>
            <div class="px-4 pt-3 pb-4 flex flex-col flex-1">

            <div class="flex-1 drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                <div class="pep-colaboradores-list min-h-[50px] mb-1 p-1 border border-dashed border-gray-300 rounded bg-gray-50 flex flex-col gap-1" data-list-type="colaborador">
                    <!-- Os colaboradores largados aparecerão aqui -->
                    <div class="text-center text-xs text-gray-400 py-1 ptr-placeholder">Arrastar pessoal aqui</div>
                </div>

                <div class="pep-vehiculos-list min-h-[50px] p-1 border border-dashed border-gray-300 rounded bg-gray-50 flex flex-col gap-1" data-list-type="vehiculo">
                    <!-- Os veículos largados aparecerão aqui -->
                    <div class="text-center text-xs text-gray-400 py-1 ptr-placeholder">Arrastar veículos aqui</div>
                </div>
            </div>
            </div>{{-- /px-4 inner --}}
        </div>
        @empty
        <div class="col-span-full p-4 text-center text-gray-500">
            Nenhum PEP registado na base de dados.
        </div>
        @endforelse
    </div>
</div>

