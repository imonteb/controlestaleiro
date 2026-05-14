<div class="w-full max-w-lg mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="tag" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $isEdit ? 'Editar Tipo de Trabalho' : 'Novo Tipo de Trabalho' }}</span>
            </div>
            <a href="{{ route('tipos-trabalho.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                ← Voltar
            </a>
        </div>
    </div>

    {{-- Formulário --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <form wire:submit="save" class="flex flex-col gap-5">

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nombre" class="cme-label">Nome <span class="text-red-500">*</span></label>
                <input id="nombre" type="text" wire:model="nombre"
                       placeholder="Ex: Construção Naval, Manutenção Elétrica..."
                       autocomplete="off" class="cme-input">
                @error('nombre')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cor --}}
            <div class="flex flex-col gap-1.5">
                <label for="color" class="cme-label">Cor da etiqueta <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-3">
                    <input id="color" type="color" wire:model.live="color"
                           style="width:3rem; height:2.75rem; border:1px solid rgba(9,20,59,0.16); border-radius:0.5rem; padding:0.125rem; cursor:pointer; background:white;">
                    <input type="text" wire:model.live="color" placeholder="#3B82F6" maxlength="7"
                           class="cme-input w-32" style="font-family:monospace;">
                    <span class="px-3 py-1.5 rounded-full text-white text-sm font-bold shadow-sm"
                          style="background-color: {{ $color }};">
                        {{ $nombre ?: 'Pré-visualização' }}
                    </span>
                </div>
                @error('color')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
                <p class="cme-muted text-xs">Esta cor será usada como fundo da etiqueta no dashboard e nos PEPs.</p>
            </div>

            {{-- Botões --}}
            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid rgba(9,20,59,0.08);">
                <button type="submit"
                        style="flex:1; background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; cursor:pointer; border:none;">
                    {{ $isEdit ? 'Guardar alterações' : 'Criar tipo de trabalho' }}
                </button>
                <a href="{{ route('tipos-trabalho.index') }}" wire:navigate class="btn-cme-secondary">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div>
