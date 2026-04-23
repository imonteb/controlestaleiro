<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Column --}}
        <div class="lg:col-span-1">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 px-2">{{ $edit_id ? 'Editar Artigo' : 'Adicionar Artigo Base' }}</h2>
            <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nome do Consumível</label>
                    <input type="text" wire:model="nombre" placeholder="Ex: Álcool Etílico 70%" 
                        class="w-full rounded-xl border-gray-200 focus:border-green-500 focus:ring-green-500 font-bold">
                    @error('nombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Unidade</label>
                    <select wire:model="unidade" class="w-full rounded-xl border-gray-200 focus:border-green-500 focus:ring-green-500 font-bold">
                        <option value="un">Unidade (un)</option>
                        <option value="ml">Mililitros (ml)</option>
                        <option value="pack">Pack / Caixa</option>
                        <option value="rolo">Rolo / Ligadura</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg active:scale-95 transition-all text-sm uppercase tracking-widest">
                    {{ $edit_id ? 'Atualizar Artigo' : 'Criar Artigo' }}
                </button>
                @if($edit_id)
                    <button type="button" wire:click="$set('edit_id', null)" class="w-full text-xs text-gray-400 font-bold uppercase hover:underline">Cancelar Edição</button>
                @endif
            </form>
        </div>

        {{-- List Column --}}
        <div class="lg:col-span-2">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 px-2">Catálogo de Consumíveis Médicos</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-sm">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase">
                            <th class="px-6 py-4">Nome</th>
                            <th class="px-6 py-4">Unidade</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-600">
                        @forelse($itens as $item)
                        <tr class="hover:bg-green-50/10 transition-colors">
                            <td class="px-6 py-3 font-bold text-gray-900">{{ $item->nombre }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-[10px] font-bold text-gray-500 uppercase">{{ $item->unidade }}</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $item->id }})" class="p-1 text-gray-400 hover:text-green-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5-5l5 5m0 0l-5 5m5-5H12" /></svg>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Eliminar este artigo?" class="p-1 text-gray-400 hover:text-red-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-300">Nenhum artigo registado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
