<div class="flex flex-col gap-4 w-full max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">{{ $isEdit ? 'Editar Item' : 'Novo Item' }}</span>
            </div>
            <a href="{{ route('epis.index') }}" wire:navigate
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
                <svg class="h-5 w-5" style="color:#09143B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span style="color:white;" class="font-semibold text-lg">Dados do Equipamento</span>
        </div>

        <form id="epiForm" wire:submit.prevent="save" style="background:#F0EEEB;" class="p-6 flex flex-col gap-5">

            {{-- Nome + Tamanho (Opcional) + Código --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-1 flex flex-col gap-1.5">
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Nome / Designação <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nome" placeholder="Ex: Capacete, Luvas, Fato..."
                           class="w-full rounded-lg text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                    @error('nome') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-1.5">
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Tamanho (Opcional)</label>
                    <input type="text" wire:model="talla" placeholder="Ex: 40, XL, L..."
                           class="w-full rounded-lg text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Referência Interna</label>
                    <input type="text" wire:model="codigo" placeholder="Ref. inventário"
                           class="w-full rounded-lg text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                </div>
            </div>

            {{-- Tipo + CA + Unidade + Stock mínimo --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="flex flex-col gap-1.5">
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Categoria <span class="text-red-500">*</span></label>
                    <select wire:model.live="tipo"
                            class="w-full rounded-lg text-sm"
                            style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                            onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                        <option value="epi">EPI (Individual/Coletivo)</option>
                        <option value="saude">Saúde / Consumível</option>
                    </select>
                    @error('tipo') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>
                @if($tipo === 'epi')
                <div class="flex flex-col gap-1.5" wire:transition>
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Nº CA</label>
                    <input type="text" wire:model="ca_numero" placeholder="Certificado"
                           class="w-full rounded-lg text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                </div>
                @endif
                <div class="flex flex-col gap-1.5">
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Unidade</label>
                    <select wire:model="unidade"
                            class="w-full rounded-lg text-sm"
                            style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                            onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
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
                    <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Stock Mínimo</label>
                    <input type="number" wire:model="stock_minimo" min="0"
                           class="w-full rounded-lg text-sm"
                           style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                           onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label style="color:#4A4845 !important; background:rgba(9,20,59,0.05); border-left:2px solid rgba(9,20,59,0.25);" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded block mb-1">Descrição</label>
                <textarea wire:model="descricao" rows="2" placeholder="Observações sobre o equipamento..."
                          class="w-full rounded-lg text-sm resize-none"
                          style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.625rem 0.75rem; outline:none;"
                          onfocus="this.style.borderColor='#09143B'" onblur="this.style.borderColor='rgba(9,20,59,0.18)'"></textarea>
            </div>

            {{-- Divider: Campos personalizados --}}
            <div class="pt-4" style="border-top:1px solid rgba(9,20,59,0.08);">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider" style="color:#09143B !important;">Campos personalizados</span>
                    <button type="button" wire:click="adicionarCampo"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"
                            style="background:#F0EEEB; color:#09143B; border:1px solid rgba(9,20,59,0.18);">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Adicionar campo
                    </button>
                </div>
                @if(count($campos_personalizados) > 0)
                <div class="space-y-3">
                    @foreach($campos_personalizados as $i => $campo)
                    <div class="flex items-center gap-3 rounded-lg p-3" style="background:white; border:1px solid rgba(9,20,59,0.12);">
                        <input type="text" wire:model="campos_personalizados.{{ $i }}.nome" placeholder="Nome do campo"
                               class="flex-1 rounded-lg text-sm" style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.5rem 0.75rem; outline:none;">
                        <select wire:model="campos_personalizados.{{ $i }}.tipo"
                                class="rounded-lg text-sm" style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18) !important; padding:0.5rem 0.75rem; outline:none;">
                            <option value="text">Texto</option>
                            <option value="date">Data</option>
                            <option value="number">Número</option>
                            <option value="textarea">Texto longo</option>
                        </select>
                        <label class="flex items-center gap-1 text-xs whitespace-nowrap" style="color:#4A4845;">
                            <input type="checkbox" wire:model="campos_personalizados.{{ $i }}.requerido" class="rounded">
                            Obrigatório
                        </label>
                        <button type="button" wire:click="removerCampo({{ $i }})" class="text-red-400 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs font-bold italic" style="color:#7A7775;">Nenhum campo personalizado. Útil para número de série, marca, modelo, data de fabrico, etc.</p>
                @endif
            </div>

            @if($tipo === 'epi')
            {{-- Divider: Riscos --}}
            <div class="pt-4" style="border-top:1px solid rgba(9,20,59,0.08);" wire:transition>
                <span class="text-[10px] font-bold uppercase tracking-wider block mb-3" style="color:#09143B !important;">Riscos cobertos por este EPI</span>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($riscosDisponiveis as $risco)
                    @php $active = in_array($risco, $riscos); @endphp
                    <label wire:click="toggleRisco('{{ $risco }}')"
                           style="{{ $active ? 'background:#fdf0c2; border:1px solid rgba(133,79,11,0.30);' : 'background:white; border:1px solid rgba(9,20,59,0.18);' }} border-radius:8px; padding:8px 12px; cursor:pointer; display:flex; align-items:center; gap:8px; user-select:none;">
                        <span style="{{ $active ? 'background:#854F0B; border-color:#854F0B;' : 'background:white; border:1px solid rgba(9,20,59,0.30);' }} width:14px; height:14px; border-radius:3px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                            @if($active)
                            <svg style="width:9px;height:9px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </span>
                        <span style="{{ $active ? 'color:#854F0B;' : 'color:#1A1A1A;' }} font-size:11px; font-weight:500;">{{ $risco }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="pt-4 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('epis.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm transition-colors"
                   style="background:#E4E2DF; color:#4A4845; border:1px solid rgba(9,20,59,0.14);">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md text-sm"
                    style="background:#09143B; color:#FFD300;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar Item</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>

<style>
#epiForm input::placeholder,
#epiForm textarea::placeholder { color: #9CA3AF !important; opacity: 1 !important; }
</style>
