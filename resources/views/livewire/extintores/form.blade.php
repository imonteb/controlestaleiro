<div>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <nav class="flex text-gray-500 text-xs uppercase font-bold tracking-wider mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li><a href="{{ route('extintores.index') }}" wire:navigate class="hover:text-yellow-600">Extintores</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="text-(--cme-blue) font-bold">{{ $extintor ? 'Editar' : 'Registar' }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">{{ $extintor ? 'Editar' : 'Novo' }} Extintor</h1>
            </div>
        </div>

        <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nº Série --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Número de Série <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="num_serie" placeholder="Ex: ABC-12345"
                        class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 transition-all font-bold text-gray-900">
                    @error('num_serie') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Tipo Agente --}}
                <div>
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Tipo de Agente</label>
                    <select wire:model="tipo_agente" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 font-bold text-gray-900">
                        <option value="CO2">CO2 (Dióxido de Carbono)</option>
                        <option value="Pó Químico">Pó Químico (ABC)</option>
                        <option value="Água">Água / Espuma</option>
                        <option value="Halon">Halon / Substitutos</option>
                    </select>
                </div>

                {{-- Tamanho --}}
                <div>
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Tamanho (Peso)</label>
                    <select wire:model="tamanho" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 font-bold text-gray-900">
                        <option value="2kg">2 kg</option>
                        <option value="5kg">5 kg</option>
                        <option value="6kg">6 kg</option>
                        <option value="9kg">9 kg</option>
                        <option value="12kg">12 kg</option>
                        <option value="50kg">50 kg (Carro)</option>
                    </select>
                </div>

                {{-- Data Verificação --}}
                <div>
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Data de Verificação</label>
                    <input type="date" wire:model="data_verificacao" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 text-gray-900 font-bold">
                </div>

                {{-- Próxima Revisão --}}
                <div>
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Próxima Revisão</label>
                    <input type="date" wire:model="proxima_revisao" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 text-gray-900 font-bold">
                </div>

                {{-- Viatura --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500">Alocação (Viatura)</label>
                    <select wire:model="veiculo_id" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500 font-bold text-gray-900">
                        <option value="">Permanecer em Armazém</option>
                        @foreach($veiculos as $v)
                            <option value="{{ $v->id }}">{{ $v->matricula }} - {{ $v->model }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado --}}
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-white/50 mb-1 uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded border-l-2 border-yellow-500 text-center">Estado de Conservação</label>
                    <div class="flex gap-4 p-2 bg-gray-50 rounded-xl">
                        <label class="flex-1 flex flex-col items-center p-3 rounded-lg cursor-pointer transition-all @if($estado === 'Conforme') bg-white shadow-sm ring-2 ring-green-500 @endif">
                            <input type="radio" wire:model.live="estado" value="Conforme" class="hidden">
                            <span class="text-[10px] font-bold uppercase @if($estado === 'Conforme') text-green-600 @else text-white/60 @endif">Conforme</span>
                        </label>
                        <label class="flex-1 flex flex-col items-center p-3 rounded-lg cursor-pointer transition-all @if($estado === 'Não Conforme') bg-white shadow-sm ring-2 ring-red-500 @endif">
                            <input type="radio" wire:model.live="estado" value="Não Conforme" class="hidden">
                            <span class="text-[10px] font-bold uppercase @if($estado === 'Não Conforme') text-red-600 @else text-white/60 @endif">Crítico</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex justify-end gap-3">
                <a href="{{ route('extintores.index') }}" wire:navigate class="px-6 py-2.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 uppercase text-[11px]">Cancelar</a>
                <button type="submit" class="px-10 py-2.5 rounded-xl bg-yellow-500 text-(--cme-blue) font-black hover:bg-yellow-400 shadow-md uppercase text-[11px] transition-all">
                    {{ $extintor ? 'Guardar Alterações' : 'Registar Extintor' }}
                </button>
            </div>
        </form>
    </div>
</div>
