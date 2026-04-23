<div class="w-full max-w-3xl mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">Tipos de Trabalho</h1>
            <p class="text-sm text-blue-200 mt-1">Categorias de trabalho usadas nos PEPs</p>
        </div>
        <a href="{{ route('tipos-trabalho.crear') }}" wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 font-bold rounded-lg shadow transition text-sm"
           style="background-color:var(--cme-yellow);color:var(--cme-blue);"
           onmouseover="this.style.backgroundColor='#facc15';"
           onmouseout="this.style.backgroundColor='var(--cme-yellow)';">
            + Novo Tipo
        </a>
    </div>

    {{-- Buscar --}}
    <div style="position:relative;display:flex;align-items:center;margin-bottom:1rem;">
        <svg style="position:absolute;left:10px;color:#9ca3af;pointer-events:none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="search"
               placeholder="Pesquisar tipo de trabalho..."
               style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 10px 8px 34px;border-radius:8px;font-size:0.875rem;width:280px;outline:none;">
        @if($search)
        <button wire:click="$set('search','')" type="button" style="position:absolute;right:8px;color:#9ca3af;cursor:pointer;background:none;border:none;font-size:1rem;">✕</button>
        @endif
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow overflow-hidden">
        @if($tipos->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-base font-medium">Não há tipos de trabalho registados</p>
                <a href="{{ route('tipos-trabalho.crear') }}" wire:navigate class="mt-3 text-sm font-semibold hover:underline" style="color:var(--cme-blue);">
                    Criar o primeiro →
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Cor</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Nome</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">PEPs</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tipos as $tipo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full border border-gray-200 shadow-sm block"
                                      style="background-color: {{ $tipo->color ?? '#3B82F6' }};"></span>
                                <code class="text-xs text-gray-500">{{ $tipo->color }}</code>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-3 py-1 rounded-full text-white text-xs font-bold shadow-sm"
                                  style="background-color: {{ $tipo->color ?? '#3B82F6' }};">
                                {{ $tipo->nombre }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-gray-500 font-medium">{{ $tipo->peps_count }}</span>
                            @if($tipo->peps_count > 0)
                                <span class="text-gray-400 text-xs ml-1">PEP{{ $tipo->peps_count !== 1 ? 's' : '' }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tipos-trabalho.editar', $tipo) }}" wire:navigate
                                   class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                    Editar
                                </a>
                                <button wire:click="pedirEliminar({{ $tipo->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Modal confirmar eliminación --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">
            @php $tipo = $tipos->find($eliminandoId); @endphp
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Eliminar tipo de trabalho</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Eliminar <strong>{{ $tipo?->nombre }}</strong>?
                        @if($tipo?->peps_count > 0)
                            <br><span class="text-red-600 font-medium">⚠ Tem {{ $tipo->peps_count }} PEP{{ $tipo->peps_count !== 1 ? 's' : '' }} associado{{ $tipo->peps_count !== 1 ? 's' : '' }} — ficarão sem tipo de trabalho.</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button wire:click="cancelarEliminar"
                        class="flex-1 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button wire:click="eliminarPermanente"
                        class="flex-1 px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                    Sim, eliminar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
