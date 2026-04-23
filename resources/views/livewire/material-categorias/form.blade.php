<div class="w-full max-w-lg mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('material-categorias.index') }}" wire:navigate
           class="text-blue-200 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">
                {{ $isEdit ? 'Editar Categoria' : 'Nova Categoria' }}
            </h1>
            <p class="text-sm text-blue-200 mt-0.5">
                {{ $isEdit ? 'Modificar dados da categoria' : 'Criar uma nova categoria de materiais' }}
            </p>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <form wire:submit="save" class="flex flex-col gap-5">

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nome" class="text-sm font-semibold text-gray-700">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input
                    id="nome"
                    type="text"
                    wire:model="nome"
                    placeholder="Ex: Eléctrico, Civil, Ferramentas..."
                    autocomplete="off"
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                />
                @error('nome')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ordem --}}
            <div class="flex flex-col gap-1.5">
                <label for="ordem" class="text-sm font-semibold text-gray-700">
                    Ordem de apresentação
                </label>
                <input
                    id="ordem"
                    type="number"
                    wire:model="ordem"
                    min="0"
                    max="999"
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;border-radius:0.5rem;width:8rem;outline:none;font-size:0.875rem;"
                    onfocus="this.style.borderColor='#2563EB';"
                    onblur="this.style.borderColor='#d1d5db';"
                />
                <p class="text-xs text-gray-400">Número menor aparece primeiro na lista.</p>
                @error('ordem')
                    <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Activo --}}
            <div class="flex items-center gap-3">
                <input
                    id="activo"
                    type="checkbox"
                    wire:model="activo"
                    class="w-4 h-4 accent-blue-600 cursor-pointer"
                />
                <label for="activo" class="text-sm font-semibold text-gray-700 cursor-pointer">
                    Categoria activa
                </label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2.5 font-bold rounded-lg shadow transition text-sm"
                    style="background-color:var(--cme-yellow);color:var(--cme-blue);"
                    onmouseover="this.style.backgroundColor='#facc15';"
                    onmouseout="this.style.backgroundColor='var(--cme-yellow)';">
                    {{ $isEdit ? 'Guardar alterações' : 'Criar categoria' }}
                </button>
                <a href="{{ route('material-categorias.index') }}" wire:navigate
                   class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-sm text-center">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div>
