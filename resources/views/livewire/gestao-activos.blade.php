<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Gestão de Activos</h1>
            <p class="text-gray-400 text-sm">Controle de validade e reposição de Extintores e Kits de Saúde.</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <flux:select wire:model.live="type" class="w-40">
                <flux:select.option value="all">Todos os tipos</flux:select.option>
                <flux:select.option value="extintor">Extintores</flux:select.option>
                <flux:select.option value="kit">Kits de Saúde</flux:select.option>
            </flux:select>

            <flux:select wire:model.live="statusFilter" class="w-40">
                <flux:select.option value="all">Todos os estados</flux:select.option>
                <flux:select.option value="ok">Conforme</flux:select.option>
                <flux:select.option value="warning">Próximo Vencimento</flux:select.option>
                <flux:select.option value="expired">Expirado</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Pesquisar por S/N, matrícula, local..." />
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <table class="w-full text-left text-sm text-gray-300">
            <thead class="bg-slate-800/50 text-gray-400 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-3">Asset</th>
                    <th class="px-4 py-3">Localização / Veículo</th>
                    <th class="px-4 py-3">Validade</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">QR / Token</th>
                    <th class="px-4 py-3">Acções</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($assets as $asset)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-4 py-4">
                        <div class="font-bold text-white">{{ $asset['name'] }}</div>
                        <div class="text-xs text-gray-500">{{ $asset['description'] }}</div>
                    </td>
                    <td class="px-4 py-4 font-medium">{{ $asset['location'] }}</td>
                    <td class="px-4 py-4">
                        @if($asset['expiry'])
                            <span class="{{ $asset['status'] === 'expired' ? 'text-red-400' : ($asset['status'] === 'warning' ? 'text-yellow-400' : 'text-gray-300') }}">
                                {{ $asset['expiry']->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-600 italic">No date</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($asset['needs_restock'])
                            <span class="bg-orange-500/20 text-orange-400 px-2 py-0.5 rounded text-[10px] font-bold border border-orange-500/30 uppercase mr-1">Reposição</span>
                        @endif
                        
                        @if($asset['status'] === 'expired')
                            <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-[10px] font-bold border border-red-500/30 uppercase">Expirado</span>
                        @elseif($asset['status'] === 'warning')
                            <span class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded text-[10px] font-bold border border-yellow-500/30 uppercase">Atenção</span>
                        @else
                            <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-[10px] font-bold border border-green-500/30 uppercase">OK</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($asset['token'])
                            <code class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700 text-blue-400">{{ $asset['token'] }}</code>
                        @else
                            <button wire:click="generateToken({{ $asset['id'] }}, '{{ $asset['type'] }}')" class="text-xs text-blue-400 hover:underline">Gerar Token</button>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <flux:button size="xs" variant="ghost" icon="pencil" title="Editar" />
                            <flux:button size="xs" variant="ghost" :icon="$asset['needs_restock'] ? 'check-circle' : 'exclamation-circle'" 
                                wire:click="toggleRestock({{ $asset['id'] }}, '{{ $asset['type'] }}')"
                                :title="$asset['needs_restock'] ? 'Marcar OK' : 'Marcar em Falta'" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Nenhum activo encontrado com estes filtros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
