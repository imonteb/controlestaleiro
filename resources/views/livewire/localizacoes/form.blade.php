<div class="w-full max-w-lg mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="map-pin" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $isEdit ? 'Editar Localização' : 'Nova Localização' }}</span>
            </div>
            <a href="{{ route('localizacoes.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                ← Voltar
            </a>
        </div>
    </div>

    {{-- Formulário --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <form wire:submit="save" class="flex flex-col gap-5">

            <div class="flex flex-col gap-1.5">
                <label for="nombre" class="cme-label">Nome <span class="text-red-500">*</span></label>
                <input id="nombre" type="text" wire:model="nombre"
                       placeholder="Ex: Funchal, Câmara de Lobos, Santana..."
                       autocomplete="off" class="cme-input">
                @error('nombre')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid rgba(9,20,59,0.08);">
                <button type="submit"
                        style="flex:1; background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; cursor:pointer; border:none;">
                    {{ $isEdit ? 'Guardar alterações' : 'Criar localização' }}
                </button>
                <a href="{{ route('localizacoes.index') }}" wire:navigate class="btn-cme-secondary">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div>
