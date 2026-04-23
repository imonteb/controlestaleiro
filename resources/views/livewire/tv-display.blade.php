{{-- CAROUSEL TV — 3 slides: Equipas | Situações especiais | Informações --}}
<div class="min-h-screen flex flex-col" wire:poll.60000ms>

    @if(!$diaAtivo)
    {{-- Sin día activo --}}
    <div class="flex-1 flex flex-col items-center justify-center gap-6">
        <img src="/images/procme_logo.svg" alt="CME" style="height:80px;opacity:0.4;">
        <p style="color:white;font-size:1.4rem;font-weight:600;letter-spacing:0.05em;">
            Painel indisponível — contacte o responsável de turno
        </p>
    </div>
    @else

    {{-- ═══ CABEÇALHO ═══════════════════════════════════════════════════ --}}
    <div class="tv-header" style="background:rgba(0,0,0,0.35);backdrop-filter:blur(6px);border-bottom:1px solid rgba(255,255,255,0.08);padding:18px 32px;flex-shrink:0;">
        <div class="flex items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <img class="tv-header-logo" src="/images/procme_logo.svg" alt="CME" style="height:52px;background:white;border-radius:8px;padding:6px 10px;">
                <div>
                    <div class="tv-header-title" style="color:#fde047;font-size:1.05rem;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;">
                        Atribuição de Equipas &mdash; Unidade C016
                    </div>
                    <div class="tv-header-date" style="color:white;font-size:1.4rem;font-weight:700;text-transform:capitalize;margin-top:2px;">
                        {{ $dataFormato ?? '' }}
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="color:white;font-size:0.78rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;opacity:0.9;">
                    Atualizado às
                </div>
                <div class="tv-clock" style="color:#6ee7b7;font-size:2rem;font-weight:800;font-variant-numeric:tabular-nums;line-height:1;">
                    {{ $agora }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ CAROUSEL ════════════════════════════════════════════════════ --}}
    @php
        $pepChunks      = $peps->count() > 14 ? $peps->values()->chunk(14) : collect([$peps->values()]);
        $pepSlideCount  = max(1, $pepChunks->count());
        $forceSevenCols = $peps->count() > 14;
        $slideCount     = $pepSlideCount + 1 + ($avisos->count() > 0 ? 1 : 0);
        $slideLabels    = [];
        for ($i = 0; $i < $pepSlideCount; $i++) {
            $slideLabels[] = $pepSlideCount > 1 ? 'Equipas '.($i+1).'/'.$pepSlideCount : 'Equipas';
        }
        $slideLabels[] = 'Situações';
        if ($avisos->count() > 0) $slideLabels[] = 'Informações';
        $durationsJs = implode(', ', array_merge(
            array_fill(0, $pepSlideCount, 25000),
            [20000],
            $avisos->count() > 0 ? [20000] : []
        ));
    @endphp

    <div class="flex-1 relative overflow-hidden"
         x-data="{
             slide: 0,
             slideCount: {{ $slideCount }},
             durations: [{{ $durationsJs }}],
             start() {
                 const self = this;
                 const advance = () => {
                     self.slide = (self.slide + 1) % self.slideCount;
                     setTimeout(advance, self.durations[self.slide]);
                 };
                 setTimeout(advance, self.durations[0]);
             }
         }"
         x-init="start()">

        {{-- ─── Slides Equipas (até 14 por slide, 7 colunas se >14) ── --}}
        @foreach($pepChunks as $pepPageIdx => $pepChunk)
        <div x-show="slide === {{ $pepPageIdx }}"
             x-transition:enter="transition-opacity duration-700 ease-in-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-700 ease-in-out"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="position:absolute;inset:0;overflow-y:auto;"
             class="tv-grid-wrap p-5">

            @if($pepChunk->isEmpty())
            <div class="flex items-center justify-center h-full">
                <p style="color:white;font-size:1.2rem;font-weight:600;">
                    Nenhuma equipa atribuída para este dia.
                </p>
            </div>
            @else
            <div @if($forceSevenCols) style="display:grid;grid-template-columns:repeat(7,1fr);gap:12px;" @else class="tv-grid" @endif>
                @foreach($pepChunk as $pep)
                @php
                    $jefes     = $pepData[$pep->id]['principal']['jefes']        ?? [];
                    $colabs    = $pepData[$pep->id]['principal']['colaboradores'] ?? [];
                    $veiculos = $pepData[$pep->id]['principal']['veiculos']     ?? [];
                    $jefesAux  = $pepData[$pep->id]['auxiliar']['jefes']          ?? [];
                    $colabsAux = $pepData[$pep->id]['auxiliar']['colaboradores']  ?? [];
                    $vehsAux   = $pepData[$pep->id]['auxiliar']['veiculos']      ?? [];
                    $temAux    = count($jefesAux) || count($colabsAux) || count($vehsAux);
                @endphp
                <div style="background:rgba(255,255,255,0.07);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.12);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;">
                    <div class="tv-card-header" style="background-color:#1e40af;border-bottom:2px solid #fde047;padding:14px 18px;">
                        <div class="flex items-center gap-2">
                            <span class="tv-pep-name" style="color:white;font-size:1.1rem;font-weight:800;flex:1;">{{ $pep->nombre }}</span>
                            @if($pep->tipoTrabalho)
                            <span style="background:{{ $pep->tipoTrabalho->color ?? '#ca8a04' }};color:white;font-size:0.72rem;font-weight:700;padding:3px 8px;border-radius:6px;">
                                {{ $pep->tipoTrabalho->nombre }}
                            </span>
                            @endif
                        </div>
                        @if($pep->localizacao)
                        <div style="color:#bfdbfe;font-size:0.8rem;margin-top:4px;">📍 {{ $pep->localizacao->nombre }}</div>
                        @endif
                    </div>
                    <div class="tv-card-body" style="padding:14px 18px;flex:1;display:flex;flex-direction:column;gap:6px;">
                        <div>
                            @foreach($jefes as $col)
                            @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                            <div class="tv-col-item" style="background:rgba(59,130,246,0.2);border:1px solid rgba(59,130,246,0.4);border-radius:8px;padding:8px 12px;margin-bottom:5px;">
                                <div style="color:#93c5fd;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px;">Chefe de Equipa</div>
                                <div class="tv-col-name" style="color:white;font-size:0.95rem;font-weight:700;">{{ $n }}</div>
                            </div>
                            @endforeach
                            @foreach($colabs as $col)
                            @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                            <div class="tv-col-item" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:7px 12px;margin-bottom:4px;">
                                <div class="tv-col-name" style="color:white;font-size:0.9rem;font-weight:600;">{{ $n }}</div>
                            </div>
                            @endforeach
                            @if(count($veiculos))
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;">
                                @foreach($veiculos as $veh)
                                <span style="background:rgba(167,139,250,0.2);border:1px solid rgba(167,139,250,0.4);color:#c4b5fd;font-size:0.8rem;font-weight:700;padding:4px 10px;border-radius:6px;">🚗 {{ $veh->matricula }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if(!count($jefes) && !count($colabs) && !count($veiculos))
                            <p style="color:white;font-size:0.8rem;font-style:italic;opacity:0.8;">Sem atribuições</p>
                            @endif
                        </div>
                        @if($temAux)
                        <div style="border-top:1px solid rgba(255,215,0,0.25);padding-top:6px;">
                            @foreach($jefesAux as $col)
                            @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                            <div class="tv-col-item" style="background:rgba(234,179,8,0.15);border:1px solid rgba(234,179,8,0.35);border-radius:8px;padding:7px 12px;margin-bottom:5px;">
                                <div style="color:#fde047;font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px;">Líder Auxiliar</div>
                                <div class="tv-col-name" style="color:white;font-size:0.9rem;font-weight:700;">{{ $n }}</div>
                            </div>
                            @endforeach
                            @foreach($colabsAux as $col)
                            @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                            <div class="tv-col-item" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:6px 12px;margin-bottom:4px;">
                                <div class="tv-col-name" style="color:white;font-size:0.88rem;font-weight:600;">{{ $n }}</div>
                            </div>
                            @endforeach
                            @if(count($vehsAux))
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;">
                                @foreach($vehsAux as $veh)
                                <span style="background:rgba(167,139,250,0.15);border:1px solid rgba(167,139,250,0.3);color:#c4b5fd;font-size:0.78rem;font-weight:700;padding:3px 9px;border-radius:6px;">🚗 {{ $veh->matricula }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach

        {{-- ─── Slide Situações Especiais ───────────────────────── --}}
        @php
            $estadoConfig = [
                'baixa'           => ['label' => '🤒 Baixas',                    'color' => '#fca5a5', 'bg' => 'rgba(239,68,68,0.12)',   'border' => 'rgba(239,68,68,0.3)'],
                'licenca'         => ['label' => '📜 Licenças',                  'color' => '#fde047', 'bg' => 'rgba(234,179,8,0.12)',   'border' => 'rgba(234,179,8,0.3)'],
                'ferias'          => ['label' => '🏖 Férias',                    'color' => '#6ee7b7', 'bg' => 'rgba(16,185,129,0.1)',   'border' => 'rgba(16,185,129,0.25)'],
                'consulta_medica' => ['label' => '🏥 Consultas Médicas',         'color' => '#93c5fd', 'bg' => 'rgba(59,130,246,0.12)',  'border' => 'rgba(59,130,246,0.3)'],
                'formacao'        => ['label' => '📚 Formação',                  'color' => '#c4b5fd', 'bg' => 'rgba(139,92,246,0.12)',  'border' => 'rgba(139,92,246,0.3)'],
                'reparacao'       => ['label' => '🔧 Equipamentos em Reparação', 'color' => '#fdba74', 'bg' => 'rgba(249,115,22,0.12)',  'border' => 'rgba(249,115,22,0.3)'],
            ];
            $estadosPresentes = array_filter($estadoConfig, fn($_, $k) => !empty($estadoData[$k]), ARRAY_FILTER_USE_BOTH);
        @endphp
        <div x-show="slide === {{ $pepSlideCount }}"
             x-transition:enter="transition-opacity duration-700 ease-in-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-700 ease-in-out"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="position:absolute;inset:0;overflow-y:auto;padding:28px 32px;">

            <div style="color:#fde047;font-size:0.8rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.1);">
                Situações especiais do dia
            </div>

            @if(count($estadosPresentes) === 0 && $estaleiroCols->isEmpty() && $estaleiroVehs->isEmpty())
            <div class="flex items-center justify-center" style="height:60%;">
                <p style="color:white;font-size:1.1rem;font-weight:600;font-style:italic;opacity:0.9;">Sem situações especiais hoje.</p>
            </div>
            @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;">
                @foreach($estadosPresentes as $estado => $cfg)
                @php
                    $todosColabs = collect($estadoData[$estado])->flatMap(fn($e) => $e['colaboradores']);
                    $todosVehs   = collect($estadoData[$estado])->flatMap(fn($e) => $e['veiculos'] ?? collect());
                @endphp
                <div style="background:{{ $cfg['bg'] }};border:1px solid {{ $cfg['border'] }};border-radius:16px;overflow:hidden;">
                    <div style="border-bottom:1px solid {{ $cfg['border'] }};padding:12px 18px;display:flex;align-items:center;gap:10px;">
                        <span style="color:{{ $cfg['color'] }};font-size:0.92rem;font-weight:800;letter-spacing:0.03em;">{{ $cfg['label'] }}</span>
                        <span style="color:white;font-size:0.78rem;font-weight:600;opacity:0.9;">
                            {{ $todosColabs->count() + $todosVehs->count() }} elemento{{ ($todosColabs->count() + $todosVehs->count()) !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:7px;">
                        @foreach($todosColabs as $col)
                        @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                        <div style="background:rgba(255,255,255,0.06);border-radius:8px;padding:8px 14px;">
                            <div style="color:white;font-size:0.92rem;font-weight:600;">{{ $n }}</div>
                        </div>
                        @endforeach
                        @if($todosVehs->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px;">
                            @foreach($estadoData[$estado] as $entry)
                                @foreach($entry['veiculos'] as $veh)
                                <div style="background:rgba(255,255,255,0.08);border:1px solid {{ $cfg['border'] }};border-radius:10px;padding:9px 14px;">
                                    <div style="color:{{ $cfg['color'] }};font-size:0.92rem;font-weight:800;">🚗 {{ $veh->matricula }}</div>
                                    @if($veh->marca || $veh->modelo)
                                    <div style="color:rgba(255,255,255,0.5);font-size:0.78rem;">{{ $veh->marca }} {{ $veh->modelo }}</div>
                                    @endif
                                    @if(!empty($entry['fecha_entrada_taller']))
                                    <div style="color:rgba(255,255,255,0.55);font-size:0.75rem;margin-top:3px;">📅 Entrada: {{ \Carbon\Carbon::parse($entry['fecha_entrada_taller'])->format('d/m/Y') }}</div>
                                    @endif
                                    @if(!empty($entry['nombre_taller']))
                                    <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;font-style:italic;">🏪 {{ $entry['nombre_taller'] }}</div>
                                    @endif
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Estaleiro: pessoal e veículos disponíveis --}}
                @if($estaleiroCols->isNotEmpty() || $estaleiroVehs->isNotEmpty())
                <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:16px;overflow:hidden;">
                    <div style="border-bottom:1px solid rgba(16,185,129,0.25);padding:12px 18px;display:flex;align-items:center;gap:10px;">
                        <span style="color:#6ee7b7;font-size:0.92rem;font-weight:800;letter-spacing:0.03em;">🏭 Estaleiro — Disponíveis</span>
                        <span style="color:rgba(255,255,255,0.35);font-size:0.78rem;font-weight:600;">
                            {{ $estaleiroCols->count() + $estaleiroVehs->count() }} elemento{{ ($estaleiroCols->count() + $estaleiroVehs->count()) !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:7px;">
                        @foreach($estaleiroCols as $col)
                        @php $n = $col->numero_colaborador.' '.explode(' ', trim($col->nombre))[0].' '.last(explode(' ', trim($col->apellido))); @endphp
                        <div style="background:rgba(255,255,255,0.06);border-radius:8px;padding:8px 14px;">
                            <div style="color:white;font-size:0.92rem;font-weight:600;">{{ $n }}</div>
                        </div>
                        @endforeach
                        @if($estaleiroVehs->isNotEmpty())
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                            @foreach($estaleiroVehs as $veh)
                            <span style="background:rgba(110,231,183,0.15);border:1px solid rgba(110,231,183,0.3);color:#6ee7b7;font-size:0.82rem;font-weight:700;padding:4px 11px;border-radius:6px;">🚗 {{ $veh->matricula }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- ─── Slide 2: INFORMAÇÕES / AVISOS ───────────────────── --}}
        @if($avisos->count() > 0)
        @php $avisosChunks = $avisos->chunk(3); @endphp
        <div x-show="slide === {{ $pepSlideCount + 1 }}"
             x-data="{ avisoPage: 0, avisoTotal: {{ $avisosChunks->count() }} }"
             x-transition:enter="transition-opacity duration-700 ease-in-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-700 ease-in-out"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="position:absolute;inset:0;overflow:hidden;padding:28px 32px;display:flex;flex-direction:column;">

            {{-- Cabeçalho --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0;">
                <div style="color:#fde047;font-size:0.8rem;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;">
                    📢 Informações
                </div>
                @if($avisosChunks->count() > 1)
                <div style="display:flex;align-items:center;gap:10px;">
                    <button @click="avisoPage = (avisoPage - 1 + avisoTotal) % avisoTotal"
                            style="background:rgba(255,255,255,0.1);border:none;color:white;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;line-height:1;display:flex;align-items:center;justify-content:center;">‹</button>
                    <span style="color:rgba(255,255,255,0.45);font-size:0.72rem;font-weight:600;" x-text="(avisoPage+1)+'/'+avisoTotal"></span>
                    <button @click="avisoPage = (avisoPage + 1) % avisoTotal"
                            style="background:rgba(255,255,255,0.1);border:none;color:white;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;line-height:1;display:flex;align-items:center;justify-content:center;">›</button>
                </div>
                @endif
            </div>

            {{-- Páginas de 3 avisos --}}
            <div style="flex:1;overflow:hidden;">
                @foreach($avisosChunks as $pageIdx => $chunk)
                <div :style="{ display: avisoPage === {{ $pageIdx }} ? 'grid' : 'none' }"
                     style="grid-template-columns:repeat({{ min($chunk->count(), 3) }},1fr);gap:22px;height:100%;">
                    @foreach($chunk as $aviso)
                    @php
                        $s = $aviso->corEstilo();
                        $imgRight = $loop->index % 2 !== 0;
                        $layoutTopo = $aviso->imagem && ($aviso->layout_imagem ?? 'lateral') === 'topo';
                        $flexDir = $aviso->imagem
                            ? ($layoutTopo ? 'column' : ($imgRight ? 'row-reverse' : 'row'))
                            : 'column';
                    @endphp
                    <div style="background:{{ $s['bg'] }};border:2px solid {{ $s['border'] }};border-radius:18px;overflow:hidden;display:flex;flex-direction:{{ $flexDir }};">
                        @if($aviso->imagem)
                            @if($layoutTopo)
                            <div style="width:100%;height:52%;min-height:52%;overflow:hidden;flex-shrink:0;">
                                <img src="{{ $aviso->imageUrl() }}" alt=""
                                     style="width:100%;height:100%;object-fit:cover;object-position:center;">
                            </div>
                            @else
                            <div style="width:42%;min-width:42%;overflow:hidden;flex-shrink:0;">
                                <img src="{{ $aviso->imageUrl() }}" alt=""
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            @endif
                        @endif
                        <div style="padding:{{ $layoutTopo ? '16px 22px' : '22px 26px' }};flex:1;display:flex;flex-direction:column;justify-content:center;">
                            <div style="color:{{ $s['text'] }};font-size:1.35rem;font-weight:800;line-height:1.3;margin-bottom:{{ $aviso->conteudo ? '10px' : '0' }};">
                                {{ $aviso->titulo }}
                            </div>
                            @if($aviso->conteudo)
                            <div style="color:rgba(255,255,255,0.78);font-size:0.98rem;font-weight:500;line-height:1.6;white-space:pre-line;">{{ $aviso->conteudo }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ─── Indicadores de slide ─────────────────────────────── --}}
        <div style="position:absolute;bottom:14px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:8px;z-index:10;">
            @foreach($slideLabels as $i => $label)
            @if($i < $slideCount)
            <button @click="slide = {{ $i }}"
                    :style="slide === {{ $i }}
                        ? 'background:rgba(253,224,71,0.85);color:#1e1b4b;width:110px;'
                        : 'background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.45);width:85px;'"
                    style="border:none;cursor:pointer;border-radius:20px;padding:5px 0;font-size:0.66rem;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;transition:all 0.4s ease;">
                {{ $label }}
            </button>
            @endif
            @endforeach
        </div>

    </div>{{-- /carousel --}}

    {{-- ═══ RODAPÉ ════════════════════════════════════════════════════════ --}}
    <div style="background:rgba(0,0,0,0.3);border-top:1px solid rgba(255,255,255,0.06);padding:10px 32px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span style="color:white;font-size:0.72rem;font-weight:600;letter-spacing:0.06em;opacity:0.8;">
            CME C016 · Construção e Manutenção Electromecânica S.A
        </span>
        <span style="color:white;font-size:0.7rem;opacity:0.7;">
            Atualização automática a cada 60 seg.
        </span>
    </div>

    @endif
</div>
