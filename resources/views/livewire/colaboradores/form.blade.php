<div class="flex flex-col gap-4 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="user-plus" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">{{ $isEdit ? 'Editar Colaborador' : 'Novo Colaborador' }}</span>
            </div>
            <a href="{{ route('colaboradores.index') }}" wire:navigate
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
                <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span style="color:white;" class="font-semibold text-lg">Dados do Colaborador</span>
        </div>

        <form id="colForm" wire:submit.prevent="save" style="background:#F0EEEB;" class="p-6 flex flex-col gap-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nº Colaborador --}}
                <div class="flex flex-col gap-1.5">
                    <label for="numero_colaborador" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Nº Colaborador <span class="text-red-500">*</span></label>
                    <input type="text" id="numero_colaborador" wire:model="numero_colaborador" placeholder="C-001"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px;"
                           class="w-full text-sm font-mono px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgba(9,20,59,0.20)]">
                    @error('numero_colaborador') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Cargo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="cargo" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Cargo <span class="text-red-500">*</span></label>
                    <input type="text" id="cargo" wire:model="cargo" placeholder="Electricista"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px;"
                           class="w-full text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgba(9,20,59,0.20)]">
                    @error('cargo') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nombre --}}
                <div class="flex flex-col gap-1.5">
                    <label for="nome" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" wire:model="nome" placeholder="João"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px;"
                           class="w-full text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgba(9,20,59,0.20)]">
                    @error('nome') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Apellido --}}
                <div class="flex flex-col gap-1.5">
                    <label for="apelido" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Apelido <span class="text-red-500">*</span></label>
                    <input type="text" id="apelido" wire:model="apelido" placeholder="Silva"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px;"
                           class="w-full text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgba(9,20,59,0.20)]">
                    @error('apelido') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Teléfono --}}
            <div class="flex flex-col gap-1.5">
                <label for="telefone" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Telefone</label>
                <input type="text" id="telefone" wire:model="telefone" placeholder="+351 912 000 000"
                       style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; border-radius:8px;"
                       class="w-full text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgba(9,20,59,0.20)]">
                @error('telefone') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            {{-- Visível no dashboard --}}
            <label style="background:white; border:1px solid rgba(9,20,59,0.14); border-radius:8px;" class="flex items-center gap-3 cursor-pointer select-none p-3 hover:bg-[#EEECEA] transition-colors">
                <input type="checkbox" wire:model="visible_en_dashboard" class="w-4 h-4 rounded" style="accent-color:#09143B;">
                <div>
                    <div style="color:#1A1A1A;" class="text-sm font-semibold">Visível no painel</div>
                    <div style="color:#7A7775;" class="text-xs mt-0.5">Aparece no Estaleiro e na piscina de recursos do dashboard</div>
                </div>
            </label>

            {{-- Divider + Footer --}}
            <div class="pt-4 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('colaboradores.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm transition-colors"
                   style="background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14);">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md text-sm"
                    style="background:#09143B; color:#FFD300;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Colaborador</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>

<style>
#colForm input::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
