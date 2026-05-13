<div class="w-full max-w-2xl mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="map-pin" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">{{ $isEdit ? 'Editar Local' : 'Novo Local Frequente' }}</span>
            </div>
            <a href="{{ route('locais-frequentes.index') }}" wire:navigate
               style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                ← Voltar
            </a>
        </div>
    </div>

    @php
        $inputStyle = "background:white;color:#1A1A1A;border:1px solid rgba(9,20,59,0.16);padding:0.5rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;";
        $selectStyle = "background:white;color:#1A1A1A;border:1px solid rgba(9,20,59,0.16);padding:0.5rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;";
        $labelStyle = "display:block;font-size:0.7rem;font-weight:700;color:#4A4845;text-transform:uppercase;margin-bottom:0.25rem;letter-spacing:0.05em;";
    @endphp

    <form wire:submit.prevent="save" class="flex flex-col gap-5">

        {{-- Nome e Tipo --}}
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);" class="rounded-xl p-5 flex flex-col gap-4">
            <div>
                <label style="{{ $labelStyle }}">Nome do Local *</label>
                <input wire:model="nome" type="text" placeholder="Ex: Estaleiro CME, Obra Câmara de Lobos..."
                       style="{{ $inputStyle }}">
                @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="{{ $labelStyle }}">Tipo</label>
                <div class="flex gap-4 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="tipo" type="radio" value="portugal" class="accent-[#09143B]">
                        <span class="text-sm font-semibold" style="color:#4A4845;">Portugal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="tipo" type="radio" value="internacional" class="accent-[#09143B]">
                        <span class="text-sm font-semibold" style="color:#4A4845;">Internacional</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Buscador Portugal --}}
        @if($tipo === 'portugal')
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);" class="rounded-xl p-5 flex flex-col gap-4">
            <div style="color:#09143B; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Localização Portugal</div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Distrito --}}
                <div>
                    <label style="{{ $labelStyle }}">Distrito</label>
                    <select wire:model.live="selectedDd" style="{{ $selectStyle }}">
                        <option value="">— Seleccionar —</option>
                        @foreach($distritos as $d)
                            <option value="{{ $d->dd }}">{{ $d->desig }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Concelho (opcional) --}}
                <div>
                    <label style="{{ $labelStyle }}">Concelho <span class="normal-case font-normal" style="color:#7A7775;">(opcional)</span></label>
                    <select wire:model.live="selectedCc" style="{{ $selectStyle }}"
                            {{ ! $selectedDd ? 'disabled' : '' }}>
                        <option value="">— Todos —</option>
                        @foreach($concelhos as $c)
                            <option value="{{ $c->cc }}">{{ $c->desig }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Pesquisa por texto --}}
            @if($selectedDd)
            <div x-data="{ query: '', results: [], open: false }"
                 @click.outside="open = false">
                <label style="{{ $labelStyle }}">Pesquisar zona, localidade ou rua</label>
                <div class="relative">
                    <input type="text"
                           x-model="query"
                           @input.debounce.350ms="
                               if (query.length >= 2) {
                                   $wire.call('searchZona', query).then(r => { results = r; open = r.length > 0; });
                               } else {
                                   results = []; open = false;
                               }"
                           placeholder="Ex: São Roque, Ginja, Rua da Levada..."
                           style="{{ $inputStyle }}"
                           autocomplete="off">

                    <div x-show="open" x-transition
                         style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid rgba(9,20,59,0.14);border-radius:0.75rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);z-index:50;overflow:hidden;">
                        <template x-for="r in results" :key="r.id">
                            <button type="button"
                                    @click="$wire.call('selecionarZona', r.id, r.localidade, r.cp4, r.cp3, r.cpalf, r.art_local ?? ''); query = r.label; open = false;"
                                    style="display:flex;justify-content:space-between;align-items:center;width:100%;padding:0.625rem 0.875rem;text-align:left;background:white;border:none;border-bottom:1px solid #f3f4f6;cursor:pointer;gap:1rem;"
                                    onmouseover="this.style.background='#F0EEEB';" onmouseout="this.style.background='white';">
                                <span style="font-size:0.8rem;color:#1A1A1A;font-weight:600;" x-text="r.label"></span>
                                <span style="font-size:0.7rem;color:#9ca3af;font-family:monospace;white-space:nowrap;" x-text="r.cp"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            @else
            <p class="text-xs italic" style="color:#7A7775;">Selecciona primeiro o distrito para pesquisar.</p>
            @endif

            {{-- CP selecionado --}}
            @if($cp4 && $cp3)
            <div style="background:#d4ede4; border:1px solid rgba(15,110,86,0.25); border-radius:12px;" class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="font-mono font-bold text-lg" style="color:#0F6E56;">{{ $cp4 }}-{{ $cp3 }}</div>
                    <div>
                        <div class="text-xs font-bold" style="color:#0F6E56;">{{ $cpalf }}</div>
                        <div class="text-xs" style="color:#0F6E56;">{{ $localidade }}</div>
                    </div>
                </div>
                <button type="button" wire:click="limparZona"
                        style="background:none;border:none;color:#7A7775;font-size:1rem;cursor:pointer;padding:0.25rem;">✕</button>
            </div>

            {{-- Pesquisa de rua (opcional) --}}
            <div x-data="{ queryRua: '', resultsRua: [], openRua: false }"
                 @click.outside="openRua = false">
                <label style="{{ $labelStyle }}">Pesquisar rua <span class="normal-case font-normal" style="color:#7A7775;">(opcional)</span></label>
                <div class="relative">
                    <input type="text"
                           x-model="queryRua"
                           @input.debounce.350ms="
                               if (queryRua.length >= 2) {
                                   $wire.call('searchRua', queryRua).then(r => { resultsRua = r; openRua = r.length > 0; });
                               } else {
                                   resultsRua = []; openRua = false;
                               }"
                           placeholder="Ex: Rua da Levada, Caminho..."
                           style="{{ $inputStyle }}"
                           autocomplete="off">

                    <div x-show="openRua" x-transition
                         style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid rgba(9,20,59,0.14);border-radius:0.75rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);z-index:50;overflow:hidden;">
                        <template x-for="r in resultsRua" :key="r.id">
                            <button type="button"
                                    @click="$wire.call('selecionarRua', r.id, r.nome, r.localidade, r.cp4, r.cp3, r.cpalf); queryRua = r.nome; openRua = false;"
                                    style="display:flex;justify-content:space-between;align-items:center;width:100%;padding:0.625rem 0.875rem;text-align:left;background:white;border:none;border-bottom:1px solid #f3f4f6;cursor:pointer;gap:1rem;"
                                    onmouseover="this.style.background='#F0EEEB';" onmouseout="this.style.background='white';">
                                <span style="font-size:0.8rem;color:#1A1A1A;font-weight:600;" x-text="r.nome"></span>
                                <span style="font-size:0.7rem;color:#9ca3af;font-family:monospace;white-space:nowrap;" x-text="r.cp"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Internacional --}}
        @if($tipo === 'internacional')
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);" class="rounded-xl p-5 flex flex-col gap-4">
            <div style="color:#09143B; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Localização Internacional</div>

            <div>
                <label style="{{ $labelStyle }}">País *</label>
                <input wire:model="pais" type="text" placeholder="Ex: França, Espanha..."
                       style="{{ $inputStyle }}">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label style="{{ $labelStyle }}">Cidade / Localidade</label>
                    <input wire:model="localidade" type="text" placeholder="Ex: Paris, Lyon..."
                           style="{{ $inputStyle }}">
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Código Postal</label>
                    <input wire:model="cp4" type="text" placeholder="Ex: 75001"
                           style="{{ $inputStyle }}">
                </div>
            </div>
        </div>
        @endif

        {{-- Morada e detalhes --}}
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);" class="rounded-xl p-5 flex flex-col gap-4">
            <div style="color:#7A7775; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Detalhes do Endereço</div>

            <div>
                <label style="{{ $labelStyle }}">Morada</label>
                <input wire:model="morada" type="text" placeholder="Rua, número..."
                       style="{{ $inputStyle }}">
            </div>

            <div>
                <label style="{{ $labelStyle }}">Notas</label>
                <textarea wire:model="notas" rows="2" placeholder="Observações opcionais..."
                          style="{{ $inputStyle }}resize:none;"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="activo" type="checkbox" class="accent-[#09143B] w-4 h-4">
                <span class="text-sm font-semibold" style="color:#4A4845;">Activo</span>
            </label>
        </div>

        {{-- Botões --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('locais-frequentes.index') }}" wire:navigate class="btn-cme-secondary">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                    style="background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 24px; border-radius:8px; cursor:pointer; border:none;">
                <span wire:loading.remove>{{ $isEdit ? 'Guardar Alterações' : 'Criar Local' }}</span>
                <span wire:loading>A guardar...</span>
            </button>
        </div>

    </form>
</div>
