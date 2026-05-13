<div class="w-full max-w-lg mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="cube" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $isEdit ? 'Editar Material' : 'Novo Material' }}</span>
            </div>
            <a href="{{ route('materiais.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                ← Voltar
            </a>
        </div>
    </div>

    {{-- Formulário --}}
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <form wire:submit="save" class="flex flex-col gap-5">

            {{-- Código --}}
            <div class="flex flex-col gap-1.5">
                <label for="codigo" class="cme-label">
                    Código <span class="text-red-500">*</span>
                </label>
                <input
                    id="codigo"
                    type="text"
                    wire:model="codigo"
                    placeholder="Ex: ELE-001, CIV-022..."
                    autocomplete="off"
                    class="cme-input"
                    style="font-family:monospace; text-transform:uppercase;"
                />
                <p class="cme-muted text-xs">Código único de referência. Será convertido para maiúsculas.</p>
                @error('codigo')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nome" class="cme-label">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input
                    id="nome"
                    type="text"
                    wire:model="nome"
                    placeholder="Ex: Cabo 2.5mm², Tubo PVC 110mm..."
                    autocomplete="off"
                    class="cme-input"
                />
                @error('nome')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div class="flex flex-col gap-1.5">
                <label for="descripcion" class="cme-label">Descrição</label>
                <textarea
                    id="descripcion"
                    wire:model="descripcion"
                    rows="2"
                    placeholder="Informação adicional opcional..."
                    class="cme-input"
                    style="resize:vertical;"
                ></textarea>
                @error('descripcion')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Categoria + Unidade --}}
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col gap-1.5">
                    <label for="categoria_id" class="cme-label">Categoria</label>
                    <select id="categoria_id" wire:model="categoria_id" class="cme-input">
                        <option value="">— Sem categoria —</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="w-36 flex flex-col gap-1.5">
                    <label for="unidade_padrao" class="cme-label">
                        Unidade <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="unidade_padrao"
                        type="text"
                        wire:model="unidade_padrao"
                        list="datalist-unidades"
                        placeholder="UND, MTS, M3..."
                        autocomplete="off"
                        class="cme-input"
                        style="text-transform:uppercase;"
                    />
                    <datalist id="datalist-unidades">
                        @foreach($unidades as $un)
                            <option value="{{ $un }}">
                        @endforeach
                    </datalist>
                    @error('unidade_padrao')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Activo --}}
            <div class="flex items-center gap-3">
                <input
                    id="activo"
                    type="checkbox"
                    wire:model.live="activo"
                    class="w-4 h-4 accent-[#09143B] cursor-pointer"
                />
                <label for="activo" class="text-sm font-semibold cursor-pointer" style="color:#4A4845;">
                    Material activo
                </label>
            </div>

            {{-- Motivo de inactivação --}}
            @if(!$activo)
            <div class="flex flex-col gap-1.5">
                <label for="motivo_baja" class="cme-label">Motivo de inactivação</label>
                <input
                    id="motivo_baja"
                    type="text"
                    wire:model="motivo_baja"
                    placeholder="Ex: Descontinuado, substituído por..."
                    class="cme-input"
                />
                @error('motivo_baja')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Botões --}}
            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid rgba(9,20,59,0.08);">
                <button type="submit"
                        style="background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; cursor:pointer; border:none; width:100%;">
                    {{ $isEdit ? 'Guardar alterações' : 'Criar material' }}
                </button>
                <a href="{{ route('materiais.index') }}" wire:navigate class="btn-cme-secondary">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div>
