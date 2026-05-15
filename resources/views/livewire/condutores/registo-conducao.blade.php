<div class="p-4 lg:p-6 flex flex-col gap-4">

    {{-- Header + Tabs --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div class="bg-[#09143B] px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="truck" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Registo de Condução</span>
            </div>
            <div class="flex items-center gap-2">
                @if($totalAbertos > 0)
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold"
                      style="background:rgba(255,211,0,0.15);color:#FFD300;">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD300] animate-pulse"></span>
                    {{ $totalAbertos }} em curso
                </span>
                @endif
                <button wire:click="exportarExcel" wire:loading.attr="disabled"
                        class="text-[11px] px-3 py-1.5 rounded-lg font-medium"
                        style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.75);border:1px solid rgba(255,255,255,0.12);">
                    <span wire:loading.remove wire:target="exportarExcel">📥 Exportar Excel</span>
                    <span wire:loading wire:target="exportarExcel">A exportar...</span>
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-4">
            <button wire:click="$set('tab','estado')"
                    class="text-[11px] px-4 py-2.5 font-semibold transition-colors
                           {{ $tab === 'estado'
                               ? 'text-[#FFD300] border-b-2 border-[#FFD300]'
                               : 'text-white/40 hover:text-white/70' }}">
                Estado Atual
            </button>
            <button wire:click="$set('tab','historico')"
                    class="text-[11px] px-4 py-2.5 font-semibold transition-colors
                           {{ $tab === 'historico'
                               ? 'text-[#FFD300] border-b-2 border-[#FFD300]'
                               : 'text-white/40 hover:text-white/70' }}">
                Histórico
            </button>
        </div>
    </div>

    {{-- ====== TAB: ESTADO ATUAL ====== --}}
    @if($tab === 'estado')
    @php
        $emConducao  = $estadoVeiculos->where('tem_condutor', true);
        $semCondutor = $estadoVeiculos->where('tem_condutor', false);
    @endphp

    {{-- Métricas --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-lg border border-[rgba(9,20,59,0.10)] px-4 py-3" style="background:#F0EEEB;">
            <div class="text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#7A7775;">Total atribuídos</div>
            <div class="text-2xl font-bold" style="color:#1A1A1A;">{{ $estadoVeiculos->count() }}</div>
            <div class="text-[10px] mt-0.5" style="color:#7A7775;">viaturas hoje</div>
        </div>
        <div class="rounded-lg border px-4 py-3" style="background:#d4ede4;border-color:rgba(15,110,86,0.20);">
            <div class="text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#0F6E56;">Em condução</div>
            <div class="text-2xl font-bold" style="color:#0F6E56;">{{ $emConducao->count() }}</div>
            <div class="text-[10px] mt-0.5" style="color:#0F6E56;">com condutor activo</div>
        </div>
        <div class="rounded-lg border px-4 py-3"
             style="background:{{ $semCondutor->count() > 0 ? '#fdf0c2' : '#F0EEEB' }};border-color:{{ $semCondutor->count() > 0 ? 'rgba(133,79,11,0.20)' : 'rgba(9,20,59,0.10)' }};">
            <div class="text-[10px] uppercase tracking-wide font-semibold mb-1"
                 style="color:{{ $semCondutor->count() > 0 ? '#854F0B' : '#7A7775' }};">Sem condutor</div>
            <div class="text-2xl font-bold"
                 style="color:{{ $semCondutor->count() > 0 ? '#854F0B' : '#1A1A1A' }};">{{ $semCondutor->count() }}</div>
            <div class="text-[10px] mt-0.5"
                 style="color:{{ $semCondutor->count() > 0 ? '#854F0B' : '#7A7775' }};">sem registo activo</div>
        </div>
    </div>

    @if($estadoVeiculos->isEmpty())
    <div class="rounded-xl border border-[rgba(9,20,59,0.10)] p-10 text-center" style="background:#F0EEEB;">
        <flux:icon name="truck" class="w-10 h-10 mx-auto mb-3" style="color:#D0CEC9;" />
        <p style="color:#7A7775;font-size:0.9rem;">Nenhuma viatura atribuída hoje.</p>
    </div>
    @else

    {{-- Viaturas em condução --}}
    @if($emConducao->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-2 px-1">
            <span class="w-2 h-2 rounded-full" style="background:#0F6E56;"></span>
            <span class="text-[11px] font-bold uppercase tracking-wide" style="color:#0F6E56;">Em condução</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($emConducao as $v)
            <div class="rounded-xl border p-4 flex flex-col gap-2"
                 style="background:#F0EEEB;border-color:rgba(15,110,86,0.18);">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wide mb-1" style="color:#7A7775;">
                            {{ $v['equipo_tipo'] }}
                        </div>
                        <div class="text-xl font-bold" style="color:#09143B;">{{ $v['matricula'] }}</div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold"
                          style="background:#d4ede4;color:#0F6E56;">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0F6E56] animate-pulse"></span>
                        Em curso
                    </span>
                </div>
                <div class="border-t pt-2" style="border-color:rgba(9,20,59,0.08);">
                    <div class="text-[12px] font-semibold" style="color:#1A1A1A;">{{ $v['condutor'] ?? '—' }}</div>
                    @if($v['condutor_num'])
                    <div class="text-[10px]" style="color:#7A7775;">Nº {{ $v['condutor_num'] }}</div>
                    @endif
                    @if($v['desde'])
                    <div class="text-[10px] mt-1" style="color:#4A4845;">
                        Desde {{ $v['desde']->format('H:i') }}
                        <span style="color:#7A7775;">({{ $v['desde']->diffForHumans(now(), true) }})</span>
                    </div>
                    @endif
                </div>
                @if($v['log_id'])
                <button wire:click="fecharSessao({{ $v['log_id'] }})"
                        class="text-[10px] font-bold px-3 py-1 rounded-lg self-end"
                        style="background:#fde8e8;color:#A32D2D;border:1px solid rgba(163,45,45,0.20);">
                    Fechar sessão
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Viaturas sem condutor --}}
    @if($semCondutor->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-2 px-1">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background:#854F0B;"></span>
            <span class="text-[11px] font-bold uppercase tracking-wide" style="color:#854F0B;">Sem condutor registado</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($semCondutor as $v)
            <div class="rounded-xl border p-4 flex flex-col gap-2"
                 style="background:#fdf9ec;border-color:rgba(133,79,11,0.20);">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wide mb-1" style="color:#854F0B;">
                            {{ $v['equipo_tipo'] }}
                        </div>
                        <div class="text-xl font-bold" style="color:#09143B;">{{ $v['matricula'] }}</div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold"
                          style="background:#fdf0c2;color:#854F0B;">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#854F0B] animate-pulse"></span>
                        Sem condutor
                    </span>
                </div>
                <div class="border-t pt-2" style="border-color:rgba(133,79,11,0.10);">
                    <div class="text-[11px]" style="color:#854F0B;">
                        Nenhum condutor registado desde o início do turno.
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif {{-- end isEmpty check --}}
    @endif {{-- end tab estado --}}

    {{-- ====== TAB: HISTÓRICO ====== --}}
    @if($tab === 'historico')

    {{-- Filtros --}}
    <div class="rounded-xl border border-[rgba(9,20,59,0.14)] p-4" style="background:#F0EEEB;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#7A7775;">Colaborador</label>
                <input wire:model.live.debounce.300ms="searchColaborador" type="text" placeholder="Nome ou nº..."
                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg border focus:outline-none"
                       style="background:#EEECEA;border-color:rgba(9,20,59,0.16);color:#1A1A1A;">
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#7A7775;">Viatura</label>
                <input wire:model.live.debounce.300ms="searchVeiculo" type="text" placeholder="Matrícula..."
                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg border focus:outline-none"
                       style="background:#EEECEA;border-color:rgba(9,20,59,0.16);color:#1A1A1A;">
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#7A7775;">De</label>
                <input wire:model.live="dataInicio" type="date"
                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg border focus:outline-none"
                       style="background:#EEECEA;border-color:rgba(9,20,59,0.16);color:#1A1A1A;">
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-wide font-semibold mb-1" style="color:#7A7775;">Até</label>
                <input wire:model.live="dataFim" type="date"
                       class="w-full text-[12px] px-2.5 py-1.5 rounded-lg border focus:outline-none"
                       style="background:#EEECEA;border-color:rgba(9,20,59,0.16);color:#1A1A1A;">
            </div>
        </div>
        @if($filtroAberto === 'sim')
        <div class="mt-3">
            <button wire:click="$set('filtroAberto','')"
                    class="px-3 py-1 rounded-lg text-[11px] font-bold"
                    style="background:#fdf0c2;color:#854F0B;border:1px solid rgba(133,79,11,0.20);">
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
                    <tr style="background:#E4E2DF;border-bottom:1px solid rgba(9,20,59,0.10);">
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
                        $aberto   = is_null($log->ended_at);
                        $duracao  = $aberto
                            ? $log->started_at->diffForHumans(now(), true)
                            : $log->started_at->diff($log->ended_at)->format('%hh %im');
                        $zebraBase = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB';
                        $rowBg     = $aberto ? 'rgba(255,211,0,0.04)' : $zebraBase;
                    @endphp
                    <tr style="border-bottom:1px solid rgba(9,20,59,0.06);background:{{ $rowBg }};">
                        <td style="padding:12px 16px;">
                            <span style="display:inline-block;background:#E4E2DF;color:#09143B;font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:6px;">
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
                                <span style="color:#854F0B;font-size:0.75rem;font-weight:700;display:flex;align-items:center;gap:4px;">
                                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#854F0B;animation:pulse 2s infinite;"></span>
                                    Em curso
                                </span>
                            @else
                                <span style="color:#4A4845;font-size:0.82rem;">{{ $log->ended_at->format('d/m H:i') }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;color:#7A7775;font-size:0.8rem;">{{ $duracao }}</td>
                        <td style="padding:12px 16px;">
                            @if($log->takeover_from_name)
                                <span style="display:inline-block;background:#fde8e8;color:#A32D2D;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:6px;"
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
                            <span style="color:#7A7775;font-size:0.9rem;">Sem registos para os filtros selecionados.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
        <div style="border-top:1px solid rgba(9,20,59,0.08);padding:12px 16px;">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    @endif {{-- end tab historico --}}

</div>
