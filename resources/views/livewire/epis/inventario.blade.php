<div class="flex flex-col gap-6 w-full">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600 tracking-wide">Inventário EPI</h1>
            <p class="text-sm text-white/70 mt-0.5">Conferir stock real vs. sistema e registar ajustes</p>
        </div>
        <button wire:click="$toggle('mostrarHistorico')" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $mostrarHistorico ? 'Ocultar histórico' : 'Ver histórico' }}
        </button>
    </div>

    @if(session('message'))
    <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800 font-medium">{{ session('message') }}</div>
    @endif
    @if(session('info'))
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800 font-medium">{{ session('info') }}</div>
    @endif

    {{-- Control Bar --}}
    <div class="bg-blue-50 rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 flex-nowrap">
            <label class="inline-flex items-center cursor-pointer group shrink-0">
                <input type="checkbox" wire:model.live="soloConStock" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:inset-s-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ms-2 sm:ms-3 text-[11px] sm:text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors whitespace-nowrap">Apenas com stock</span>
            </label>

            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

            <span class="text-[10px] sm:text-xs font-medium text-gray-400 whitespace-nowrap">
                Mostrando {{ count($linhas) }} linha(s)
            </span>
        </div>

        <div class="relative w-48">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Procurar..." 
                   class="block w-full p-5 pl-10 pr-10 py-1.5 border border-gray-200 rounded-xl text-sm 
                   font-medium text-gray-900 bg-zinc-100 shadow-sm transition-all focus:ring-2 focus:ring-blue-100 
                   focus:border-blue-400 outline-none placeholder:text-gray-400">
            @if(!empty($search))
            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            @endif
        </div>
    </div>

    {{-- Inventário Table --}}
    <div class="bg-blue-50 rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-(--cme-blue) px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-(--cme-blue)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 00(2 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Conferência de Stock</span>
        </div>

        @if(count($linhas) === 0)
            <div class="p-12 text-center text-gray-400">Nenhum resultado encontrado. Tente ajustar os filtros.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">EPI</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Código</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tamanho</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[80px]">Stock Sistema</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[80px]">Stock Real</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[80px]">Diferença</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($linhas as $i => $linha)
                    <tr wire:key="inv-row-{{ $linha['epi_item_id'] }}-{{ $linha['tamanho'] }}" class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors {{ $linha['diferenca'] != 0 ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-2.5 font-semibold text-gray-900 whitespace-nowrap">{{ $linha['nome'] }}</td>
                        <td class="px-4 py-2.5 text-gray-600 whitespace-nowrap font-mono">{{ $linha['codigo'] ?: '—' }}</td>
                        <td class="px-4 py-2.5 text-center whitespace-nowrap">@if($linha['tamanho'])<span class="inline-flex px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-semibold">{{ $linha['tamanho'] }}</span>@else<span class="text-gray-400">—</span>@endif</td>
                        <td class="px-3 py-2 text-center font-mono font-semibold text-gray-700 whitespace-nowrap">{{ $linha['stock_sistema'] }}</td><td class="px-3 py-2 text-center whitespace-nowrap"><input type="number" wire:model.live.debounce.500ms="linhas.{{ $i }}.stock_real" class="w-14 text-center rounded border font-mono text-sm font-semibold text-gray-900 bg-white {{ $linha['diferenca'] != 0 ? 'border-yellow-400 bg-yellow-50' : 'border-gray-300' }}" style="padding:0.2rem;outline:none;display:inline-block;"></td><td class="px-3 py-2 text-center font-mono font-bold whitespace-nowrap {{ $linha['diferenca'] > 0 ? 'text-green-600' : ($linha['diferenca'] < 0 ? 'text-red-600' : 'text-gray-400') }}">{{ $linha['diferenca'] > 0 ? '+' : '' }}{{ $linha['diferenca'] }}</td>
                        <td class="px-4 py-2.5">
                            @if($linha['diferenca'] != 0)
                            <input type="text"
                                   wire:model="linhas.{{ $i }}.motivo"
                                   placeholder="Motivo do ajuste..."
                                   class="w-full rounded border text-sm text-gray-900 bg-white {{ $errors->has('linhas.'.$i.'.motivo') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                                   style="padding:0.375rem 0.5rem;outline:none;">
                                @error("linhas.{$i}.motivo") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
            <span class="text-xs text-gray-500">
                {{ collect($linhas)->filter(fn($l) => $l['diferenca'] != 0)->count() }} item(s) com diferença
            </span>
            <button wire:click="guardar" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-md disabled:opacity-50" style="background:#0f2a5e;">
                <svg wire:loading.remove wire:target="guardar" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <svg wire:loading wire:target="guardar" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                <span wire:loading.remove wire:target="guardar">Guardar ajustes</span>
                <span wire:loading wire:target="guardar">A guardar...</span>
            </button>
        </div>
        @endif
    </div>

    {{-- Histórico de Ajustes --}}
    @if($mostrarHistorico)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gray-800 px-6 py-4 flex items-center gap-3">
            <div class="bg-yellow-500 p-2 rounded-lg">
                <svg class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-white font-semibold text-lg">Histórico de Ajustes</span>
        </div>

        @if($historial->isEmpty())
            <div class="p-8 text-center text-gray-400">Nenhum ajuste registado.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">EPI</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tam.</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Qtd</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Detalhes</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Utilizador</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $h)
                    <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-gray-600">
                            {{ is_string($h['fecha']) ? \Carbon\Carbon::parse($h['fecha'])->format('d/m/Y') : $h['fecha']->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2.5">
                            @if($h['tipo'] === 'rececao')
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase">Entrada</span>
                            @elseif($h['tipo'] === 'entrega')
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase">Saída</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase">Ajuste</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 font-semibold text-gray-900">{{ $h['item']->nombre ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center">{{ $h['talla'] ?: '—' }}</td>
                        <td class="px-4 py-2.5 text-center font-mono font-bold {{ $h['qtd'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $h['qtd'] > 0 ? '+' : '' }}{{ $h['qtd'] }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-700 text-xs">{{ $h['detalhe'] }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $h['user'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif
</div>
