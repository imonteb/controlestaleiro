<div class="p-6 space-y-6" x-cloak>

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Gestão de Sessões</h1>
            <p class="text-sm text-blue-200 mt-0.5">Monitorização e controlo de acessos ao sistema</p>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" wire:model.live="onlyActive" class="rounded border-blue-600 bg-blue-900/60 text-yellow-500 focus:ring-yellow-500">
                <span class="text-sm text-blue-100 group-hover:text-white transition">Apenas Ativas</span>
            </label>
        </div>
    </div>

    {{-- Mensagens de Feedback --}}
    @if(session()->has('success'))
        <div class="bg-green-600/20 border border-green-500 text-green-200 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pesquisa --}}
    <div class="max-w-sm">
        <input wire:model.live="search" type="text" placeholder="Pesquisar por utilizador, IP ou cidade…"
               class="w-full rounded-lg bg-blue-900/50 border border-blue-700 text-white placeholder-blue-400 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto rounded-xl border border-blue-700/60 shadow-lg">
        <table class="w-full text-sm text-left text-blue-100">
            <thead class="bg-blue-900/70 text-blue-300 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-3">Utilizador</th>
                    <th class="px-5 py-3">IP & Localização</th>
                    <th class="px-5 py-3">Navegador / Dispositivo</th>
                    <th class="px-5 py-3">Início / Última Atividade</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-blue-800/50">
                @forelse($sessions as $session)
                    @php
                        $isActive = is_null($session->logout_at) && $session->last_activity_at->gt(now()->subMinutes(config('session.lifetime')));
                    @endphp
                    <tr class="bg-blue-950/30 hover:bg-blue-900/30 transition">
                        <td class="px-5 py-3">
                            <div class="font-medium text-white">{{ $session->user->name }}</div>
                            <div class="text-xs text-blue-400">{{ $session->user->email }}</div>
                            @if($session->user_id === auth()->id())
                                <span class="mt-1 inline-block text-[10px] bg-yellow-500/20 text-yellow-400 border border-yellow-500/40 rounded px-1.5 py-0.5 uppercase font-bold">Esta Sessão</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-white font-mono text-xs">{{ $session->ip_address }}</span>
                                @if($isActive)
                                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]" title="Online"></span>
                                @endif
                            </div>
                            <div class="text-xs text-blue-300 mt-1 flex items-center gap-1">
                                📍 {{ $session->city ?: 'Desconhecido' }}, {{ $session->country ?: '---' }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-blue-200 text-xs truncate max-w-[200px]" title="{{ $session->user_agent }}">
                                {{ Str::limit($session->user_agent, 40) }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-xs">
                                <span class="text-blue-400 uppercase font-bold text-[9px]">Início:</span> 
                                <span class="text-white">{{ $session->login_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-blue-400 uppercase font-bold text-[9px]">Ativo:</span> 
                                <span class="text-white">{{ $session->last_activity_at->diffForHumans() }}</span>
                            </div>
                            @if($session->logout_at)
                                <div class="text-xs mt-1">
                                    <span class="text-red-400 uppercase font-bold text-[9px]">Fim:</span> 
                                    <span class="text-red-300">{{ $session->logout_at->format('H:i') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($isActive && $session->user_id !== auth()->id())
                                <button wire:click="terminateSession('{{ $session->session_id }}')"
                                        wire:confirm="Tem a certeza que deseja terminar esta sessão? O utilizador será desconectado imediatamente."
                                        class="inline-flex items-center gap-1 text-xs bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded-lg transition shadow-md">
                                    <svg class="w-3 H-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Terminar
                                </button>
                            @elseif($session->user_id === auth()->id() && $isActive)
                                <span class="text-[10px] text-yellow-500 font-bold uppercase italic opacity-60">Sessão Atual</span>
                            @else
                                <span class="text-[10px] text-gray-500 font-bold uppercase italic">Encerrada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-blue-400 italic">Nenhuma sessão encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
        <div class="mt-4 shadow-lg p-2 rounded-lg bg-blue-900/20">
            {{ $sessions->links() }}
        </div>
    @endif
</div>
