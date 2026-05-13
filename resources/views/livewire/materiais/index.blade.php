<div class="w-full max-w-5xl mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="cube" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Catálogo de Materiais</span>
                <span style="color:rgba(255,255,255,0.5); font-size:11px;">— Materiais para guias de transporte</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="$toggle('showImport')" class="btn-cme-ghost text-[11px]">
                    📥 Importar Excel
                </button>
                <a href="{{ route('materiais.crear') }}" wire:navigate
                   style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                    + Novo Material
                </a>
            </div>
        </div>
    </div>

    {{-- Flash mensagens --}}
    @if($importSuccess)
    <div class="badge-ok px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-semibold">{{ $importMsg }}</span>
    </div>
    @endif
    @if($importError)
    <div class="badge-danger px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span class="text-sm font-semibold">{{ $importErrorMsg }}</span>
    </div>
    @endif

    {{-- Panel importação --}}
    @if($showImport)
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-sm" style="color:#1A1A1A;">Importar Materiais via Excel</h3>
            <button wire:click="$set('showImport', false)" class="text-lg leading-none" style="color:#7A7775; background:none; border:none; cursor:pointer;">&times;</button>
        </div>
        <p class="text-xs mb-3" style="color:#7A7775;">
            O ficheiro deve ter as colunas: <code class="bg-gray-100 px-1 rounded">codigo</code>, <code class="bg-gray-100 px-1 rounded">nome</code>, <code class="bg-gray-100 px-1 rounded">categoria</code>, <code class="bg-gray-100 px-1 rounded">unidade</code>, <code class="bg-gray-100 px-1 rounded">descricao</code>.
            O <strong>código</strong> é obrigatório e único — linhas com código existente serão actualizadas.
            Categorias novas são criadas automaticamente.
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex flex-col gap-2 w-full">
                <label class="text-[10px] font-black uppercase tracking-widest" style="color:#4A4845;">Selecionar Ficheiro (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroImport" accept=".xlsx,.xls,.csv"
                       class="block w-full text-sm text-[#4A4845] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.18)] rounded-lg bg-white">
                @error('ficheiroImport') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center justify-between gap-4 pt-3 w-full" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('materiais.plantilla') }}" target="_blank"
                   class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-colors hover:opacity-70"
                   style="color:#09143B;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Modelo Excel
                </a>
                <button wire:click="importar" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 font-bold py-2.5 px-6 rounded-xl transition-all shadow-md text-xs uppercase tracking-widest disabled:opacity-50"
                        style="background:#09143B; color:#FFD300;">
                    <svg wire:loading.remove wire:target="importar" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="importar" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="importar">Importar</span>
                    <span wire:loading wire:target="importar">A processar...</span>
                </button>
            </div>
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

    {{-- Tabela --}}
    <div class="rounded-xl border border-[rgba(9,20,59,0.14)] overflow-hidden">
        @if($materiais->isEmpty())
            <div class="flex flex-col items-center justify-center py-16" style="color:#7A7775;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:#C4C2BF;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-base font-medium">Sem materiais encontrados</p>
                @if(!$search && !$filtroCategoria)
                <a href="{{ route('materiais.crear') }}" wire:navigate class="mt-3 text-sm font-semibold hover:underline" style="color:#09143B;">
                    Criar o primeiro →
                </a>
                @endif
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th class="text-left px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Código</th>
                        <th class="text-left px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Nome</th>
                        <th class="text-left px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Categoria</th>
                        <th class="text-left px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Unidade</th>
                        <th class="text-left px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @foreach($materiais as $material)
                    @php $rowBg = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB'; @endphp
                    <tr class="hover:bg-[#E4E2DF] transition-colors {{ !$material->activo ? 'opacity-60' : '' }}" style="background:{{ $rowBg }};">
                        <td class="px-5 py-3">
                            <span class="badge-info">{{ $material->codigo }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-semibold" style="color:#1A1A1A;">{{ $material->nome }}</span>
                            @if($material->descripcion)
                                <span class="block text-xs mt-0.5 truncate max-w-xs" style="color:#7A7775;">{{ $material->descripcion }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($material->categoria)
                                <span class="badge-info">{{ $material->categoria->nome }}</span>
                            @else
                                <span class="text-xs italic" style="color:#C4C2BF;">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-mono text-xs" style="color:#4A4845;">{{ $material->unidade_padrao }}</td>
                        <td class="px-5 py-3">
                            @if($material->activo)
                                <span class="badge-ok">Activo</span>
                            @else
                                <span class="badge-neutral" title="{{ $material->motivo_baja }}">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('materiais.editar', $material) }}" wire:navigate class="btn-cme-secondary">
                                    Editar
                                </a>
                                <button wire:click="pedirEliminar({{ $material->id }})"
                                        style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer;">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginação --}}
            @if($materiais->hasPages())
            <div class="px-5 py-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                {{ $materiais->links() }}
            </div>
            @endif
        @endif
    </div>

    {{-- Total --}}
    @if($materiais->total() > 0)
    <p class="text-xs mt-3 text-right" style="color:#7A7775;">{{ $materiais->total() }} material{{ $materiais->total() !== 1 ? 'is' : '' }} encontrado{{ $materiais->total() !== 1 ? 's' : '' }}</p>
    @endif

    {{-- Modal confirmar eliminação --}}
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
