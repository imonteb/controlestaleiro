    <div class="px-3.5 pt-3 pb-20 flex flex-col gap-2.5">

        {{-- ── LATEST GUIA BANNER ─────────────────────────────────── --}}
        @if($activeColaboradorId && $ultimaGuia && !$hideUltimaGuia)
        <div class="mb-1 relative">
            {{-- Botão Principal --}}
            <button wire:click="openTab('guias')"
               class="w-full bg-linear-to-br from-blue-900 to-blue-800 border border-yellow-300 rounded-2xl p-3.5 flex items-center gap-3 text-left shadow-[0_10px_15px_-3px_rgba(0,0,0,0.3)]">
                <div class="bg-yellow-300 text-blue-900 w-10 h-10 rounded-xl flex items-center justify-center text-2xl shrink-0 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)]">
                    🚛
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-0.5">
                        <span class="text-amber-300 text-[0.6rem] font-black uppercase tracking-[0.05em]">Guia de Transporte Atual</span>
                        @php
                            $guiaStClasses = match($ultimaGuia->estado) {
                                'pendente' => 'text-yellow-400 bg-yellow-400/20 border-yellow-400/40',
                                'emitida'  => 'text-emerald-400 bg-emerald-400/20 border-emerald-400/40',
                                'recusada' => 'text-red-400 bg-red-400/20 border-red-400/40',
                                default    => 'text-gray-400 bg-gray-400/20 border-gray-400/40',
                            };
                        @endphp
                        <span class="text-[0.55rem] font-black uppercase px-1.5 py-0.5 rounded border {{ $guiaStClasses }}">
                            {{ strtoupper($ultimaGuia->estado) }}
                        </span>
                    </div>
                    <div class="text-white text-[0.9rem] font-extrabold">{{ $ultimaGuia->matricula }}</div>
                    @if($ultimaGuia->estado === 'emitida' && $ultimaGuia->numero_at)
                        <div class="text-emerald-300 text-xs font-extrabold mt-0.5">A/T: {{ $ultimaGuia->numero_at }}</div>
                    @else
                        <div class="text-white/60 text-[0.65rem]">Origem: {{ $ultimaGuia->local_carga_nome }}</div>
                    @endif
                </div>
                <div class="text-yellow-300 text-xl opacity-60">→</div>
            </button>
            {{-- Botão de fechar (X) --}}
            <button wire:click.stop="dismissUltimaGuia"
                    class="absolute top-2 right-2 w-5.5 h-5.5 bg-black/30 hover:bg-black/50 border border-white/20 text-white rounded-full flex items-center justify-center text-[10px] cursor-pointer z-20 transition-all">
                ✕
            </button>
        </div>
        @endif

        {{-- ── DATA DO DIA PUBLICADO ───────────────────────────── --}}
        @if(isset($dataFormato) && $dataFormato)
        <div class="flex items-center gap-2 px-0.5 mb-1">
            <span class="text-[0.6rem] font-extrabold tracking-[0.12em] uppercase text-white/40">📅 Equipas para</span>
            <span class="bg-yellow-300/15 border border-yellow-300/30 text-yellow-300 text-[0.65rem] font-extrabold px-2 py-0.5 rounded-full">
                {{ $dataFormato }}
            </span>
        </div>
        @endif

        @forelse($peps as $pep)
        @php
            /* Build search index for this PEP card */
            $allColsToPep = array_merge(
                $pepData[$pep->id]['principal']['jefes']        ?? [],
                $pepData[$pep->id]['principal']['colaboradores'] ?? [],
                $pepData[$pep->id]['auxiliar']['jefes']          ?? [],
                $pepData[$pep->id]['auxiliar']['colaboradores']  ?? [],
            );
            $searchTokens = [
                $pep->nombre,
                $pep->localizacao?->nombre ?? '',
            ];
            foreach ($allColsToPep as $c) {
                $searchTokens[] = $c->numero_colaborador;
                $searchTokens[] = trim($c->nombre);
                $searchTokens[] = trim($c->apellido);
            }
            $dataSearch = strtolower(implode(' ', array_filter($searchTokens)));

            $jefes     = $pepData[$pep->id]['principal']['jefes']        ?? [];
            $colabs    = $pepData[$pep->id]['principal']['colaboradores'] ?? [];
            $veiculos = $pepData[$pep->id]['principal']['veiculos']     ?? [];
            $notasP    = $pepData[$pep->id]['principal']['notas']       ?? null;
            $jefesAux  = $pepData[$pep->id]['auxiliar']['jefes']          ?? [];
            $colabsAux = $pepData[$pep->id]['auxiliar']['colaboradores']  ?? [];
            $vehsAux   = $pepData[$pep->id]['auxiliar']['veiculos']      ?? [];
            $notasAux  = $pepData[$pep->id]['auxiliar']['notas']         ?? null;
            $temAux    = count($jefesAux) || count($colabsAux) || count($vehsAux);
        @endphp

        <div class="ph-card bg-white/6 border border-white/11 rounded-2xl overflow-hidden">

            {{-- Card header --}}
            <div class="bg-blue-900 border-b-2 border-yellow-300 px-3.5 py-2.5">
                <div class="flex items-center gap-2">
                    <span class="text-white text-base font-extrabold flex-1 leading-snug">{{ $pep->nombre }}</span>
                    @if($pep->tipoTrabalho)
                    <span class="text-white text-[0.65rem] font-bold px-1.5 py-0.5 rounded whitespace-nowrap shrink-0" style="background:{{ $pep->tipoTrabalho->color ?? '#ca8a04' }};">
                        {{ $pep->tipoTrabalho->nombre }}
                    </span>
                    @endif
                </div>
                @if($pep->localizacao)
                <div class="text-white/90 text-xs mt-0.5">📍 {{ $pep->localizacao->nombre }}</div>
                @endif
            </div>

            {{-- Card body --}}
            <div class="px-3.5 py-2.5 flex flex-col gap-2">

                {{-- Observações / Notas --}}
                @if($notasP)
                <div class="bg-yellow-400/10 border-l-[3px] border-yellow-400 px-3 py-2 rounded-r-lg mb-1">
                    <div class="text-yellow-300 text-[0.6rem] font-extrabold tracking-[0.05em] uppercase mb-0.5">Instruções / Tarefa</div>
                    <div class="text-white text-[0.8rem] leading-snug">{{ $notasP }}</div>
                </div>
                @endif

                {{-- Equipa Principal --}}
                <div>
                    <div class="text-emerald-300 text-[0.6rem] font-extrabold tracking-widest uppercase mb-1.5">Equipa Principal</div>

                    @foreach($jefes as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-blue-500/18 border border-blue-500/35 rounded-lg px-2.5 py-1.5 mb-1">
                        <div class="text-white/90 text-[0.58rem] font-extrabold uppercase tracking-[0.06em] mb-0.5">👑 Chefe de Equipa</div>
                        <div class="text-white text-[0.9rem] font-bold">{{ $n }}</div>
                    </div>
                    @endforeach

                    @foreach($colabs as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-white/4 border border-white/9 rounded-lg px-2.5 py-1.5 mb-1">
                        <div class="text-white text-[0.88rem] font-semibold">{{ $n }}</div>
                    </div>
                    @endforeach

                    @if(count($veiculos))
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        @foreach($veiculos as $veh)
                        @php
                            $currentLog = \App\Models\VehicleDriverLog::where('vehicle_id', $veh->id)->whereNull('ended_at')->first();
                        @endphp
                        <div class="flex flex-col gap-1">
                            <span class="bg-violet-400/18 border border-violet-400/35 text-violet-300 text-xs font-bold px-2.5 py-0.5 rounded-md flex items-center gap-1.5">
                                🚗 {{ $veh->matricula }}
                                @if($currentLog)
                                    <span class="text-[0.6rem] text-white/80">(👤 {{ explode(' ', $currentLog->colaborador->nombre)[0] }})</span>
                                @endif
                            </span>

                            @if($veh->link_seguros)
                            <a href="{{ $veh->link_seguros }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;border-radius:4px;background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.35);color:#7dd3fc;padding:2px 8px;text-decoration:none;font-size:0.6rem;font-weight:700;">
                                <svg style="width:10px;height:10px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                                Seguro
                            </a>
                            @endif

                            @if(!$currentLog)
                                <button wire:click="registrarCondutor({{ $veh->id }})"
                                        class="bg-emerald-400/10 border border-emerald-400/30 text-emerald-300 text-[0.6rem] font-bold px-1.5 py-0.5 rounded">
                                    Sou eu que conduzo
                                </button>
                            @elseif($activeColaboradorId && $currentLog->colaborador_id === $activeColaboradorId)
                                <button wire:click="liberarVeiculo({{ $veh->id }})"
                                        class="bg-red-500/10 border border-red-500/30 text-red-300 text-[0.6rem] font-bold px-1.5 py-0.5 rounded">
                                    Libertar
                                </button>
                            @else
                                <button wire:click="registrarCondutor({{ $veh->id }})"
                                        class="bg-amber-400/10 border border-amber-400/30 text-amber-300 text-[0.6rem] font-bold px-1.5 py-0.5 rounded">
                                    Assumir condução
                                </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(!count($jefes) && !count($colabs) && !count($veiculos))
                    <p class="text-white/30 text-[0.78rem] italic">Sem atribuições</p>
                    @endif
                </div>

                {{-- Equipa Auxiliar --}}
                @if($temAux)
                <div class="border-t border-white/8 pt-2">
                    <div class="text-yellow-300 text-[0.6rem] font-extrabold tracking-widest uppercase mb-1.5">Equipa Auxiliar</div>

                    @if($notasAux && $notasAux !== $notasP)
                    <div class="bg-yellow-400/10 border-l-[3px] border-yellow-400 px-3 py-2 rounded-r-lg mb-2">
                        <div class="text-yellow-300 text-[0.6rem] font-extrabold tracking-[0.05em] uppercase mb-0.5">Instruções (Auxiliar)</div>
                        <div class="text-white text-[0.8rem] leading-snug">{{ $notasAux }}</div>
                    </div>
                    @endif

                    @foreach($jefesAux as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-yellow-500/13 border border-yellow-500/30 rounded-lg px-2.5 py-1.5 mb-1">
                        <div class="text-yellow-300 text-[0.58rem] font-extrabold uppercase tracking-[0.06em] mb-0.5">⭐ Líder Auxiliar</div>
                        <div class="text-white text-[0.88rem] font-bold">{{ $n }}</div>
                    </div>
                    @endforeach

                    @foreach($colabsAux as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-white/3 border border-white/7 rounded-lg px-2.5 py-1.5 mb-1">
                        <div class="text-white text-[0.85rem] font-semibold">{{ $n }}</div>
                    </div>
                    @endforeach

                    @if(count($vehsAux))
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        @foreach($vehsAux as $veh)
                        <span class="bg-violet-400/13 border border-violet-400/28 text-violet-300 text-[0.73rem] font-bold px-2 py-0.5 rounded-md">🚗 {{ $veh->matricula }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>

        @empty
        <div class="text-center py-16 px-5">
            <p class="text-white/30 text-[0.95rem] font-semibold">Nenhuma equipa atribuída para este dia.</p>
        </div>
        @endforelse

        {{-- ── BOTÃO VER TODAS AS EQUIPAS ─────────────────────────────────── --}}
        @if($activeColaboradorId && $todasEquipas->count() > 0)
        <button wire:click="$toggle('verTodasEquipas')"
                class="w-full bg-white/6 border border-white/15 rounded-2xl px-4 py-3 flex items-center justify-between text-left">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-300/15 w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0">👥</div>
                <div>
                    <div class="text-yellow-300 text-[0.8rem] font-extrabold">Ver todas as equipas</div>
                    <div class="text-white/40 text-[0.65rem]">{{ $todasEquipas->count() }} equipa{{ $todasEquipas->count() !== 1 ? 's' : '' }} no activo hoje</div>
                </div>
            </div>
            <svg class="w-4 h-4 text-white/40 transition-transform duration-200 {{ $verTodasEquipas ? 'rotate-180' : '' }}"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        @if($verTodasEquipas)
        <div class="flex flex-col gap-2.5 mt-1">
            <div class="text-white/90 text-[0.62rem] font-extrabold tracking-[0.12em] uppercase px-0.5">
                Todas as equipas — {{ \Carbon\Carbon::parse($data)->locale('pt')->translatedFormat('d F') }}
            </div>
            @foreach($todasEquipas as $pep)
            @php
                $jefesT  = $pepData[$pep->id]['principal']['jefes']        ?? [];
                $colabsT = $pepData[$pep->id]['principal']['colaboradores'] ?? [];
                $vehsT   = $pepData[$pep->id]['principal']['veiculos']     ?? [];
                $jefesTA = $pepData[$pep->id]['auxiliar']['jefes']          ?? [];
                $colabsTA= $pepData[$pep->id]['auxiliar']['colaboradores']  ?? [];
                $vehsTA  = $pepData[$pep->id]['auxiliar']['veiculos']      ?? [];
                $temAuxT = count($jefesTA) || count($colabsTA) || count($vehsTA);
            @endphp
            <div class="bg-white/4 border border-white/10 rounded-2xl overflow-hidden">
                <div class="bg-blue-900/70 border-b border-yellow-300/40 px-3.5 py-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-white text-[0.95rem] font-extrabold flex-1 leading-snug">{{ $pep->nombre }}</span>
                        @if($pep->tipoTrabalho)
                        <span class="text-white text-[0.6rem] font-bold px-1.5 py-0.5 rounded shrink-0"
                              style="background:{{ $pep->tipoTrabalho->color ?? '#ca8a04' }};">
                            {{ $pep->tipoTrabalho->nombre }}
                        </span>
                        @endif
                    </div>
                    @if($pep->localizacao)
                    <div class="text-white/70 text-[0.68rem] mt-0.5">📍 {{ $pep->localizacao->nombre }}</div>
                    @endif
                </div>
                <div class="px-3.5 py-2.5 flex flex-col gap-1">
                    @foreach($jefesT as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-blue-500/15 border border-blue-500/30 rounded-lg px-2.5 py-1.5">
                        <div class="text-white/60 text-[0.55rem] font-extrabold uppercase tracking-wider mb-0.5">👑 Chefe</div>
                        <div class="text-white text-[0.85rem] font-bold">{{ $n }}</div>
                    </div>
                    @endforeach
                    @foreach($colabsT as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-white/4 border border-white/8 rounded-lg px-2.5 py-1.5">
                        <div class="text-white text-[0.82rem] font-semibold">{{ $n }}</div>
                    </div>
                    @endforeach
                    @if(count($vehsT))
                    <div class="flex flex-wrap gap-1.5 mt-0.5">
                        @foreach($vehsT as $veh)
                        <span class="bg-violet-400/15 border border-violet-400/30 text-violet-300 text-[0.72rem] font-bold px-2 py-0.5 rounded-md">🚗 {{ $veh->matricula }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($temAuxT)
                    <div class="border-t border-white/6 pt-1.5 mt-0.5">
                        <div class="text-yellow-300/70 text-[0.58rem] font-extrabold tracking-widest uppercase mb-1">Auxiliar</div>
                        @foreach($jefesTA as $col)
                        @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                        <div class="bg-yellow-500/10 border border-yellow-500/25 rounded-lg px-2.5 py-1.5 mb-1">
                            <div class="text-white text-[0.82rem] font-bold">{{ $n }}</div>
                        </div>
                        @endforeach
                        @foreach($colabsTA as $col)
                        @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                        <div class="bg-white/3 border border-white/6 rounded-lg px-2.5 py-1.5 mb-1">
                            <div class="text-white text-[0.8rem]">{{ $n }}</div>
                        </div>
                        @endforeach
                        @if(count($vehsTA))
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @foreach($vehsTA as $veh)
                            <span class="bg-violet-400/10 border border-violet-400/20 text-violet-300/80 text-[0.7rem] font-bold px-2 py-0.5 rounded-md">🚗 {{ $veh->matricula }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                    @if(!count($jefesT) && !count($colabsT) && !count($vehsT))
                    <p class="text-white/25 text-[0.75rem] italic">Sem atribuições</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endif


        {{-- ── ESTALEIRO (disponíveis) — só aparece quando há pessoal/veículos disponíveis --}}
        @if($estaleiroCols->isNotEmpty() || $estaleiroVehs->isNotEmpty())
        @php
            $estSearch = 'estaleiro funchal disponível ';
            foreach($estaleiroCols as $c) {
                $estSearch .= $c->numero_colaborador.' '.mb_strtolower($c->nombre).' '.mb_strtolower($c->apellido).' ';
            }
            foreach($estaleiroVehs as $v) {
                $estSearch .= mb_strtolower($v->matricula).' ';
            }
            $estSearch = trim($estSearch);
        @endphp
        <div class="ph-card bg-white/6 border border-white/11 rounded-2xl overflow-hidden">

            <div class="bg-emerald-950 border-b-2 border-emerald-300 px-3.5 py-2.5">
                <div class="flex items-center gap-2">
                    <span class="text-white text-base font-extrabold flex-1 leading-snug">Estaleiro</span>
                    <span class="bg-emerald-900 border border-emerald-300 text-emerald-300 text-[0.65rem] font-bold px-1.5 py-0.5 rounded">Disponíveis</span>
                </div>
                <div class="text-emerald-200 text-xs mt-0.5">📍 Funchal — pessoal e veículos sem afectação</div>
            </div>

            <div class="px-3.5 py-2.5 flex flex-col gap-2">
                @if($estaleiroCols->isEmpty() && $estaleiroVehs->isEmpty())
                <p class="text-white/30 text-[0.78rem] italic">Todos assignados hoje.</p>
                @else
                    @if($estaleiroCols->isNotEmpty())
                    <div>
                        <div class="text-emerald-300 text-[0.6rem] font-extrabold tracking-widest uppercase mb-1.5">Pessoal disponível</div>
                        @foreach($estaleiroCols as $col)
                        @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                        <div class="bg-white/4 border border-white/9 rounded-lg px-2.5 py-1.5 mb-1">
                            <div class="text-white text-[0.88rem] font-semibold">{{ $n }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @if($estaleiroVehs->isNotEmpty())
                    <div>
                        <div class="text-emerald-300 text-[0.6rem] font-extrabold tracking-widest uppercase mb-1.5">Veículos disponíveis</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($estaleiroVehs as $veh)
                            <span class="bg-emerald-400/15 border border-emerald-400/35 text-emerald-300 text-xs font-bold px-2.5 py-0.5 rounded-md">🚗 {{ $veh->matricula }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        @endif {{-- estaleiro not empty --}}

    </div>

    {{-- ── SITUAÇÕES ESPECIAIS ─────────────────────────────────── --}}
    @php
        $estadoConfig = [
            'baixa'           => ['label' => 'Baixas',                    'icon' => '🤒', 'card' => 'bg-red-500/12 border-red-500/35',      'title' => 'text-red-300',    'veh' => 'text-red-300'],
            'licenca'         => ['label' => 'Licenças',                  'icon' => '📜', 'card' => 'bg-yellow-500/12 border-yellow-500/35', 'title' => 'text-yellow-300', 'veh' => 'text-yellow-300'],
            'ferias'          => ['label' => 'Férias',                    'icon' => '🏖',  'card' => 'bg-emerald-500/10 border-emerald-500/30','title' => 'text-emerald-300','veh' => 'text-emerald-300'],
            'consulta_medica' => ['label' => 'Consultas Médicas',         'icon' => '🏥', 'card' => 'bg-blue-500/12 border-blue-500/35',     'title' => 'text-blue-300',   'veh' => 'text-blue-300'],
            'formacao'        => ['label' => 'Formação',                  'icon' => '📚', 'card' => 'bg-violet-500/12 border-violet-500/35', 'title' => 'text-violet-300', 'veh' => 'text-violet-300'],
            'reparacao'       => ['label' => 'Equipamentos em Reparação', 'icon' => '🔧', 'card' => 'bg-orange-500/12 border-orange-500/35', 'title' => 'text-orange-300', 'veh' => 'text-orange-300'],
        ];
        $estadosPresentes = array_filter($estadoConfig, fn($_, $k) => !empty($estadoData[$k]), ARRAY_FILTER_USE_BOTH);
    @endphp

    @if(count($estadosPresentes) > 0)
    <div class="px-3.5 pb-28">
        <div class="text-white/30 text-[0.62rem] font-extrabold tracking-[0.12em] uppercase py-3.5 px-0.5 border-t border-white/7">
            Situações especiais do dia
        </div>
        <div class="flex flex-col gap-2.5">
            @foreach($estadosPresentes as $estado => $cfg)
            @php
                $todosColabs = collect($estadoData[$estado])->flatMap(fn($e) => $e['colaboradores']);
                $todosVehs   = collect($estadoData[$estado])->flatMap(fn($e) => $e['veiculos'] ?? collect());
                $stTokens = [$cfg['label'], $estado];
                foreach ($todosColabs as $c) {
                    $stTokens[] = $c->numero_colaborador;
                    $stTokens[] = trim($c->nombre);
                    $stTokens[] = trim($c->apellido);
                }
                foreach ($todosVehs as $v) {
                    $stTokens[] = $v->matricula;
                }
                $stSearch = strtolower(implode(' ', array_filter($stTokens)));
            @endphp
            <div class="ph-card border rounded-2xl overflow-hidden {{ $cfg['card'] }}"
                 x-show="matches('{{ $stSearch }}')"
                 x-transition:enter="transition-opacity duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">

                <div class="border-b px-3.5 py-2.5 flex items-center gap-2 {{ $cfg['card'] }}">
                    <span class="text-[0.9rem] font-extrabold {{ $cfg['title'] }}">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                    <span class="text-white/30 text-[0.72rem] font-semibold">
                        {{ $todosColabs->count() + $todosVehs->count() }} elemento{{ ($todosColabs->count() + $todosVehs->count()) !== 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="px-3.5 py-2.5 flex flex-col gap-1.5">
                    @foreach($todosColabs as $col)
                    @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                    <div class="bg-white/5 rounded-lg px-2.5 py-1.5">
                        <div class="text-white text-[0.88rem] font-semibold">{{ $n }}</div>
                    </div>
                    @endforeach

                    @foreach($estadoData[$estado] as $entry)
                        @foreach($entry['veiculos'] as $veh)
                        <div class="bg-white/7 border rounded-lg px-2.5 py-1.5 {{ $cfg['card'] }}">
                            <div class="text-[0.88rem] font-extrabold {{ $cfg['veh'] }}">🚗 {{ $veh->matricula }}</div>
                            @if($veh->marca || $veh->modelo)
                            <div class="text-white/45 text-[0.72rem]">{{ $veh->marca }} {{ $veh->modelo }}</div>
                            @endif
                            @if(!empty($entry['fecha_entrada_taller']))
                            <div class="text-white/45 text-[0.7rem] mt-0.5">📅 {{ \Carbon\Carbon::parse($entry['fecha_entrada_taller'])->format('d/m/Y') }}</div>
                            @endif
                            @if(!empty($entry['nombre_taller']))
                            <div class="text-white/35 text-[0.7rem] italic">🏪 {{ $entry['nombre_taller'] }}</div>
                            @endif
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    @if(!$peps->count() && $estaleiroCols->isEmpty() && $activeColaboradorId)
    <div class="text-center py-24 px-8">
        <div class="bg-white/5 p-8 rounded-2xl border border-white/10">
            <div class="text-5xl mb-4">📅</div>
            <h2 class="text-white text-xl font-extrabold mb-2">Sem atribuição hoje</h2>
            <p class="text-white text-[0.9rem]">Não foi encontrado em nenhuma equipa ou no estaleiro para o dia de hoje.</p>
        </div>
    </div>
    @endif

    <div class="pb-30"></div>
    @endif

