<div class="p-4 lg:p-6 flex flex-col gap-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600">Registo de Condução</h1>
            <p style="color:white;font-size:0.88rem;font-weight:600;margin-top:2px;">
                Histórico de sessões de condução por veículo e colaborador
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($totalAbertos > 0)
            <div class="flex items-center gap-2 bg-amber-500/15 border border-amber-500/30 rounded-xl px-4 py-2.5 cursor-pointer"
                 wire:click="$set('filtroAberto', filtroAberto === 'sim' ? '' : 'sim')">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span style="color:#fde047;font-size:0.82rem;font-weight:700;">{{ $totalAbertos }} sessão{{ $totalAbertos !== 1 ? 'ões' : '' }} em aberto</span>
            </div>
            @endif
            <button wire:click="exportarExcel" wire:loading.attr="disabled"
                    style="display:flex;align-items:center;gap:6px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.3);color:#86efac;font-size:0.78rem;font-weight:700;padding:8px 16px;border-radius:10px;cursor:pointer;white-space:nowrap;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span wire:loading.remove wire:target="exportarExcel">Exportar Excel</span>
                <span wire:loading wire:target="exportarExcel">A exportar...</span>
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:16px 20px;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label style="color:rgba(255,255,255,0.5);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:5px;">Colaborador</label>
                <input wire:model.live.debounce.300ms="searchColaborador" type="text" placeholder="Nome ou nº..."
                       style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:9px;padding:8px 12px;color:white;font-size:0.85rem;outline:none;">
            </div>
            <div>
                <label style="color:rgba(255,255,255,0.5);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:5px;">Viatura</label>
                <input wire:model.live.debounce.300ms="searchVeiculo" type="text" placeholder="Matrícula..."
                       style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:9px;padding:8px 12px;color:white;font-size:0.85rem;outline:none;">
            </div>
            <div>
                <label style="color:rgba(255,255,255,0.5);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:5px;">De</label>
                <input wire:model.live="dataInicio" type="date"
                       style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:9px;padding:8px 12px;color:white;font-size:0.85rem;outline:none;color-scheme:dark;">
            </div>
            <div>
                <label style="color:rgba(255,255,255,0.5);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:5px;">Até</label>
                <input wire:model.live="dataFim" type="date"
                       style="width:100%;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:9px;padding:8px 12px;color:white;font-size:0.85rem;outline:none;color-scheme:dark;">
            </div>
        </div>
        @if($filtroAberto === 'sim')
        <div style="margin-top:10px;">
            <button wire:click="$set('filtroAberto','')"
                    style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.35);color:#fcd34d;font-size:0.75rem;font-weight:700;padding:4px 12px;border-radius:6px;cursor:pointer;">
                ✕ A mostrar só sessões em aberto
            </button>
        </div>
        @endif
    </div>

    {{-- Tabela --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.08);">
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Viatura</th>
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Condutor</th>
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Início</th>
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Fim</th>
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Duração</th>
                        <th style="padding:10px 16px;text-align:left;color:rgba(255,255,255,0.45);font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Info</th>
                        <th style="padding:10px 16px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $aberto = is_null($log->ended_at);
                        $duracao = $aberto
                            ? $log->started_at->diffForHumans(now(), true)
                            : $log->started_at->diff($log->ended_at)->format('%hh %im');
                    @endphp
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);{{ $aberto ? 'background:rgba(245,158,11,0.05);' : '' }}">
                        <td style="padding:12px 16px;">
                            <span style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#c4b5fd;font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:6px;">
                                {{ $log->veiculo?->matricula ?? '—' }}
                            </span>
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="color:white;font-size:0.85rem;font-weight:600;">{{ $log->colaborador?->nombre ?? '—' }}</div>
                            @if($log->colaborador?->numero_colaborador)
                            <div style="color:rgba(255,255,255,0.4);font-size:0.7rem;">Nº {{ $log->colaborador->numero_colaborador }}</div>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:rgba(255,255,255,0.8);font-size:0.82rem;">
                            {{ $log->started_at->format('d/m/Y') }}<br>
                            <span style="color:#6ee7b7;font-weight:700;">{{ $log->started_at->format('H:i') }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            @if($aberto)
                                <span style="color:#fcd34d;font-size:0.75rem;font-weight:700;">● Em curso</span>
                            @else
                                <span style="color:rgba(255,255,255,0.6);font-size:0.82rem;">{{ $log->ended_at->format('d/m H:i') }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:rgba(255,255,255,0.55);font-size:0.8rem;">
                            {{ $duracao }}
                        </td>
                        <td style="padding:12px 16px;">
                            @if($log->takeover_from_name)
                                <span style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#fca5a5;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:6px;"
                                      title="Assumiu de {{ $log->takeover_from_name }} às {{ $log->takeover_at?->format('H:i') }}">
                                    ⚡ Takeover
                                </span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            @if($aberto)
                                <button wire:click="fecharSessao({{ $log->id }})"
                                        style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#fca5a5;font-size:0.7rem;font-weight:700;padding:4px 10px;border-radius:6px;cursor:pointer;">
                                    Fechar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:40px;text-align:center;color:rgba(255,255,255,0.25);font-size:0.9rem;">
                            Sem registos para os filtros selecionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.06);">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
