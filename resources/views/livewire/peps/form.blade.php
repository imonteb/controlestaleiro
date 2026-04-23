<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">
                {{ $isEdit ? 'Editar PEP' : 'Novo PEP' }}
            </h1>
            <p class="text-sm text-white/70 mt-0.5">{{ $isEdit ? 'Modifica os dados do PEP' : 'Regista um novo Posto de Execução de Projeto' }}</p>
        </div>
        <a href="{{ route('peps.index') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar à lista
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">

        {{-- Card Header --}}
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados do PEP</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            {{-- Nome --}}
            <div class="flex flex-col gap-1.5">
                <label for="nome" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Nome do PEP <span class="text-red-500">*</span></label>
                <input type="text" id="nome" wire:model="nome" placeholder="Ex: PEP-001 Eletricidade"
                       class="w-full rounded-lg text-sm"
                       style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
                @error('nome') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Localização --}}
                <div class="flex flex-col gap-1.5">
                    <label for="localizacao_id" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Localização</label>
                    <select id="localizacao_id" wire:model="localizacao_id"
                            class="w-full rounded-lg text-sm"
                            style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
                        <option value="">— Sem localização —</option>
                        @foreach($localizacoes as $locacion)
                            <option value="{{ $locacion->id }}">{{ $locacion->nombre }}</option>
                        @endforeach
                    </select>
                    @error('localizacao_id') <span class="text-xs text-red-500 flex items-center gap-1"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-11a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                </div>

                {{-- Tipo de Trabajo --}}
                <div class="flex flex-col gap-1.5">
                    <label for="tipo_trabalho_id" class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Tipo de Trabalho</label>
                    <select id="tipo_trabalho_id" wire:model="tipo_trabalho_id"
                            class="w-full rounded-lg text-sm"
                            style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;" onfocus="this.style.borderColor='#0f2a5e'" onblur="this.style.borderColor='#d1d5db'">
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
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span class="px-3 py-1 rounded-full text-white text-xs font-bold" style="background-color: {{ $selectedTipo->color ?? '#0f2a5e' }};">
                    {{ $selectedTipo->nombre }}
                </span>
                <span class="text-gray-400">← etiqueta que aparecerá no dashboard</span>
            </div>
            @endif

            {{-- Divider --}}
            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                <a href="{{ route('peps.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md"
                    style="background:#0f2a5e;">
                    <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    <span wire:loading.remove wire:target="save">Guardar PEP</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </form>
    </div>

</div>
