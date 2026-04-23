<div class="w-full max-w-lg mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('materiais.index') }}" wire:navigate
           class="text-blue-200 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">
                {{ $isEdit ? 'Editar Material' : 'Novo Material' }}
            </h1>
            <p class="text-sm text-blue-200 mt-0.5">
                {{ $isEdit ? 'Modificar dados do material' : 'Adicionar ao catálogo de materiais' }}
            </p>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <form wire:submit="save" class="flex flex-col gap-5">

            {{-- Código --}}
            <div class="flex flex-col gap-1.5">
                <label for="codigo" class="text-sm font-semibold text-gray-700">
                    Código <span class="text-red-500">*</span>
                </label>
                <input
                    id="codigo"
                    type="text"
                    wire:model="codigo"
                    placeholder="Ex: ELE-001, CIV-022..."
                    autocomplete="off"
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;font-family:monospace;text-transform:uppercase;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                />
                <p class="text-xs text-gray-400">Código único de referência. Será convertido para maiúsculas.</p>
                @error('codigo')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nome" class="text-sm font-semibold text-gray-700">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input
                    id="nome"
                    type="text"
                    wire:model="nome"
                    placeholder="Ex: Cabo 2.5mm², Tubo PVC 110mm..."
                    autocomplete="off"
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                />
                @error('nome')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div class="flex flex-col gap-1.5">
                <label for="descripcion" class="text-sm font-semibold text-gray-700">Descrição</label>
                <textarea
                    id="descripcion"
                    wire:model="descripcion"
                    rows="2"
                    placeholder="Informação adicional opcional..."
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;resize:vertical;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                ></textarea>
                @error('descripcion')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categoria + Unidade (en fila) --}}
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col gap-1.5">
                    <label for="categoria_id" class="text-sm font-semibold text-gray-700">Categoria</label>
                    <select
                        id="categoria_id"
                        wire:model="categoria_id"
                        style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;"
                        onfocus="this.style.borderColor='#2563EB';"
                        onblur="this.style.borderColor='#d1d5db';"
                    >
                        <option value="">— Sem categoria —</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="w-36 flex flex-col gap-1.5">
                    <label for="unidade_padrao" class="text-sm font-semibold text-gray-700">
                        Unidade <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="unidade_padrao"
                        type="text"
                        wire:model="unidade_padrao"
                        list="datalist-unidades"
                        placeholder="UND, MTS, M3..."
                        autocomplete="off"
                        style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;text-transform:uppercase;"
                        onfocus="this.style.borderColor='#2563EB';"
                        onblur="this.style.borderColor='#d1d5db';"
                    />
                    <datalist id="datalist-unidades">
                        @foreach($unidades as $un)
                            <option value="{{ $un }}">
                        @endforeach
                    </datalist>
                    @error('unidade_padrao')
                        <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Activo --}}
            <div class="flex items-center gap-3">
                <input
                    id="activo"
                    type="checkbox"
                    wire:model.live="activo"
                    class="w-4 h-4 accent-blue-600 cursor-pointer"
                />
                <label for="activo" class="text-sm font-semibold text-gray-700 cursor-pointer">
                    Material activo
                </label>
            </div>

            {{-- Motivo de baja (solo si inactivo) --}}
            @if(!$activo)
            <div class="flex flex-col gap-1.5">
                <label for="motivo_baja" class="text-sm font-semibold text-gray-700">Motivo de inactivação</label>
                <input
                    id="motivo_baja"
                    type="text"
                    wire:model="motivo_baja"
                    placeholder="Ex: Descontinuado, substituído por..."
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                />
                @error('motivo_baja')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2.5 font-bold rounded-lg shadow transition text-sm"
                    style="background-color:var(--cme-yellow);color:var(--cme-blue);"
                    onmouseover="this.style.backgroundColor='#facc15';"
                    onmouseout="this.style.backgroundColor='var(--cme-yellow)';">
                    {{ $isEdit ? 'Guardar alterações' : 'Criar material' }}
                </button>
                <a href="{{ route('materiais.index') }}" wire:navigate
                   class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-sm text-center">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div>
