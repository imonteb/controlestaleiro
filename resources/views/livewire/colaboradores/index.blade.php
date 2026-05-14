<div class="flex flex-col gap-4 w-full max-w-6xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="users" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Colaboradores</span>
            </div>
            <a href="{{ route('colaboradores.crear') }}" wire:navigate
               style="display:inline-flex; align-items:center; gap:6px; background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                + Novo Colaborador
            </a>
        </div>
    </div>

    {{-- Filtro de estado + Pesquisa --}}
    <div class="cme-card flex items-center justify-between gap-2 flex-wrap" style="border:1px solid rgba(9,20,59,0.14); border-radius:10px; padding:10px 14px;">
        <div class="flex items-center gap-2">
            <button wire:click="$set('filtro','activos')"
                style="{{ $filtro === 'activos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Ativos
            </button>
            <button wire:click="$set('filtro','inactivos')"
                style="{{ $filtro === 'inactivos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Inativos
            </button>
            <button wire:click="$set('filtro','todos')"
                style="{{ $filtro === 'todos' ? 'background:#09143B; color:#FFD300; border-color:#09143B;' : 'background:white; color:#4A4845; border-color:rgba(9,20,59,0.18);' }}"
                class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors border">
                Todos
            </button>
        </div>
        <div class="relative flex items-center">
            <svg class="absolute left-2.5 h-3.5 w-3.5 text-[#7A7775] pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
            <input wire:model.live.debounce.300ms="pesquisa" type="search"
                   id="colaboradores-search"
                   placeholder="Pesquisar..."
                   class="cme-input"
                   style="padding:6px 28px; width:220px;">
            @if($pesquisa)
            <button wire:click="$set('pesquisa','')" type="button" class="absolute right-2 text-[#7A7775] hover:text-[#1A1A1A] cursor-pointer text-base leading-none">✕</button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div class="cme-card rounded-xl overflow-hidden" style="border:1px solid rgba(9,20,59,0.14);">

        {{-- Table header bar --}}
        <div style="background:#09143B !important;" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div style="background:#FFD300;" class="p-2 rounded-lg">
                    <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 00-3-3.87"/></svg>
                </div>
                <span style="color:white;" class="font-semibold text-lg">Lista de Pessoal</span>
            </div>
            <span style="background:rgba(255,255,255,0.15); color:white;" class="text-xs font-bold px-3 py-1 rounded-full">
                {{ $colaboradores->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="cme-surface">
                    <tr class="cme-surface">
                        @php
                            $cols = [
                                'numero_colaborador' => 'Nº',
                                'nombre'             => 'Nome Completo',
                                'denominacion_cargo' => 'Cargo',
                                'telefono'           => 'Telefone',
                            ];
                        @endphp
                        @foreach($cols as $col => $label)
                        <th class="px-6 py-3.5 text-left border-b border-[rgba(9,20,59,0.08)]">
                            <button wire:click="sort('{{ $col }}')"
                                class="cme-muted inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider transition-colors group">
                                {{ $label }}
                                <span class="flex flex-col leading-none">
                                    <svg class="h-2.5 w-2.5 {{ $sortBy === $col && $sortDir === 'asc' ? 'text-[#FFD300]' : 'text-[rgba(9,20,59,0.25)]' }}" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4l4 6H4z"/></svg>
                                    <svg class="h-2.5 w-2.5 {{ $sortBy === $col && $sortDir === 'desc' ? 'text-[#FFD300]' : 'text-[rgba(9,20,59,0.25)]' }}" fill="currentColor" viewBox="0 0 16 16"><path d="M8 12L4 6h8z"/></svg>
                                </span>
                            </button>
                        </th>
                        @endforeach
                        <th class="cme-muted px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider border-b border-[rgba(9,20,59,0.08)] whitespace-nowrap">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($colaboradores as $colaborador)
                    <tr class="{{ !$colaborador->activo ? 'opacity-50' : '' }} transition-colors group {{ $loop->index % 2 === 0 ? 'cme-row-even' : 'cme-row-odd' }}">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center gap-1.5 font-mono font-bold px-2.5 py-1 rounded-lg text-xs tracking-wider w-fit" style="background:rgba(9,20,59,0.08); color:#09143B;">
                                    {{ $colaborador->numero_colaborador }}
                                </span>
                                @if(!$colaborador->activo)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0" style="background:{{ $colaborador->activo ? '#09143B' : '#9ca3af' }};">
                                    {{ strtoupper(substr($colaborador->nombre, 0, 1)) }}{{ strtoupper(substr($colaborador->apellido, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="cme-text font-semibold truncate max-w-45"
                                         title="{{ $colaborador->nombre }} {{ $colaborador->apellido }}">
                                        {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                    </div>
                                    @if($colaborador->motivo_baja)
                                        <div class="text-xs text-red-500 mt-0.5">{{ $colaborador->motivo_baja }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background:rgba(9,20,59,0.08); color:#09143B; border:1px solid rgba(9,20,59,0.15);">
                                {{ $colaborador->denominacion_cargo }}
                            </span>
                        </td>
                        <td class="cme-mid px-6 py-4">
                            @if($colaborador->telefono)
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-[#7A7775]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $colaborador->telefono }}
                                </span>
                            @else
                                <span style="color:rgba(9,20,59,0.20);">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('colaboradores.editar', $colaborador->id) }}" wire:navigate
                                   title="Ver / Editar colaborador"
                                   class="btn-cme-secondary inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                {{-- Toggle visible en dashboard --}}
                                <button wire:click="toggleVisibleDashboard({{ $colaborador->id }})"
                                    title="{{ $colaborador->visible_en_dashboard ? 'Visível no dashboard — clique para ocultar' : 'Oculto do dashboard — clique para mostrar' }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    style="min-width:76px; justify-content:center; {{ $colaborador->visible_en_dashboard ? 'background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25);' : 'background:#E4E2DF; color:#7A7775; border:1px solid rgba(9,20,59,0.14);' }}">
                                    @if($colaborador->visible_en_dashboard)
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Visível
                                    @else
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95M6.34 6.34A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.21 2.592M6.34 6.34L3 3m3.34 3.34l11.32 11.32M17.66 17.66L21 21"/></svg>
                                        Oculto
                                    @endif
                                </button>
                                @if($colaborador->activo)
                                <button wire:click="desativar({{ $colaborador->id }})"
                                    title="Desativar colaborador"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold transition-colors"
                                    style="background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                                @else
                                <button wire:click="reactivar({{ $colaborador->id }})"
                                    title="Reativar colaborador"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-semibold transition-colors"
                                    style="background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25);">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endif
                                @if(auth()->user()->isAdmin())
                                <button wire:click="pedirEliminar({{ $colaborador->id }})"
                                    title="Eliminar colaborador"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                    style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20);">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Painel inline: motivo de baixa --}}
                    @if($desativandoId === $colaborador->id)
                    <tr class="border-l-4 border-orange-400 bg-orange-50">
                        <td colspan="5" class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start gap-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-orange-700">
                                        Dar baixa: <strong>{{ $colaborador->nombre }} {{ $colaborador->apellido }}</strong>
                                    </p>
                                    <p class="text-xs text-orange-600 mt-0.5 mb-2">O colaborador ficará marcado como inativo e não aparecerá no painel de atribuição. Pode ser reativado a qualquer momento.</p>
                                    <label class="text-xs font-bold text-orange-700 uppercase tracking-wider">Motivo da baixa <span class="font-normal text-orange-500">(opcional)</span></label>
                                    <textarea wire:model="motivoBaixa" rows="2"
                                        placeholder="Ex: Fim de contrato, despedimento, renúncia voluntária..."
                                        class="w-full text-sm border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none mt-1 bg-white text-gray-900"></textarea>
                                </div>
                                <div class="flex gap-2 sm:mt-6">
                                    <button wire:click="confirmarDesativar"
                                        class="px-4 py-2 rounded-lg text-sm font-bold text-white transition-colors bg-orange-700 hover:bg-orange-800">
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
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="cme-muted flex flex-col items-center gap-3">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 00-3-3.87"/></svg>
                                <p class="text-sm font-medium">Nenhum colaborador registado</p>
                                <a href="{{ route('colaboradores.crear') }}" wire:navigate class="text-xs hover:underline" style="color:#09143B;">Criar o primeiro →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($colaboradores->hasPages())
        <div class="px-4 py-3 border-t border-[rgba(9,20,59,0.08)]">
            {{ $colaboradores->links() }}
        </div>
        @endif
    </div>

    {{-- Modal: Confirmar eliminação permanente --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 flex items-center gap-3 bg-red-900">
                <svg class="h-6 w-6 text-red-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <h3 class="text-white font-bold text-lg">Eliminar permanentemente</h3>
            </div>
            @if($eliminacaoBloqueada)
            <div class="px-6 py-5">
                <div class="flex items-start gap-3 bg-amber-50 border border-amber-300 rounded-xl p-4">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <div>
                        <p class="text-amber-800 font-bold text-sm">Não é possível eliminar este colaborador.</p>
                        <p class="text-amber-700 text-sm mt-1">Este colaborador tem histórico de atividade no sistema (atribuições, EPIs, ferramentas, guias ou incidentes). Para proteger a integridade dos dados, apenas pode ser <strong>desativado</strong>.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-5 flex justify-end">
                <button wire:click="cancelarEliminar"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Fechar
                </button>
            </div>
            @else
            <div class="px-6 py-5">
                <p class="text-gray-700 text-sm">Esta ação <strong>não pode ser desfeita</strong>. O colaborador não tem histórico de atividade e será eliminado definitivamente do sistema.</p>
                <p class="text-red-700 text-sm mt-3 font-semibold">Tem a certeza de que deseja continuar?</p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button wire:click="cancelarEliminar"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button wire:click="eliminarPermanente"
                    class="px-4 py-2 rounded-lg text-white text-sm font-bold transition-colors bg-red-600 hover:bg-red-700">
                    Sim, eliminar permanentemente
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
