<div class="w-full max-w-5xl mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">Locais Frequentes</h1>
            <p class="text-sm text-blue-200 mt-1">Endereços guardados para guias de transporte</p>
        </div>
        <a href="{{ route('locais-frequentes.crear') }}" wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 font-bold rounded-lg shadow transition text-sm"
           style="background-color:var(--cme-yellow);color:var(--cme-blue);"
           onmouseover="this.style.backgroundColor='#facc15';"
           onmouseout="this.style.backgroundColor='var(--cme-yellow)';">
            + Novo Local
        </a>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Pesquisar por nome, localidade, morada..."
               class="flex-1 min-w-[220px] bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">

        <select wire:model.live="filtroTipo"
                class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">Todos os tipos</option>
            <option value="portugal">Portugal</option>
            <option value="internacional">Internacional</option>
        </select>

        <select wire:model.live="filtroActivo"
                class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="activos">Activos</option>
            <option value="inactivos">Inactivos</option>
            <option value="">Todos</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nome</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Endereço</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">CP</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">País</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($locais as $local)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $local->nome }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase
                                    {{ $local->tipo === 'portugal' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $local->tipo }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $local->morada }}
                            @if($local->localidade)
                                <div class="text-xs text-gray-400">{{ $local->localidade }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                            @if($local->cp4 && $local->cp3)
                                {{ $local->cp4 }}-{{ $local->cp3 }}
                                @if($local->cpalf)
                                    <div class="text-gray-400">{{ $local->cpalf }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $local->pais }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($local->activo)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Activo</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('locais-frequentes.editar', $local) }}" wire:navigate
                                   class="text-blue-500 hover:text-blue-700 text-xs font-semibold">Editar</a>
                                <button wire:click="pedirEliminar({{ $local->id }})"
                                        class="text-red-400 hover:text-red-600 text-xs font-semibold">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">Sem locais registados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginacion --}}
    <div class="mt-4">{{ $locais->links() }}</div>

    {{-- Modal confirmar eliminar --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Eliminar local?</h3>
            <p class="text-sm text-gray-500 mb-5">Esta acção não pode ser revertida.</p>
            <div class="flex gap-3 justify-end">
                <button wire:click="cancelarEliminar" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button wire:click="eliminar" class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-bold hover:bg-red-600">Eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>
