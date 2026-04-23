<div class="w-full max-w-2xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('locais-frequentes.index') }}" wire:navigate class="text-blue-200 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold uppercase tracking-wide text-yellow-600">
            {{ $isEdit ? 'Editar Local' : 'Novo Local Frequente' }}
        </h1>
    </div>

    @php
        $inputStyle = "background:white;color:#111827;border:1px solid #d1d5db;padding:0.5rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;";
        $selectStyle = "background:white;color:#111827;border:1px solid #d1d5db;padding:0.5rem 0.75rem;border-radius:0.5rem;width:100%;outline:none;font-size:0.875rem;";
        $labelStyle = "display:block;font-size:0.7rem;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:0.25rem;letter-spacing:0.05em;";
    @endphp

    <form wire:submit.prevent="save" class="flex flex-col gap-5">

        {{-- Nome e Tipo --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 flex flex-col gap-4">
            <div>
                <label style="{{ $labelStyle }}">Nome do Local *</label>
                <input wire:model="nome" type="text" placeholder="Ex: Estaleiro CME, Obra Câmara de Lobos..."
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
                @error('nome') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="{{ $labelStyle }}">Tipo</label>
                <div class="flex gap-4 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="tipo" type="radio" value="portugal" class="accent-yellow-500">
                        <span class="text-sm font-semibold text-gray-700">Portugal</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model.live="tipo" type="radio" value="internacional" class="accent-yellow-500">
                        <span class="text-sm font-semibold text-gray-700">Internacional</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Buscador Portugal --}}
        @if($tipo === 'portugal')
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 flex flex-col gap-4">
            <div class="text-xs font-bold text-blue-600 uppercase tracking-wide">Localização Portugal</div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Distrito --}}
                <div>
                    <label style="{{ $labelStyle }}">Distrito</label>
                    <select wire:model.live="selectedDd" style="{{ $selectStyle }}"
                            onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
                        <option value="">— Seleccionar —</option>
                        @foreach($distritos as $d)
                            <option value="{{ $d->dd }}">{{ $d->desig }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Concelho (opcional) --}}
                <div>
                    <label style="{{ $labelStyle }}">Concelho <span class="normal-case font-normal text-gray-400">(opcional)</span></label>
                    <select wire:model.live="selectedCc" style="{{ $selectStyle }}"
                            {{ ! $selectedDd ? 'disabled' : '' }}
                            onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
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
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';"
                           autocomplete="off">

                    <div x-show="open" x-transition
                         style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);z-index:50;overflow:hidden;">
                        <template x-for="r in results" :key="r.id">
                            <button type="button"
                                    @click="$wire.call('selecionarZona', r.id, r.localidade, r.cp4, r.cp3, r.cpalf, r.art_local ?? ''); query = r.label; open = false;"
                                    style="display:flex;justify-content:space-between;align-items:center;width:100%;padding:0.625rem 0.875rem;text-align:left;background:white;border:none;border-bottom:1px solid #f3f4f6;cursor:pointer;gap:1rem;"
                                    onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='white';">
                                <span style="font-size:0.8rem;color:#111827;font-weight:600;" x-text="r.label"></span>
                                <span style="font-size:0.7rem;color:#9ca3af;font-family:monospace;white-space:nowrap;" x-text="r.cp"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            @else
            <p class="text-xs text-gray-400 italic">Selecciona primeiro o distrito para pesquisar.</p>
            @endif

            {{-- CP selecionado --}}
            @if($cp4 && $cp3)
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-emerald-600 font-mono font-bold text-lg">{{ $cp4 }}-{{ $cp3 }}</div>
                    <div>
                        <div class="text-xs font-bold text-emerald-700">{{ $cpalf }}</div>
                        <div class="text-xs text-emerald-600">{{ $localidade }}</div>
                    </div>
                </div>
                <button type="button" wire:click="limparZona"
                        style="background:none;border:none;color:#6b7280;font-size:1rem;cursor:pointer;padding:0.25rem;">✕</button>
            </div>

            {{-- Pesquisa de rua (opcional) --}}
            <div x-data="{ queryRua: '', resultsRua: [], openRua: false }"
                 @click.outside="openRua = false">
                <label style="{{ $labelStyle }}">Pesquisar rua <span class="normal-case font-normal text-gray-400">(opcional)</span></label>
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
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';"
                           autocomplete="off">

                    <div x-show="openRua" x-transition
                         style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1px solid #e5e7eb;border-radius:0.75rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);z-index:50;overflow:hidden;">
                        <template x-for="r in resultsRua" :key="r.id">
                            <button type="button"
                                    @click="$wire.call('selecionarRua', r.id, r.nome, r.localidade, r.cp4, r.cp3, r.cpalf); queryRua = r.nome; openRua = false;"
                                    style="display:flex;justify-content:space-between;align-items:center;width:100%;padding:0.625rem 0.875rem;text-align:left;background:white;border:none;border-bottom:1px solid #f3f4f6;cursor:pointer;gap:1rem;"
                                    onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='white';">
                                <span style="font-size:0.8rem;color:#111827;font-weight:600;" x-text="r.nome"></span>
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
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 flex flex-col gap-4">
            <div class="text-xs font-bold text-purple-600 uppercase tracking-wide">Localização Internacional</div>

            <div>
                <label style="{{ $labelStyle }}">País *</label>
                <input wire:model="pais" type="text" placeholder="Ex: França, Espanha..."
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label style="{{ $labelStyle }}">Cidade / Localidade</label>
                    <input wire:model="localidade" type="text" placeholder="Ex: Paris, Lyon..."
                           style="{{ $inputStyle }}"
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
                </div>
                <div>
                    <label style="{{ $labelStyle }}">Código Postal</label>
                    <input wire:model="cp4" type="text" placeholder="Ex: 75001"
                           style="{{ $inputStyle }}"
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
                </div>
            </div>
        </div>
        @endif

        {{-- Morada e detalhes --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 flex flex-col gap-4">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wide">Detalhes do Endereço</div>

            <div>
                <label style="{{ $labelStyle }}">Morada</label>
                <input wire:model="morada" type="text" placeholder="Rua, número..."
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';">
            </div>

            <div>
                <label style="{{ $labelStyle }}">Notas</label>
                <textarea wire:model="notas" rows="2" placeholder="Observações opcionais..."
                          style="{{ $inputStyle }}resize:none;"
                          onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#d1d5db';"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input wire:model="activo" type="checkbox" class="accent-yellow-500 w-4 h-4">
                <span class="text-sm font-semibold text-gray-700">Activo</span>
            </label>
        </div>

        {{-- Botones --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('locais-frequentes.index') }}" wire:navigate
               class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-lg font-bold text-sm shadow"
                    style="background-color:var(--cme-yellow);color:var(--cme-blue);">
                <span wire:loading.remove>{{ $isEdit ? 'Guardar Alterações' : 'Criar Local' }}</span>
                <span wire:loading>A guardar...</span>
            </button>
        </div>

    </form>
</div>
