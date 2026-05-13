<div class="p-4 lg:p-6 flex flex-col gap-4">

    {{-- Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4">
        <div class="bg-[#09143B] px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="truck" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Registo de Condução</span>
            </div>
            <div class="flex items-center gap-2">
                @if($totalAbertos > 0)
                <div wire:click="$set('filtroAberto', filtroAberto === 'sim' ? '' : 'sim')"
                     class="badge-warn cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-lg">
                    <span class="w-2 h-2 rounded-full bg-[#854F0B] animate-pulse"></span>
                    <span class="text-[11px] font-bold">{{ $totalAbertos }} sessão{{ $totalAbertos !== 1 ? 'ões' : '' }} em aberto</span>
                </div>
                @endif
                <button wire:click="exportarExcel" wire:loading.attr="disabled" class="btn-cme-ghost text-[11px]">
                    <span wire:loading.remove wire:target="exportarExcel">📥 Exportar Excel</span>
                    <span wire:loading wire:target="exportarExcel">A exportar...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="cme-card rounded-xl border border-[rgba(9,20,59,0.14)] p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="cme-label">Colaborador</label>
                <input wire:model.live.debounce.300ms="searchColaborador" type="text" placeholder="Nome ou nº..." class="cme-input">
            </div>
            <div>
                <label class="cme-label">Viatura</label>
                <input wire:model.live.debounce.300ms="searchVeiculo" type="text" placeholder="Matrícula..." class="cme-input">
            </div>
            <div>
                <label class="cme-label">De</label>
                <input wire:model.live="dataInicio" type="date" class="cme-input">
            </div>
            <div>
                <label class="cme-label">Até</label>
                <input wire:model.live="dataFim" type="date" class="cme-input">
            </div>
        </div>
        @if($filtroAberto === 'sim')
        <div class="mt-3">
            <button wire:click="$set('filtroAberto','')" class="badge-warn cursor-pointer px-3 py-1 rounded-lg text-[11px] font-bold border-0">
                ✕ A mostrar só sessões em aberto
            </button>
        </div>
        @endif
    </div>

    {{-- Tabela --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Viatura</th>
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Condutor</th>
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Início</th>
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Fim</th>
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Duração</th>
                        <th style="padding:10px 16px;text-align:left;color:#4A4845;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">Info</th>
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
                        $zebraBase = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB';
                        $rowBg = $aberto ? 'rgba(255,211,0,0.06)' : $zebraBase;
                    @endphp
                    <tr style="border-bottom:1px solid rgba(9,20,59,0.06); background:{{ $rowBg }};">
                        <td style="padding:12px 16px;">
                            <span class="badge-info" style="font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:6px;">
                                {{ $log->veiculo?->matricula ?? '—' }}
                            </span>
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="color:#1A1A1A;font-size:0.85rem;font-weight:600;">{{ $log->colaborador?->nombre ?? '—' }}</div>
                            @if($log->colaborador?->numero_colaborador)
                            <div style="color:#7A7775;font-size:0.7rem;">Nº {{ $log->colaborador->numero_colaborador }}</div>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            <span style="color:#4A4845;font-size:0.82rem;">{{ $log->started_at->format('d/m/Y') }}</span><br>
                            <span style="color:#0F6E56;font-weight:700;font-size:0.82rem;">{{ $log->started_at->format('H:i') }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            @if($aberto)
                                <span style="color:#854F0B;font-size:0.75rem;font-weight:700;">
                                    <span class="inline-block w-2 h-2 rounded-full bg-[#854F0B] animate-pulse mr-1"></span>Em curso
                                </span>
                            @else
                                <span style="color:#4A4845;font-size:0.82rem;">{{ $log->ended_at->format('d/m H:i') }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#7A7775;font-size:0.8rem;">
                            {{ $duracao }}
                        </td>
                        <td style="padding:12px 16px;">
                            @if($log->takeover_from_name)
                                <span class="badge-danger" style="font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:6px;"
                                      title="Assumiu de {{ $log->takeover_from_name }} às {{ $log->takeover_at?->format('H:i') }}">
                                    ⚡ Takeover
                                </span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;text-align:right;">
                            @if($aberto)
                                <button wire:click="fecharSessao({{ $log->id }})"
                                        style="background:#fde8e8;color:#A32D2D;border:1px solid rgba(163,45,45,0.20);padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                                    Fechar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:40px;text-align:center;">
                            <span class="cme-muted" style="font-size:0.9rem;">Sem registos para os filtros selecionados.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div style="border-top:1px solid rgba(9,20,59,0.08); padding:12px 16px;">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
