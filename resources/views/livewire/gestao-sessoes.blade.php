<div class="p-6 space-y-6" x-cloak>

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Gestão de Sessões</span>
                <span style="color:rgba(255,255,255,0.5); font-size:11px;">— Monitorização de acessos</span>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model.live="onlyActive" class="accent-[#09143B] rounded">
                <span style="color:rgba(255,255,255,0.8); font-size:11px; font-weight:600;">Apenas Ativas</span>
            </label>
        </div>
    </div>

    {{-- Mensagens de Feedback --}}
    @if(session()->has('success'))
        <div class="badge-ok px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pesquisa --}}
    <div class="mb-4">
        <input wire:model.live="search" type="text" placeholder="Pesquisar por utilizador, IP ou cidade…"
               class="cme-input w-full max-w-sm">
    </div>

    {{-- Tabela --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                    <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Utilizador</th>
                    <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">IP &amp; Localização</th>
                    <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Navegador / Dispositivo</th>
                    <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Início / Última Atividade</th>
                    <th class="px-5 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                @forelse($sessions as $session)
                    @php
                        $isActive = is_null($session->logout_at) && $session->last_activity_at->gt(now()->subMinutes(config('session.lifetime')));
                    @endphp
                    <tr class="transition-colors hover:bg-[#E4E2DF]" style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-5 py-3">
                            <div style="color:#1A1A1A; font-weight:600;">{{ $session->user->name }}</div>
                            <div class="text-xs" style="color:#7A7775;">{{ $session->user->email }}</div>
                            @if($session->user_id === auth()->id())
                                <span class="badge-warn mt-1 inline-block">Esta Sessão</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span style="color:#1A1A1A; font-family:monospace; font-size:12px;">{{ $session->ip_address }}</span>
                                @if($isActive)
                                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]" title="Online"></span>
                                @endif
                            </div>
                            <div class="text-xs mt-1" style="color:#7A7775;">
                                📍 {{ $session->city ?: 'Desconhecido' }}, {{ $session->country ?: '---' }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-xs truncate max-w-[200px]" style="color:#4A4845;" title="{{ $session->user_agent }}">
                                {{ Str::limit($session->user_agent, 40) }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-xs">
                                <span style="color:#7A7775; font-size:9px; font-weight:700; text-transform:uppercase;">Início:</span>
                                <span style="color:#1A1A1A;">{{ $session->login_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-xs mt-1">
                                <span style="color:#7A7775; font-size:9px; font-weight:700; text-transform:uppercase;">Ativo:</span>
                                <span style="color:#1A1A1A;">{{ $session->last_activity_at->diffForHumans() }}</span>
                            </div>
                            @if($session->logout_at)
                                <div class="text-xs mt-1">
                                    <span style="color:#A32D2D; font-size:9px; font-weight:700; text-transform:uppercase;">Fim:</span>
                                    <span style="color:#A32D2D;">{{ $session->logout_at->format('H:i') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($isActive && $session->user_id !== auth()->id())
                                <button wire:click="terminateSession('{{ $session->session_id }}')"
                                        wire:confirm="Tem a certeza que deseja terminar esta sessão? O utilizador será desconectado imediatamente."
                                        style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Terminar
                                </button>
                            @elseif($session->user_id === auth()->id() && $isActive)
                                <span style="color:#854F0B; font-size:10px; font-weight:700; text-transform:uppercase;">Sessão Atual</span>
                            @else
                                <span class="cme-muted text-[10px] font-bold uppercase">Encerrada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center cme-muted italic">Nenhuma sessão encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    @endif
</div>
