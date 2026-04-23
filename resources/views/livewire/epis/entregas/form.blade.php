<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">Nova Entrega</h1>
            <p class="text-sm text-gray-800 mt-0.5">Registar entrega de EPI a um colaborador</p>
        </div>
        <a href="{{ route('epis.entregas.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-gray-800 font-semibold text-lg">Dados da Entrega</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            {{-- Colaborador --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold uppercase tracking-wider" style="color:#111827 !important;">Colaborador <span class="text-red-500">*</span></label>
                @php
                    $colabData = $colaboradores->map(fn($c) => [
                        'id' => $c->id,
                        'label' => trim($c->nombre.' '.($c->apellido ?? '')),
                        'search' => mb_strtolower(trim($c->nombre.' '.($c->apellido ?? ''))),
                        'num' => $c->numero_colaborador ?? '',
                        'cargo' => $c->denominacion_cargo ?? '',
                    ])->values();
                @endphp
                <div x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('colaborador_id'),
                    colaboradores: {{ json_encode($colabData) }},
                    norm(str) {
                        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    },
                    matches(c) {
                        if (!this.search) return true;
                        const s = this.norm(this.search);
                        return this.norm(c.label).includes(s) || this.norm(c.num).includes(s) || this.norm(c.cargo).includes(s);
                    },
                    select(id, label) {
                        this.selected = id;
                        this.search = label;
                        this.open = false;
                    },
                    init() {
                        this.$watch('selected', v => { if (!v) this.search = ''; });
                        if (this.selected) {
                            const c = this.colaboradores.find(c => c.id == this.selected);
                            if (c) this.search = c.label;
                        }
                    }
                }" class="relative">
                    <div class="relative">
                        <input type="text" x-model="search"
                               @focus="open = true"
                               @click="open = true"
                               @input="open = true"
                               placeholder="Pesquisar por nome, nº ou cargo..."
                               class="w-full rounded-lg text-sm pr-8"
                               style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;"
                               autocomplete="off">
                        <button type="button" @click="search = ''; selected = null; open = true" x-show="search"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                        <template x-for="c in colaboradores.filter(c => matches(c))" :key="c.id">
                            <button type="button"
                                    @click="select(c.id, c.label)"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 hover:text-blue-800 transition-colors cursor-pointer"
                                    :class="selected == c.id ? 'bg-blue-50 font-semibold text-blue-800' : 'text-gray-700'">
                                <span x-text="c.label" class="font-medium"></span>
                                <span x-show="c.num" x-text="' — ' + c.num" class="text-xs text-gray-400"></span>
                                <span x-show="c.cargo" x-text="' · ' + c.cargo" class="text-xs text-gray-400"></span>
                            </button>
                        </template>
                        <div x-show="search && colaboradores.filter(c => matches(c)).length === 0"
                             class="px-3 py-3 text-sm text-gray-400 text-center">
                            Nenhum resultado
                        </div>
                    </div>
                </div>
                @error('colaborador_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>


            {{-- Multi-EPI entregas --}}
            <div class="flex flex-col gap-4">
                <label class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#111827 !important;">EPIs a entregar</label>
                @foreach($entregas as $index => $entrega)
                    <div class="border rounded-xl p-4 mb-2 bg-gray-50 relative" wire:key="{{ $entrega['key'] ?? ($index . '-' . ($entrega['epi_item_id'] ?? 'new')) }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                            {{-- EPI selector --}}
                            <div class="md:col-span-2 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold" style="color:#111827 !important;">EPI <span class="text-red-500">*</span></label>
                                @php
                                    $epiData = $epiItems->map(fn($e) => [
                                        'id'    => $e->id,
                                        'nome'  => $e->nombre.($e->codigo ? ' ('.$e->codigo.')' : ''),
                                        'stock' => $e->stockAtual(),
                                        'uni'   => $e->unidade ?? 'un',
                                    ])->values();
                                @endphp
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ $entrega['epi_item_id'] ?? '' }}',
                                    idx: {{ $index }},
                                    items: {{ json_encode($epiData) }},
                                    norm(str) {
                                        return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                                    },
                                    matches(item) {
                                        if (!this.search) return true;
                                        return this.norm(item.nome).includes(this.norm(this.search));
                                    },
                                    select(id, nome) {
                                        this.selected = id;
                                        this.search = nome;
                                        this.open = false;
                                        $wire.set('entregas.' + this.idx + '.epi_item_id', id);
                                    },
                                    clear() {
                                        this.selected = null;
                                        this.search = '';
                                        this.open = true;
                                        $wire.set('entregas.' + this.idx + '.epi_item_id', null);
                                    },
                                    init() {
                                        if (this.selected) {
                                            const f = this.items.find(i => i.id == this.selected);
                                            if (f) this.search = f.nome;
                                        }
                                    }
                                }" class="relative">
                                    <div class="relative">
                                        <input type="text" x-model="search"
                                               @focus="open = true" @click="open = true" @input="open = true"
                                               placeholder="Pesquisar EPI..."
                                               class="w-full rounded-lg text-sm pr-8"
                                               style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;"
                                               autocomplete="off">
                                        <button type="button" @click="clear()"
                                                x-show="search" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div x-show="open" @click.outside="open = false" x-cloak
                                         class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                                        <template x-for="item in items.filter(i => matches(i))" :key="item.id">
                                            <button type="button"
                                                    @click="select(item.id, item.nome)"
                                                    class="w-full text-left px-3 py-2 text-sm transition-colors cursor-pointer"
                                                    :class="{
                                                        'bg-blue-50 font-semibold text-blue-800': selected == item.id,
                                                        'text-gray-400': item.stock <= 0 && selected != item.id,
                                                        'text-gray-700 hover:bg-blue-50 hover:text-blue-800': item.stock > 0 && selected != item.id
                                                    }">
                                                <span x-text="item.nome" class="font-medium block"></span>
                                                <span :class="item.stock > 0 ? 'text-green-600' : 'text-red-400'"
                                                      x-text="'Stock: ' + item.stock + ' ' + item.uni"
                                                      class="text-xs"></span>
                                            </button>
                                        </template>
                                        <div x-show="search && items.filter(i => matches(i)).length === 0"
                                             class="px-3 py-3 text-sm text-gray-400 text-center">Nenhum resultado</div>
                                    </div>
                                </div>
                                @error('entregas.' . $index . '.epi_item_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            {{-- Cantidad --}}
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold" style="color:#111827 !important;">Qtd <span class="text-red-500">*</span></label>
                                <input type="number" min="1" wire:model.live="entregas.{{ $index }}.cantidad" class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                @error('entregas.' . $index . '.cantidad') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            {{-- Talla --}}
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold" style="color:#111827 !important;">Talla</label>
                                @php $item = $entregas[$index]['selectedItem'] ?? null; @endphp
                                @if($item && $item->requiere_talla)
                                    <select wire:model.live="entregas.{{ $index }}.talla" class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                        <option value="">— Talla —</option>
                                        @foreach($item->tallas_disponibles ?? [] as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" wire:model.live="entregas.{{ $index }}.talla" class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                @endif
                            </div>
                            {{-- Fecha --}}
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold" style="color:#111827 !important;">Data <span class="text-red-500">*</span></label>
                                <input type="date" wire:model.live="entregas.{{ $index }}.fecha_entrega" class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                @error('entregas.' . $index . '.fecha_entrega') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            {{-- Eliminar --}}
                            <div class="flex flex-col items-end">
                                <button type="button" class="text-red-500 hover:text-red-700 text-xs font-bold" wire:click="removeEntrega('{{ $entrega['key'] }}')" @if(count($entregas) === 1) disabled @endif>Remover</button>
                            </div>
                        </div>
                        {{-- Stock --}}
                        @php $stock = $entregas[$index]['stockDisponible'] ?? null; @endphp
                        @if($stock)
                            <span class="text-xs font-semibold {{ str_contains($stock, '0') ? 'text-red-600' : 'text-green-600' }}">{{ $stock }}</span>
                        @endif
                        {{-- Campos personalizados dinámicos --}}
                        @php $item = $entregas[$index]['selectedItem'] ?? null; @endphp
                        @if($item && $item->campos_personalizados)
                        <div class="border-t border-gray-200 pt-4 mt-3">
                            <span class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider block mb-3">Dados do equipamento</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($item->campos_personalizados as $campo)
                                @php $campoNome = $campo['nome'] ?? $campo['nombre'] ?? ''; @endphp
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-semibold text-gray-700">
                                        {{ ucfirst($campoNome) }}
                                        @if($campo['requerido'] ?? false) <span class="text-red-500">*</span> @endif
                                    </label>
                                    @if(($campo['tipo'] ?? 'text') === 'textarea')
                                        <textarea wire:model="entregas.{{ $index }}.valores_personalizados.{{ $campoNome }}" rows="2"
                                            class="w-full rounded-lg text-sm resize-none" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;"></textarea>
                                    @elseif(($campo['tipo'] ?? 'text') === 'date')
                                        <input type="date" wire:model="entregas.{{ $index }}.valores_personalizados.{{ $campoNome }}"
                                            class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                    @elseif(($campo['tipo'] ?? 'text') === 'number')
                                        <input type="number" wire:model="entregas.{{ $index }}.valores_personalizados.{{ $campoNome }}"
                                            class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                    @else
                                        <input type="text" wire:model="entregas.{{ $index }}.valores_personalizados.{{ $campoNome }}" placeholder="{{ ucfirst($campoNome) }}"
                                            class="w-full rounded-lg text-sm" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        {{-- Observaciones --}}
                        <div class="flex flex-col gap-1.5 mt-2">
                            <label class="text-xs font-bold uppercase tracking-wider" style="color:#111827 !important;">Observações</label>
                            <textarea wire:model="entregas.{{ $index }}.observaciones" rows="2" placeholder="Notas adicionais..." class="w-full rounded-lg text-sm resize-none" style="background:white;color:#111827 !important;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;"></textarea>
                        </div>
                    </div>
                @endforeach
                <button type="button" class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors" wire:click="addEntrega">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Adicionar EPI
                </button>
            </div>

            {{-- Actions --}}
            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                <a href="{{ route('epis.entregas.index') }}" wire:navigate class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancelar</a>
                <button type="submit" class="inline-flex items-center gap-2 hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md" style="background:#0f2a5e;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Registar Entrega</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>
        </form>
    </div>

    <livewire:epis.entrega-modal />
</div>
