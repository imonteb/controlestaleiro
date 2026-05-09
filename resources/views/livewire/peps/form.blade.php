<div class="flex flex-col gap-4 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="clipboard" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">{{ $isEdit ? 'Editar PEP' : 'Novo PEP' }}</span>
            </div>
            <a href="{{ route('peps.index') }}" wire:navigate
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
                <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span style="color:white;" class="font-semibold text-lg">Dados do PEP</span>
        </div>

        <form id="pepForm" wire:submit.prevent="save" style="background:#F0EEEB;" class="p-6 flex flex-col gap-5">

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nome" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Nome do PEP <span class="text-red-500">*</span></label>
                <input type="text" id="nome" wire:model="nome" placeholder="Ex: PEP-001 Eletricidade"
                       class="w-full text-sm"
                       style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                       onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                @error('nome') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Localização --}}
                <div class="flex flex-col gap-1.5">
                    <label for="localizacao_id" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Localização</label>
                    <select id="localizacao_id" wire:model="localizacao_id"
                            class="w-full text-sm"
                            style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                            onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                        <option value="">— Sem localização —</option>
                        @foreach($localizacoes as $locacion)
                            <option value="{{ $locacion->id }}">{{ $locacion->nombre }}</option>
                        @endforeach
                    </select>
                    @error('localizacao_id') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-11a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Tipo de Trabajo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="tipo_trabalho_id" style="color:#4A4845 !important;" class="text-[11px] font-bold uppercase tracking-wider block mb-1">Tipo de Trabalho</label>
                    <select id="tipo_trabalho_id" wire:model="tipo_trabalho_id"
                            class="w-full text-sm"
                            style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none; border-radius:8px;"
                            onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                        <option value="">— Sem tipo —</option>
                        @foreach($tiposTrabalho as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipo_trabalho_id') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Preview tipo trabalho color --}}
            @if($tipo_trabalho_id && $tiposTrabalho->find($tipo_trabalho_id))
            @php $selectedTipo = $tiposTrabalho->find($tipo_trabalho_id); @endphp
            <div class="flex items-center gap-2 text-sm">
                <span class="px-3 py-1 rounded-full text-white text-xs font-bold" style="background-color: {{ $selectedTipo->color ?? '#09143B' }};">
                    {{ $selectedTipo->nombre }}
                </span>
                <span style="color:#7A7775;">← etiqueta que aparecerá no dashboard</span>
            </div>
            @endif

            {{-- Divider + Footer --}}
            <div class="pt-4 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('peps.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm transition-colors"
                   style="background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14);">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md text-sm"
                    style="background:#09143B; color:#FFD300;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar PEP</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>

<style>
#pepForm input::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
