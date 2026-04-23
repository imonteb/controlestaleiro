<div>
    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <nav class="flex text-gray-500 text-xs uppercase font-bold tracking-wider mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li><a href="{{ route('saude.index') }}" wire:navigate class="hover:text-yellow-600">Saúde</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="text-gray-500 font-bold uppercase tracking-wider">{{ $kit ? 'Gerir Mala' : 'Novo Kit' }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">{{ $kit ? 'Gerir Conteúdo da Mala' : 'Registar Kit de Primeiros Socorros' }}</h1>
            </div>
            <a href="{{ route('saude.index') }}" wire:navigate class="p-2 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </a>
        </div>

        <form wire:submit="save" class="space-y-6">
            {{-- Kit Basic Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-black text-(--cme-blue) mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Viatura Alocada <span class="text-red-500">*</span></label>
                        <select wire:model="veiculo_id" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 font-bold text-gray-900">
                            <option value="">Escolher Viatura...</option>
                            @foreach($veiculos as $v)
                                <option value="{{ $v->id }}">{{ $v->matricula }} - {{ $v->model }}</option>
                            @endforeach
                        </select>
                        @error('veiculo_id') <span class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-black text-(--cme-blue) mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Designação da Mala</label>
                        <input type="text" wire:model="designacao" placeholder="Ex: Mala de Primeiros Socorros"
                            class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 text-gray-900 font-bold">
                        @error('designacao') <span class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-black text-(--cme-blue) mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">ID / Ref Mala</label>
                        <input type="text" wire:model="identificador_kit" placeholder="Ex: KIT-001"
                            class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 text-gray-900 font-bold">
                    </div>
                </div>
            </div>

            {{-- Kit Items --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 flex items-center justify-between bg-green-50/10">
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Planilha de Conteúdo (Consumíveis)</h2>
                    <button type="button" wire:click="addItem" class="px-3 py-1.5 rounded-lg bg-yellow-500 text-(--cme-blue) text-[10px] font-black uppercase hover:bg-yellow-400 transition-all flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Adicionar Item
                    </button>
                </div>
                
                <div class="p-8">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="pb-4 pr-4">Consumível / Produto</th>
                                <th class="pb-4 px-4 w-32 text-center">Quantidade</th>
                                <th class="pb-4 px-4 w-48">Data Validade</th>
                                <th class="pb-4 pl-4 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($kit_itens as $index => $item)
                            <tr>
                                <td class="py-4 pr-4">
                                    <select wire:model="kit_itens.{{ $index }}.saude_item_id" class="w-full rounded-xl border-gray-100 focus:border-green-500 focus:ring-green-500 text-sm text-gray-900">
                                        <option value="">Selecionar Artigo...</option>
                                        @foreach($base_itens as $bi)
                                            <option value="{{ $bi->id }}">{{ $bi->nombre }} ({{ $bi->unidade }})</option>
                                        @endforeach
                                    </select>
                                    @error("kit_itens.{$index}.saude_item_id") <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                                </td>
                                <td class="py-4 px-4">
                                    <input type="number" wire:model="kit_itens.{{ $index }}.quantidade" class="w-full rounded-xl border-gray-100 text-center font-bold text-gray-900">
                                </td>
                                <td class="py-4 px-4">
                                    <input type="date" wire:model="kit_itens.{{ $index }}.data_validade" class="w-full rounded-xl border-gray-100 text-sm text-gray-900 @if(isset($item['data_validade']) && $item['data_validade'] < now()->format('Y-m-d')) text-red-600 border-red-200 bg-red-50 @endif">
                                </td>
                                <td class="py-4 pl-4 text-right">
                                    <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('saude.index') }}" wire:navigate class="px-8 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-100 uppercase text-xs">Cancelar</a>
                <button type="submit" class="px-12 py-3 rounded-xl bg-yellow-500 text-(--cme-blue) font-black hover:bg-yellow-400 shadow-lg active:scale-95 transition-all uppercase text-xs tracking-widest">
                    Gravar Mala de Primeiros Socorros
                </button>
            </div>
        </form>
    </div>
</div>
