<div class="flex flex-col gap-6 w-full max-w-2xl mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">Nova Recepção</h1>
            <p class="text-sm text-white/70 mt-0.5">Registar entrada de stock de EPI</p>
        </div>
        <a href="{{ route('epis.rececoes.index') }}" wire:navigate
            class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-white font-semibold text-lg">Dados da Recepção</span>
        </div>

        <form wire:submit.prevent="save" class="p-6 flex flex-col gap-5">

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">EPI <span
                        class="text-red-500">*</span></label>
                <select wire:model.live="epi_item_id" class="w-full rounded-lg text-sm"
                    style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;">
                    <option value="">— Selecionar EPI —</option>
                    @foreach ($epiItems as $epi)
                        <option value="{{ $epi->id }}">
                            {{ $epi->nombre }}{{ $epi->codigo ? ' (' . $epi->codigo . ')' : '' }}</option>
                    @endforeach
                </select>
                @error('epi_item_id')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Quantidade <span
                            class="text-red-500">*</span></label>
                    <input type="number" wire:model="quantidade" min="1" class="w-full rounded-lg text-sm"
                        style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;">
                    @error('quantidade')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                    {{-- Talla (Display only in Flat SKU) --}}
                    @if ($talla)
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Tamanho</label>
                            <div class="w-full rounded-lg text-sm bg-gray-100 border border-gray-300 px-3 py-2 text-gray-600">
                                {{ $talla }}
                            </div>
                        </div>
                    @endif
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Data <span
                                class="text-red-500">*</span></label>
                        <input type="date" wire:model="data" class="w-full rounded-lg text-sm"
                            style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;">
                        @error('data')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label
                            class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Fornecedor</label>
                        <input type="text" wire:model="fornecedor" placeholder="Nome do fornecedor"
                            class="w-full rounded-lg text-sm"
                            style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Nº
                            Fatura</label>
                        <input type="text" wire:model="numero_fatura" placeholder="Referência"
                            class="w-full rounded-lg text-sm"
                            style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;">
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-(--cme-blue) uppercase tracking-wider">Observações</label>
                    <textarea wire:model="observacoes" rows="2" placeholder="Notas adicionais..."
                        class="w-full rounded-lg text-sm resize-none"
                        style="background:white;color:#111827;border:1px solid #d1d5db;padding:0.625rem 0.75rem;outline:none;"></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 flex justify-end gap-3">
                    <a href="{{ route('epis.rececoes.index') }}" wire:navigate
                        class="inline-flex items-center gap-2 py-2.5 px-5 rounded-lg font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancelar</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md"
                        style="background:#0f2a5e;">
                        <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                        </svg>
                        <span wire:loading.remove wire:target="save">Registar Recepção</span>
                        <span wire:loading wire:target="save">A guardar...</span>
                    </button>
                </div>
        </form>
    </div>
</div>
