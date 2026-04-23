<div class="flex flex-col gap-6 w-full max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">
                {{ $isEdit ? 'Editar Item' : 'Novo Item' }}
            </h1>
            <p class="text-sm text-slate-200 font-bold mt-0.5">{{ $isEdit ? 'Modifica os dados do registo' : 'Regista um novo ativo no inventário (EPI, Saúde, Incêndio ou Ferramenta)' }}</p>
        </div>
        <a href="{{ route('epis.index') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-slate-200 hover:text-white text-sm font-bold transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar à lista
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados do Equipamento</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            {{-- Nome + Tamanho (Opcional) + Código --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-1 flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Nome / Designação <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="nome" placeholder="Ex: Capacete, Luvas, Fato..."
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    @error('nome') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Tamanho (Opcional)</label>
                    <input type="text" wire:model="talla" placeholder="Ej: 40, XL, L..."
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Referência Interna</label>
                    <input type="text" wire:model="codigo" placeholder="Ref. inventário"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>
            </div>

            {{-- Tipo + CA + Unidade + Stock mínimo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Categoria <span class="text-red-500">*</span></label>
                    <select wire:model.live="tipo" class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="epi">EPI (Individual/Coletivo)</option>
                        <option value="ferramenta">Ferramenta</option>
                        <option value="saude">Saúde / Consumível</option>
                        <option value="incendio">Incêndio / Extintores</option>
                    </select>
                    @error('tipo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @if($tipo === 'epi')
                <div class="flex flex-col gap-1.5" wire:transition>
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Nº CA</label>
                    <input type="text" wire:model="ca_numero" placeholder="Certificado"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                @endif
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Unidade</label>
                    <select wire:model="unidade" class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="unidade">Unidade</option>
                        <option value="par">Par</option>
                        <option value="conjunto">Conjunto</option>
                        <option value="kit">Kit</option>
                        <option value="caixa">Caixa</option>
                        <option value="metro">Metro</option>
                        <option value="rolo">Rolo</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Stock Mínimo</label>
                    <input type="number" wire:model="stock_minimo" min="0"
                           class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-(--cme-blue) uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Descrição</label>
                <textarea wire:model="descricao" rows="2" placeholder="Observações sobre o equipamento..."
                          class="w-full rounded-lg text-sm bg-white text-gray-900 border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 resize-none"></textarea>
            </div>

            {{-- Divider: Campos personalizados --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Campos personalizados</span>
                    <button type="button" wire:click="adicionarCampo"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-(--cme-blue) bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Adicionar campo
                    </button>
                </div>
                @if(count($campos_personalizados) > 0)
                <div class="space-y-3">
                    @foreach($campos_personalizados as $i => $campo)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <input type="text" wire:model="campos_personalizados.{{ $i }}.nome" placeholder="Nome do campo"
                               class="flex-1 rounded-lg text-sm" style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                        <select wire:model="campos_personalizados.{{ $i }}.tipo"
                                class="rounded-lg text-sm" style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.5rem 0.75rem;outline:none;">
                            <option value="text">Texto</option>
                            <option value="date">Data</option>
                            <option value="number">Número</option>
                            <option value="textarea">Texto longo</option>
                        </select>
                        <label class="flex items-center gap-1 text-xs text-gray-600 whitespace-nowrap">
                            <input type="checkbox" wire:model="campos_personalizados.{{ $i }}.requerido" class="rounded border-gray-300">
                            Obrigatório
                        </label>
                        <button type="button" wire:click="removerCampo({{ $i }})" class="text-red-400 hover:text-red-600">
                             <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-600 font-bold italic">Nenhum campo personalizado. Útil para número de série, marca, modelo, data de fabrico, etc.</p>
                @endif
            </div>

            @if($tipo === 'epi')
            {{-- Divider: Riscos --}}
            <div class="border-t border-gray-200 pt-4" wire:transition>
                <span class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider block mb-3">Riscos cobertos por este EPI</span>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($riscosDisponiveis as $risco)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-colors
                        {{ in_array($risco, $riscos) ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-white border-gray-300 text-gray-800 font-bold hover:bg-gray-50' }}">
                        <input type="checkbox" wire:click="toggleRisco('{{ $risco }}')" {{ in_array($risco, $riscos) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-amber-600">
                        <span class="text-xs font-medium">{{ $risco }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                <a href="{{ route('epis.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-(--cme-blue) font-black py-2.5 px-6 rounded-lg transition-colors shadow-md">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Item</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>
