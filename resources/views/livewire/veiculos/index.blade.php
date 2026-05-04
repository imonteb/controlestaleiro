<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-500 tracking-wide">Veículos</h1>
            <p class="text-sm text-white/80 mt-0.5">Gestão da frota de veículos registados</p>
        </div>
        <a href="{{ route('veiculos.crear') }}" wire:navigate
           class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-(--cme-blue) font-bold py-2.5 px-5 rounded-lg transition-colors shadow-md whitespace-nowrap">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Veículo
        </a>
    </div>

    {{-- Filtro de estado + Búsqueda --}}
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <div class="flex items-center gap-2">
        <button wire:click="$set('filtro','activos')"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
            style="{{ $filtro === 'activos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
            Ativos
        </button>
        <button wire:click="$set('filtro','inactivos')"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
            style="{{ $filtro === 'inactivos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
            Inativos
        </button>
        <button wire:click="$set('filtro','todos')"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border"
            style="{{ $filtro === 'todos' ? 'background:#0f2a5e;color:#eab308;border-color:#0f2a5e;' : 'background:white;color:#6b7280;border-color:#d1d5db;' }}">
            Todos
        </button>
        </div>
        {{-- Buscar --}}
        <div class="flex items-center gap-2">
        <div style="position:relative;display:flex;align-items:center;">
            <svg style="position:absolute;left:10px;color:#9ca3af;pointer-events:none;" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="pesquisa" type="search"
                   placeholder="Pesquisar..."
                   style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 10px 8px 34px;border-radius:8px;font-size:0.875rem;width:240px;outline:none;">
            @if($pesquisa)
            <button wire:click="$set('pesquisa','')" type="button" style="position:absolute;right:8px;color:#9ca3af;cursor:pointer;background:none;border:none;font-size:1rem;">✕</button>
            @endif
        </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-x-auto border border-gray-100">

        {{-- Table header bar --}}
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-500 p-2 rounded-lg">
                    <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
                </div>
                <span class="text-white font-semibold text-lg">Frota de Veículos</span>
            </div>
            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
                {{ $veiculos->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-(--cme-blue)/20">
                        @php
                            $cols = [
                                'matricula' => 'Matrícula',
                                'marca'     => 'Marca',
                                'modelo'    => 'Modelo',
                            ];
                        @endphp
                        @foreach($cols as $col => $label)
                        <th class="px-6 py-3.5 text-left border-b border-white/8">
                            <button wire:click="sort('{{ $col }}')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-white/60 uppercase tracking-wider hover:text-white/90 transition-colors group">
                                {{ $label }}
                                <span class="flex flex-col leading-none">
                                    <svg class="h-2.5 w-2.5 {{ $sortBy === $col && $sortDir === 'asc' ? 'text-yellow-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4l4 6H4z"/></svg>
                                    <svg class="h-2.5 w-2.5 {{ $sortBy === $col && $sortDir === 'desc' ? 'text-yellow-500' : 'text-gray-300 group-hover:text-gray-400' }}" fill="currentColor" viewBox="0 0 16 16"><path d="M8 12L4 6h8z"/></svg>
                                </span>
                            </button>
                        </th>
                        @endforeach
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-white/60 uppercase tracking-wider border-b border-white/8 whitespace-nowrap">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($veiculos as $veiculo)
                    <tr class="{{ !$veiculo->activo ? 'opacity-50' : ($loop->index % 2 === 0 ? 'bg-white/0' : 'bg-white/3') }} hover:bg-yellow-400/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-2 font-mono font-bold text-purple-800 bg-purple-50 border border-purple-200 px-3 py-1.5 rounded-lg text-sm tracking-widest w-fit">
                                        <svg class="h-3.5 w-3.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
                                        {{ $veiculo->matricula }}
                                    </span>
                                    <button wire:click="editarLink({{ $veiculo->id }})"
                                            title="{{ $veiculo->link_seguros ? 'Editar link de seguros' : 'Adicionar link de seguros' }}"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:6px;border:1px solid {{ $veiculo->link_seguros ? '#bae6fd' : '#e5e7eb' }};background:{{ $veiculo->link_seguros ? '#e0f2fe' : '#f9fafb' }};color:{{ $veiculo->link_seguros ? '#0284c7' : '#9ca3af' }};cursor:pointer;">
                                        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                                    </button>
                                </div>
                                @if(!$veiculo->activo)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $veiculo->marca }}</div>
                            @if($veiculo->motivo_baja)
                                <div class="text-xs text-red-500 mt-0.5">{{ $veiculo->motivo_baja }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-white/70">{{ $veiculo->modelo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('veiculos.editar', $veiculo->id) }}" wire:navigate
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-200 text-xs font-semibold transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </a>
                                @if($veiculo->activo)
                                    <button wire:click="desativar({{ $veiculo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border"
                                        style="background:#fff7ed;color:#c2410c;border-color:#fed7aa;">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Desativar
                                    </button>
                                @else
                                    <button wire:click="reactivar({{ $veiculo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border"
                                        style="background:#f0fdf4;color:#15803d;border-color:#bbf7d0;">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Reativar
                                    </button>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <button wire:click="pedirEliminar({{ $veiculo->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-semibold transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Painel inline: motivo de baixa --}}
                    @if($desativandoId === $veiculo->id)
                    <tr class="border-l-4 border-orange-400" style="background:#fff7ed;">
                        <td colspan="4" class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start gap-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-orange-700">
                                        Dar baixa: <strong>{{ $veiculo->matricula }} – {{ $veiculo->marca }}</strong>
                                    </p>
                                    <p class="text-xs text-orange-600 mt-0.5 mb-2">O veículo ficará marcado como inativo e não aparecerá no painel de atribuição. Pode ser reativado a qualquer momento.</p>
                                    <label class="text-xs font-bold text-orange-700 uppercase tracking-wider">Motivo da baixa <span class="font-normal text-orange-500">(opcional)</span></label>
                                    <textarea wire:model="motivoBaixa" rows="2"
                                        placeholder="Ex: Vendido, em reparação prolongada, baixa definitiva..."
                                        class="w-full text-sm border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none mt-1"
                                        style="background:white;color:#111827;"></textarea>
                                </div>
                                <div class="flex gap-2 sm:mt-6">
                                    <button wire:click="confirmarDesativar"
                                        class="px-4 py-2 rounded-lg text-sm font-bold text-white transition-colors"
                                        style="background:#c2410c;">
                                        Confirmar Baixa
                                    </button>
                                    <button wire:click="$set('desativandoId', null)"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    {{-- Painel inline: link de seguros --}}
                    @if($editandoLinkId === $veiculo->id)
                    <tr style="border-left:4px solid #38bdf8;background:#f0f9ff;">
                        <td colspan="4" class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start gap-3">
                                <div class="flex-1">
                                    <p style="font-size:0.875rem;font-weight:600;color:#0369a1;margin-bottom:4px;">
                                        🛡️ Link de seguros — <strong>{{ $veiculo->matricula }}</strong>
                                    </p>
                                    <p style="font-size:0.75rem;color:#0284c7;margin-bottom:8px;">Este link aparecerá no telemóvel ao lado da matrícula. Deixe em branco para remover.</p>
                                    <input wire:model="editandoLink" type="url"
                                           placeholder="https://..."
                                           style="width:100%;font-size:0.875rem;border:1px solid #7dd3fc;border-radius:8px;padding:8px 12px;background:white;color:#111827;outline:none;">
                                    @error('editandoLink')
                                        <p style="font-size:0.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex gap-2 sm:mt-7">
                                    <button wire:click="guardarLink"
                                        style="padding:8px 16px;border-radius:8px;font-size:0.875rem;font-weight:700;color:white;background:#0284c7;border:none;cursor:pointer;">
                                        Guardar
                                    </button>
                                    <button wire:click="$set('editandoLinkId', null)"
                                        style="padding:8px 16px;border-radius:8px;font-size:0.875rem;font-weight:600;color:#374151;background:white;border:1px solid #d1d5db;cursor:pointer;">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
                                <p class="text-sm font-medium">Nenhum veículo registado</p>
                                <a href="{{ route('veiculos.crear') }}" wire:navigate class="text-(--cme-blue) hover:underline text-xs">Criar o primeiro →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($veiculos->hasPages())
        <div class="px-4 py-3 border-t border-white/5">
            {{ $veiculos->links() }}
        </div>
        @endif
    </div>

    {{-- Modal: Confirmar eliminación permanente --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.55);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 flex items-center gap-3" style="background:#7f1d1d;">
                <svg class="h-6 w-6 text-red-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <h3 class="text-white font-bold text-lg">Eliminar permanentemente</h3>
            </div>
            <div class="px-6 py-5">
                <p class="text-gray-700 text-sm">Esta ação <strong>não pode ser desfeita</strong>. O veículo será eliminado definitivamente do sistema juntamente com todos os seus registos associados.</p>
                <p class="text-red-700 text-sm mt-3 font-semibold">Tem a certeza de que deseja continuar?</p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button wire:click="cancelarEliminar"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button wire:click="eliminarPermanente"
                    class="px-4 py-2 rounded-lg text-white text-sm font-bold transition-colors"
                    style="background:#dc2626;">
                    Sim, eliminar permanentemente
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
