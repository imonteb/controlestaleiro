<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">
                {{ $isEdit ? 'Editar Colaborador' : 'Novo Colaborador' }}
            </h1>
            <p class="text-sm text-white/70 mt-0.5">{{ $isEdit ? 'Modifica os dados do colaborador' : 'Regista um novo colaborador no sistema' }}</p>
        </div>
        <a href="{{ route('colaboradores.index') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar à lista
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">

        {{-- Card Header --}}
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados do Colaborador</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nº Colaborador --}}
                <div class="flex flex-col gap-1.5">
                    <label for="numero_colaborador" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Nº Colaborador <span class="text-red-500">*</span></label>
                    <input type="text" id="numero_colaborador" wire:model="numero_colaborador" placeholder="C-001"
                           class="w-full rounded-lg text-sm font-mono bg-white text-gray-900 border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-(--cme-blue) focus:border-(--cme-blue)">
                    @error('numero_colaborador') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Cargo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="cargo" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Cargo <span class="text-red-500">*</span></label>
                    <input type="text" id="cargo" wire:model="cargo" placeholder="Electricista"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-(--cme-blue) focus:border-(--cme-blue)">
                    @error('cargo') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nombre --}}
                <div class="flex flex-col gap-1.5">
                    <label for="nome" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Nome <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" wire:model="nome" placeholder="João"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-(--cme-blue) focus:border-(--cme-blue)">
                    @error('nome') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Apellido --}}
                <div class="flex flex-col gap-1.5">
                    <label for="apelido" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Apelido <span class="text-red-500">*</span></label>
                    <input type="text" id="apelido" wire:model="apelido" placeholder="Silva"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-(--cme-blue) focus:border-(--cme-blue)">
                    @error('apelido') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Teléfono --}}
            <div class="flex flex-col gap-1.5">
                <label for="telefone" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Telefone</label>
                <input type="text" id="telefone" wire:model="telefone" placeholder="+351 912 000 000"
                       class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-(--cme-blue) focus:border-(--cme-blue)">
                @error('telefone') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            {{-- Visível no dashboard --}}
            <label class="flex items-center gap-3 cursor-pointer select-none p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                <input type="checkbox" wire:model="visible_en_dashboard" class="w-4 h-4 rounded accent-(--cme-blue)">
                <div>
                    <div class="text-sm font-semibold text-gray-800">Visível no painel</div>
                    <div class="text-xs text-gray-500">Aparece no Estaleiro e na piscina de recursos do dashboard</div>
                </div>
            </label>

            {{-- Divider --}}
            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                <a href="{{ route('colaboradores.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-(--cme-blue) hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Colaborador</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>
