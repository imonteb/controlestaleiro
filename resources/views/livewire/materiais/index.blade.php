<div class="w-full max-w-5xl mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">Catálogo de Materiais</h1>
            <p class="text-sm text-blue-200 mt-1">Materiais disponíveis para guias de transporte</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$toggle('showImport')"
                    class="inline-flex items-center gap-2 px-4 py-2 font-bold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importar Excel
            </button>
            <a href="{{ route('materiais.crear') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 font-bold rounded-lg shadow transition text-sm"
               style="background-color:var(--cme-yellow);color:var(--cme-blue);"
               onmouseover="this.style.backgroundColor='#facc15';"
               onmouseout="this.style.backgroundColor='var(--cme-yellow)';">
                + Novo Material
            </a>
        </div>
    </div>

    {{-- Mensajes de importación --}}
    @if($importSuccess)
    <div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
        <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ $importMsg }}
    </div>
    @endif
    @if($importError)
    <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold">
        <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ $importErrorMsg }}
    </div>
    @endif

    {{-- Panel importación --}}
    @if($showImport)
    <div class="mb-4 bg-white border border-blue-200 rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-gray-800 text-sm">Importar Materiais via Excel</h3>
            <button wire:click="$set('showImport', false)" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
        </div>
        <p class="text-xs text-gray-500 mb-3">
            O ficheiro deve ter as colunas: <code class="bg-gray-100 px-1 rounded">codigo</code>, <code class="bg-gray-100 px-1 rounded">nome</code>, <code class="bg-gray-100 px-1 rounded">categoria</code>, <code class="bg-gray-100 px-1 rounded">unidade</code>, <code class="bg-gray-100 px-1 rounded">descricao</code>.
            O <strong>código</strong> é obrigatório e único — linhas com código existente serão actualizadas.
            Categorias novas são criadas automaticamente.
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('materiais.plantilla') }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Descarregar Modelo Excel
            </a>
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                       class="text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                @error('ficheiroImport') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <button wire:click="importar" wire:loading.attr="disabled"
                    class="px-4 py-2 text-xs font-bold rounded-lg shadow transition"
                    style="background-color:var(--cme-yellow);color:var(--cme-blue);">
                <span wire:loading.remove wire:target="importar">Importar</span>
                <span wire:loading wire:target="importar">A importar...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">

        {{-- Buscar --}}
        <div style="position:relative;display:flex;align-items:center;">
            <svg style="position:absolute;left:10px;color:#9ca3af;pointer-events:none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="search"
                   placeholder="Nome ou código..."
                   style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 10px 8px 34px;border-radius:8px;font-size:0.875rem;width:240px;outline:none;">
            @if($search)
            <button wire:click="$set('search','')" type="button" style="position:absolute;right:8px;color:#9ca3af;cursor:pointer;background:none;border:none;font-size:1rem;">✕</button>
            @endif
        </div>

        {{-- Filtro categoría --}}
        <select wire:model.live="filtroCategoria"
                style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 12px;border-radius:8px;font-size:0.875rem;outline:none;">
            <option value="">Todas as categorias</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
            @endforeach
        </select>

        {{-- Filtro activo --}}
        <select wire:model.live="filtroActivo"
                style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 12px;border-radius:8px;font-size:0.875rem;outline:none;">
            <option value="activos">Activos</option>
            <option value="inactivos">Inactivos</option>
            <option value="todos">Todos</option>
        </select>

        <a href="{{ route('material-categorias.index') }}" wire:navigate
           class="px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
            Gerir categorias →
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow overflow-hidden">
        @if($materiais->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-base font-medium">Sem materiais encontrados</p>
                @if(!$search && !$filtroCategoria)
                <a href="{{ route('materiais.crear') }}" wire:navigate class="mt-3 text-sm font-semibold hover:underline" style="color:var(--cme-blue);">
                    Criar o primeiro →
                </a>
                @endif
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Código</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Nome</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Categoria</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Unidade</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 uppercase text-xs tracking-wider">Estado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($materiais as $material)
                    <tr class="hover:bg-gray-50 transition-colors {{ !$material->activo ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3">
                            <code class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $material->codigo }}</code>
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-semibold text-gray-800">{{ $material->nome }}</span>
                            @if($material->descripcion)
                                <span class="block text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $material->descripcion }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($material->categoria)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">{{ $material->categoria->nome }}</span>
                            @else
                                <span class="text-gray-300 text-xs italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-gray-600 font-mono text-xs">{{ $material->unidade_padrao }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($material->activo)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Activo</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500" title="{{ $material->motivo_baja }}">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('materiais.editar', $material) }}" wire:navigate
                                   class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                    Editar
                                </a>
                                <button wire:click="pedirEliminar({{ $material->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginación --}}
            @if($materiais->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $materiais->links() }}
            </div>
            @endif
        @endif
    </div>

    {{-- Total --}}
    @if($materiais->total() > 0)
    <p class="text-xs text-gray-400 mt-3 text-right">{{ $materiais->total() }} material{{ $materiais->total() !== 1 ? 'is' : '' }} encontrado{{ $materiais->total() !== 1 ? 's' : '' }}</p>
    @endif

    {{-- Modal confirmar eliminación --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Eliminar material</h3>
                    <p class="text-sm text-gray-600 mt-1">Esta acção é irreversível. O material será eliminado permanentemente.</p>
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
