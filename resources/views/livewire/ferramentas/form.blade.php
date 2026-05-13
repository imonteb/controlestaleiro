<div class="flex flex-col gap-4 w-full max-w-2xl mx-auto">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="wrench" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $ferramenta ? 'Editar Ferramenta' : 'Nova Ferramenta' }}</span>
            </div>
            <a href="{{ route('ferramentas.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                ← Voltar
            </a>
        </div>
    </div>

    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden">
        <div class="bg-[#09143B] px-6 py-4 flex items-center gap-3">
            <div style="background:rgba(255,211,0,0.15);" class="p-2 rounded-lg">
                <svg class="h-5 w-5 text-[#FFD300]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados do Equipamento</span>
        </div>

        <form wire:submit="save" class="p-6 flex flex-col gap-8" style="background:#F0EEEB;">

            {{-- ── Identificação Geral ─────────────────────────── --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="h-1 w-8 bg-[#FFD300] rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest" style="color:#7A7775;">Identificação Geral</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 flex flex-col gap-1">
                        <label class="cme-label">Designação do Equipamento <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="designacao"
                               placeholder="Ex: Berbequim Percussão 18V"
                               class="cme-input">
                        @error('designacao') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="cme-label">Código Interno</label>
                        <input type="text" wire:model="referencia"
                               placeholder="Ex: 514"
                               class="cme-input"
                               style="color:#09143B; font-weight:700;">
                        @error('referencia') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ── Controlo Preventivo ─────────────────────────── --}}
            <div style="background:rgba(9,20,59,0.04); border:1px solid rgba(9,20,59,0.12); border-radius:12px;" class="p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="h-1 w-8 bg-[#09143B] rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest" style="color:#09143B;">Controlo Preventivo</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="cme-label">Estado Operacional</label>
                        <select wire:model="estado_operacional" class="cme-input">
                            <option value="Apto">Apto</option>
                            <option value="Não Apto">Não Apto</option>
                            <option value="Condicionado">Condicionado</option>
                            <option value="Abate">Abate</option>
                        </select>
                        @error('estado_operacional') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="cme-label">Periodicidade (Meses)</label>
                        <input type="number" wire:model="periodicidade_meses" min="1" class="cme-input">
                        @error('periodicidade_meses') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="cme-label">Documentação</label>
                        <select wire:model="tipo_documentacao" class="cme-input">
                            <option value="Manual">Manual</option>
                            <option value="Certificado">Certificado</option>
                            <option value="Ficha Técnica">Ficha Técnica</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('tipo_documentacao') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ── Detalhes Técnicos ───────────────────────────── --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="h-1 w-8 rounded-full" style="background:rgba(9,20,59,0.20);"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest" style="color:#7A7775;">Detalhes Técnicos &amp; Identificação</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach([
                        ['model' => 'familia',    'label' => 'Família',      'placeholder' => ''],
                        ['model' => 'marca',      'label' => 'Marca',        'placeholder' => ''],
                        ['model' => 'modelo',     'label' => 'Modelo',       'placeholder' => ''],
                        ['model' => 'num_serie',  'label' => 'Nº Série',     'placeholder' => ''],
                        ['model' => 'localizacao','label' => 'Localização',  'placeholder' => 'Ex: Armazém A'],
                    ] as $field)
                    <div class="flex flex-col gap-1">
                        <label class="cme-label">{{ $field['label'] }}</label>
                        <input type="text" wire:model="{{ $field['model'] }}"
                               @if($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endif
                               class="cme-input">
                        @error($field['model']) <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    @endforeach

                    <div class="flex items-center gap-3 pt-5">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="activo" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            <span class="ml-2 text-xs font-bold uppercase" style="color:#4A4845;">Ativo</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── Ações ───────────────────────────────────────── --}}
            <div class="pt-5 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('ferramentas.index') }}" wire:navigate class="btn-cme-secondary inline-flex items-center gap-2">
                    Cancelar
                </a>

                <button type="submit" class="btn-cme-primary inline-flex items-center gap-2">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ $ferramenta ? 'Guardar Alterações' : 'Criar Equipamento' }}</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>
        </form>
    </div>
</div>
