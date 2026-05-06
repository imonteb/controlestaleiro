<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">{{ $ferramenta ? 'Editar' : 'Nova' }} Ferramenta</h1>
            <p class="text-sm text-white/70 mt-0.5 font-medium">Registo de equipamento no livro de ferramentas</p>
        </div>
        <a href="{{ route('ferramentas.index') }}" wire:navigate
            class="inline-flex items-center gap-1.5 text-gray-400 hover:text-(--cme-blue) text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    <div class="bg-white/5 rounded-2xl shadow-lg overflow-hidden border border-white/10">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="text-yellow-400 font-semibold text-lg">Dados do Equipamento</span>
        </div>

        <form wire:submit="save" class="p-6 flex flex-col gap-8">

            {{-- ── Identificação Geral ─────────────────────────── --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="h-1 w-8 bg-yellow-500 rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white/50">Identificação Geral</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-white/50 uppercase tracking-wider">
                            Designação do Equipamento <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="designacao"
                               placeholder="Ex: Berbequim Percussão 18V"
                               class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-medium text-white placeholder:text-white/30 focus:border-(--cme-blue) focus:bg-white/12 focus:outline-none focus:ring-0 transition-colors">
                        @error('designacao') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-white/50 uppercase tracking-wider">Código Interno</label>
                        <input type="text" wire:model="referencia"
                               placeholder="Ex: 514"
                               class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-bold text-yellow-400 placeholder:text-white/30 focus:border-(--cme-blue) focus:bg-white/12 focus:outline-none focus:ring-0 transition-colors">
                        @error('referencia') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- ── Controlo Preventivo ─────────────────────────── --}}
            <div class="bg-blue-500/10 rounded-2xl p-5 border border-blue-400/20 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="h-1 w-8 bg-blue-400 rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-300">Controlo Preventivo</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Estado Operacional</label>
                        <select wire:model="estado_operacional"
                                class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-bold text-white focus:border-blue-400 focus:outline-none focus:ring-0 transition-colors">
                            <option value="Apto">Apto</option>
                            <option value="Não Apto">Não Apto</option>
                            <option value="Condicionado">Condicionado</option>
                            <option value="Abate">Abate</option>
                        </select>
                        @error('estado_operacional') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Periodicidade (Meses)</label>
                        <input type="number" wire:model="periodicidade_meses" min="1"
                               class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-bold text-white focus:border-blue-400 focus:outline-none focus:ring-0 transition-colors">
                        @error('periodicidade_meses') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Documentação</label>
                        <select wire:model="tipo_documentacao"
                                class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-bold text-white focus:border-blue-400 focus:outline-none focus:ring-0 transition-colors">
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
                    <div class="h-1 w-8 bg-white/30 rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white/40">Detalhes Técnicos & Identificação</span>
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
                        <label class="text-[11px] font-bold text-white/50 uppercase tracking-wider">{{ $field['label'] }}</label>
                        <input type="text" wire:model="{{ $field['model'] }}"
                               @if($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endif
                               class="w-full rounded-xl border-2 border-white/15 bg-white/8 px-3 py-2.5 text-sm font-medium text-white placeholder:text-white/30 focus:border-(--cme-blue) focus:bg-white/12 focus:outline-none focus:ring-0 transition-colors">
                        @error($field['model']) <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    @endforeach

                    <div class="flex items-center gap-3 pt-5">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="activo" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            <span class="ml-2 text-xs font-bold uppercase text-white/50">Ativo</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── Ações ───────────────────────────────────────── --}}
            <div class="border-t border-white/10 pt-5 flex justify-end gap-3">
                <a href="{{ route('ferramentas.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 py-2.5 px-5 rounded-xl font-semibold text-sm text-white/60 bg-white/8 hover:bg-white/12 border border-white/10 transition-colors">
                    Cancelar
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 text-white font-bold py-2.5 px-7 rounded-xl transition-colors shadow-md hover:opacity-90"
                    style="background:#0f2a5e;">
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
