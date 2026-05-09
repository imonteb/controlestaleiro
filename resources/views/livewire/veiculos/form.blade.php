<div class="flex flex-col gap-4 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="truck" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">{{ $isEdit ? 'Editar Veículo' : 'Novo Veículo' }}</span>
            </div>
            <a href="{{ route('veiculos.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white transition-colors flex items-center gap-1">
                ← Voltar à lista
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">

        {{-- Card Header --}}
        <div style="background:#09143B !important;" class="px-6 py-4 flex items-center gap-3">
            <div class="bg-[#FFD300] p-2 rounded-lg">
                <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1m7-11h5l3 5v4h-2m-6 0H7"/></svg>
            </div>
            <span style="color:white;" class="font-semibold text-lg">Dados do Veículo</span>
        </div>

        <form id="veiForm" wire:submit.prevent="save" style="background:#F0EEEB;" class="p-6 flex flex-col gap-5">

            {{-- Matrícula full width --}}
            <div class="flex flex-col gap-1.5">
                <label for="matricula" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Matrícula <span class="text-red-500">*</span></label>
                <input type="text" id="matricula" wire:model="matricula" placeholder="MA-00-AA"
                       class="w-full text-sm font-mono uppercase tracking-widest"
                       style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                       onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                @error('matricula') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Marca --}}
                <div class="flex flex-col gap-1.5">
                    <label for="marca" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Marca <span class="text-red-500">*</span></label>
                    <input type="text" id="marca" wire:model="marca" placeholder="Ford"
                           class="w-full text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                    @error('marca') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Modelo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="modelo" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Modelo <span class="text-red-500">*</span></label>
                    <input type="text" id="modelo" wire:model="modelo" placeholder="Transit"
                           class="w-full text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                    @error('modelo') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Divider + Footer --}}
            <div class="pt-4 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('veiculos.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm transition-colors"
                   style="background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14);">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md text-sm"
                    style="background:#09143B; color:#FFD300;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Veículo</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>

<style>
#veiForm input::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
