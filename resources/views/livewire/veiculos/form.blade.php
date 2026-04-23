<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">
                {{ $isEdit ? 'Editar Veículo' : 'Novo Veículo' }}
            </h1>
            <p class="text-sm text-white/70 mt-0.5">{{ $isEdit ? 'Modifica os dados do veículo' : 'Regista um novo veículo na frota' }}</p>
        </div>
        <a href="{{ route('veiculos.index') }}" wire:navigate
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
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados do Veículo</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            {{-- Matrícula full width --}}
            <div class="flex flex-col gap-1.5">
                <label for="matricula" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Matrícula <span class="text-red-500">*</span></label>
                <input type="text" id="matricula" wire:model="matricula" placeholder="MA-00-AA"
                       class="w-full rounded-lg text-sm font-mono uppercase tracking-widest"
                       style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
                @error('matricula') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Marca --}}
                <div class="flex flex-col gap-1.5">
                    <label for="marca" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Marca <span class="text-red-500">*</span></label>
                    <input type="text" id="marca" wire:model="marca" placeholder="Ford"
                           class="w-full rounded-lg text-sm"
                           style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
                    @error('marca') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Modelo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="modelo" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Modelo <span class="text-red-500">*</span></label>
                    <input type="text" id="modelo" wire:model="modelo" placeholder="Transit"
                           class="w-full rounded-lg text-sm"
                           style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
                    @error('modelo') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                <a href="{{ route('veiculos.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-(--cme-blue) hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Veículo</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>
