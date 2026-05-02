<div class="min-h-screen flex flex-col bg-slate-900" style="background-color: #0f172a;">

    @if(!$diaAtivo)
    {{-- Sin día activo --}}
    <div class="flex-1 flex flex-col items-center justify-center gap-6">
        <img src="/images/procme_logo.svg" alt="CME" style="height:80px;opacity:0.4;">
        <p style="color:rgba(255,255,255,0.35);font-size:1.4rem;font-weight:600;letter-spacing:0.05em;">
            Monitor de Equipas — Painel indisponível
        </p>
    </div>
    @else

    {{-- ═══ CABEÇALHO ═══════════════════════════════════════════════════ --}}
    <div class="pc-monitor-header" style="background:rgba(0,0,0,0.35);backdrop-filter:blur(6px);border-bottom:1px solid rgba(255,255,255,0.08);padding:14px 24px;flex-shrink:0;">
        <div class="flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <img src="/images/procme_logo.svg" alt="CME" style="height:44px;background:white;border-radius:6px;padding:4px 8px;">
                <div>
                    <div style="color:#fde047;font-size:0.9rem;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;">
                        Monitor de Equipas &mdash; Unidade C016
                    </div>
                    <div style="color:rgba(255,255,255,0.85);font-size:1.2rem;font-weight:700;text-transform:capitalize;">
                        <span id="monitor-fecha-real">{{ $dataFormato ?? '' }}</span>
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="color:rgba(255,255,255,0.3);font-size:0.7rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;">
                    Atualização em tempo real
                </div>
                <div style="color:#6ee7b7;font-size:1.6rem;font-weight:800;font-variant-numeric:tabular-nums;line-height:1;">
                    {{ $agora }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ CONTEÚDO PRINCIPAL (Scrollable) ══════════════════════════ --}}
    <div class="flex-1 overflow-y-auto p-6">
        
        {{-- Secção de Equipas em PEPs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 mb-10">
            @forelse($peps as $pep)
                @php
                    $jefes     = $pepData[$pep->id]['principal']['jefes']        ?? [];
                    $colabs    = $pepData[$pep->id]['principal']['colaboradores'] ?? [];
                    $veiculos = $pepData[$pep->id]['principal']['veiculos']     ?? [];
                    $jefesAux  = $pepData[$pep->id]['auxiliar']['jefes']          ?? [];
                    $colabsAux = $pepData[$pep->id]['auxiliar']['colaboradores']  ?? [];
                    $vehsAux   = $pepData[$pep->id]['auxiliar']['veiculos']      ?? [];
                    $temAux    = count($jefesAux) || count($colabsAux) || count($vehsAux);
                @endphp

                <div class="flex flex-col rounded-xl overflow-hidden border border-white/10 bg-white/5 backdrop-blur-md">
                    <div class="px-4 py-3 bg-blue-800/80 border-b-2 border-yellow-400">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-white font-extrabold text-sm truncate uppercase">{{ $pep->nombre }}</span>
                            @if($pep->tipoTrabalho)
                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md text-white whitespace-nowrap" style="background: {{ $pep->tipoTrabalho->color ?? '#ca8a04' }}">
                                    {{ $pep->tipoTrabalho->nombre }}
                                </span>
                            @endif
                        </div>
                        @if($pep->localizacao)
                            <div class="text-blue-200 text-[0.7rem] mt-1 flex items-center gap-1">
                                📍 {{ $pep->localizacao->nombre }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4 flex-1 flex flex-col gap-3">
                        {{-- Principal --}}
                        <div class="space-y-1">
                            @foreach($jefes as $col)
                                <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-2">
                                    <div class="text-blue-400 text-[0.55rem] font-black uppercase tracking-widest mb-0.5">Chefe de Equipa</div>
                                    <div class="text-white text-xs font-bold">{{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}</div>
                                </div>
                            @endforeach
                            
                            @foreach($colabs as $col)
                                <div class="bg-white/5 border border-white/10 rounded-lg p-2">
                                    <div class="text-white/90 text-xs font-semibold">{{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}</div>
                                </div>
                            @endforeach

                            @if(count($veiculos))
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach($veiculos as $veh)
                                        <span class="bg-purple-500/10 border border-purple-500/30 text-purple-300 text-[0.7rem] font-bold px-2 py-1 rounded-md">
                                            🚗 {{ $veh->matricula }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Auxiliar --}}
                        @if($temAux)
                            <div class="pt-2 border-t border-yellow-500/20 space-y-1">
                                <div class="text-yellow-500/60 text-[0.55rem] font-black uppercase tracking-widest mb-2 font-mono">Reforço Auxiliar</div>
                                @foreach($jefesAux as $col)
                                    <div class="bg-yellow-500/5 border border-yellow-500/20 rounded-lg p-2">
                                        <div class="text-white/90 text-xs font-bold">{{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}</div>
                                    </div>
                                @endforeach
                                @foreach($colabsAux as $col)
                                    <div class="bg-white/5 border border-white/5 rounded-lg p-1.5 px-2">
                                        <div class="text-white/80 text-[0.7rem] font-medium">{{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-white/30 font-semibold italic">Nenhuma equipa atribuída para este dia.</p>
                </div>
            @endforelse
        </div>

        {{-- Secção de Situações Especiais --}}
        @php
            $estadoConfig = [
                'baixa'           => ['label' => '🤕 Baixas',                    'color' => '#fca5a5', 'bg' => 'rgba(239,68,68,0.12)',   'border' => 'rgba(239,68,68,0.3)'],
                'licenca'         => ['label' => '📜 Licenças',                  'color' => '#fde047', 'bg' => 'rgba(234,179,8,0.12)',   'border' => 'rgba(234,179,8,0.3)'],
                'ferias'          => ['label' => '🏖 Férias',                    'color' => '#6ee7b7', 'bg' => 'rgba(16,185,129,0.1)',   'border' => 'rgba(16,185,129,0.25)'],
                'consulta_medica' => ['label' => '🏥 Consultas Médicas',         'color' => '#93c5fd', 'bg' => 'rgba(59,130,246,0.12)',  'border' => 'rgba(59,130,246,0.3)'],
                'formacao'        => ['label' => '📚 Formação',                  'color' => '#c4b5fd', 'bg' => 'rgba(139,92,246,0.12)',  'border' => 'rgba(139,92,246,0.3)'],
                'reparacao'       => ['label' => '🔧 Equipamentos em Reparação', 'color' => '#fdba74', 'bg' => 'rgba(249,115,22,0.12)',  'border' => 'rgba(249,115,22,0.3)'],
            ];
            $estadosPresentes = array_filter($estadoConfig, fn($_, $k) => !empty($estadoData[$k]), ARRAY_FILTER_USE_BOTH);
        @endphp

        <div class="border-t border-white/10 pt-8">
            <h3 class="text-yellow-400 font-black text-xs uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-yellow-400/30"></span>
                Situações Especiais do Dia
                <span class="flex-1 h-[1px] bg-yellow-400/30"></span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4 mb-10">
                @foreach($estadosPresentes as $estado => $cfg)
                    @php
                        $todosColabs = collect($estadoData[$estado])->flatMap(fn($e) => $e['colaboradores']);
                        $todosVehs   = collect($estadoData[$estado])->flatMap(fn($e) => $e['veiculos'] ?? collect());
                    @endphp
                    <div class="rounded-xl border overflow-hidden" style="background: {{ $cfg['bg'] }}; border-color: {{ $cfg['border'] }}">
                        <div class="px-4 py-2 border-b flex items-center justify-between" style="border-color: {{ $cfg['border'] }}">
                            <span class="text-xs font-black uppercase" style="color: {{ $cfg['color'] }}">{{ $cfg['label'] }}</span>
                            <span class="text-[0.65rem] font-bold text-white/30">{{ $todosColabs->count() }}</span>
                        </div>
                        <div class="p-3 flex flex-col gap-1.5">
                            @foreach($todosColabs as $col)
                                <div class="bg-black/20 rounded-md px-3 py-1.5 text-xs text-white/90 font-medium">
                                    {{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}
                                </div>
                            @endforeach
                            @foreach($todosVehs as $veh)
                                <div class="border rounded-md px-3 py-1.5 text-xs font-black flex items-center gap-2" style="border-color: {{ $cfg['border'] }}; color: {{ $cfg['color'] }}">
                                    🚗 {{ $veh->matricula }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Estaleiro --}}
                @if($estaleiroCols->isNotEmpty() || $estaleiroVehs->isNotEmpty())
                    <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/5 overflow-hidden">
                        <div class="px-4 py-2 border-b border-emerald-500/30 flex items-center justify-between">
                            <span class="text-xs font-black text-emerald-400 uppercase">🏭 Estaleiro (Disponíveis)</span>
                            <span class="text-[0.65rem] font-bold text-white/30">{{ $estaleiroCols->count() }}</span>
                        </div>
                        <div class="p-3 flex flex-col gap-1.5">
                            @foreach($estaleiroCols as $col)
                                <div class="bg-black/20 rounded-md px-3 py-1.5 text-xs text-white/80">
                                    {{ $col->numero_colaborador }} {{ explode(' ', trim($col->nombre))[0] }} {{ last(explode(' ', trim($col->apellido))) }}
                                </div>
                            @endforeach
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($estaleiroVehs as $veh)
                                    <span class="text-[0.65rem] font-bold text-emerald-300 bg-emerald-500/10 px-2 py-0.5 rounded-md">🚗 {{ $veh->matricula }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ RODAPÉ ════════════════════════════════════════════════════════ --}}
    <div style="background:rgba(0,0,0,0.3);border-top:1px solid rgba(255,255,255,0.06);padding:8px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span style="color:rgba(255,255,255,0.35);font-size:0.75rem;font-weight:600;letter-spacing:0.04em;">
            CME C016 · Monitorização em Tempo Real
        </span>
        <span style="color:rgba(255,255,255,0.2);font-size:0.75rem;">
            Pressione F11 para ecrã inteiro.
        </span>
    </div>

    <script>
    (function() {
        var dias = ['Domingo','Segunda-Feira','Terça-Feira','Quarta-Feira','Quinta-Feira','Sexta-Feira','Sábado'];
        var meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        function updateFecha() {
            var el = document.getElementById('monitor-fecha-real');
            if (!el) return;
            var now = new Date();
            el.textContent = dias[now.getDay()] + ', ' + now.getDate() + ' De ' + meses[now.getMonth()] + ' De ' + now.getFullYear();
        }
        updateFecha();
        setInterval(updateFecha, 60000);
    })();
    </script>

    @endif
</div>
