<div class="flex flex-col lg:flex-row gap-6 w-full items-start" x-data="dragAndDrop()">

    <!-- Painel Esquerdo: PEPs e Áreas Especiais -->
    <div class="w-full lg:flex-1 lg:min-w-0 flex flex-col gap-6">

        <div class="mb-6">
            @php
                $hoje = \Carbon\Carbon::now();
                // Mostrar o lembrete nos primeiros 12 dias do mês (margem para impressões mensais)
                $mostrarLembreteEpi = $hoje->day <= 12;
                $mesAnterior = $hoje->copy()->subMonth();
            @endphp

            @if($mostrarLembreteEpi)
                <div class="bg-linear-to-r from-blue-700 to-blue-900 rounded-2xl shadow-xl p-5 mb-6 border border-blue-400/30 flex items-center justify-between animate-in slide-in-from-top duration-700 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 text-white/5 transform rotate-12 group-hover:scale-110 transition-transform duration-700 pointer-events-none">
                        <svg class="h-32 w-32" fill="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    </div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="bg-yellow-500 p-3 rounded-2xl shadow-lg ring-4 ring-blue-400/20">
                            <svg class="h-7 w-7 text-blue-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-black text-base tracking-wider uppercase">Relatórios de EPI Pendentes</h3>
                            <p class="text-blue-100 text-sm mt-0.5 opacity-90">O mês de <span class="text-yellow-400 font-bold uppercase">{{ $mesAnterior->translatedFormat('F') }}</span> terminou. É necessário imprimir as fichas mensais para arquivo.</p>
                        </div>
                    </div>
                    <a href="{{ route('epis.imprimir-mensal', ['month' => $mesAnterior->month, 'year' => $mesAnterior->year]) }}" 
                       target="_blank"
                       class="relative z-10 bg-yellow-500 hover:bg-yellow-400 text-blue-950 font-black px-6 py-3 rounded-xl text-xs transition-all active:scale-95 shadow-[0_4px_20px_rgba(234,179,8,0.4)] flex items-center gap-2 uppercase tracking-widest whitespace-nowrap">
                        <span>Gerar PDF Mensal</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            @endif

            {{-- Banner: dia pendente de confirmação --}}
            @if ($diaPendenteConfirmacao)
                @php $vendoODiaPendente = $data === $diaPendenteConfirmacao->fecha->toDateString(); @endphp
                <div class="bg-amber-900/80 border border-amber-400/60 rounded-xl py-4 px-5 mb-4 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-500 p-2 rounded-lg shrink-0">
                            <svg class="h-5 w-5 text-amber-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-amber-200 font-bold text-sm">Dia pendente de confirmação</div>
                            <div class="text-amber-300/80 text-xs mt-0.5">
                                @if($vendoODiaPendente)
                                    Revise as equipas e confirme este dia quando estiver tudo correto.
                                @else
                                    O dia <strong class="text-amber-200">{{ $diaPendenteConfirmacao->fecha->locale('pt')->isoFormat('dddd, D [de] MMMM') }}</strong> ainda não foi confirmado. Veja o dia antes de confirmar.
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($vendoODiaPendente)
                            <button wire:click="abrirModalConfirmacao" type="button"
                                class="bg-amber-500 hover:bg-amber-400 text-amber-950 text-xs font-black px-4 py-2 rounded-lg transition-colors">
                                ✓ Confirmar dia
                            </button>
                        @else
                            <button wire:click="irParaDiaPendente" type="button"
                                class="bg-white/10 hover:bg-white/20 text-amber-200 border border-amber-400/40 text-xs font-bold px-3 py-2 rounded-lg transition-colors">
                                Ver dia →
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between mb-2">
                <h1 class="text-2xl font-bold uppercase text-yellow-500">Centros de Custo (PEP)</h1>
            </div>

            {{-- Banner: copiar do último dia com dados --}}
            @if ($semDados)
                <div class="bg-blue-900/85 border border-blue-400 rounded-xl py-4 px-5 mb-4">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <span class="text-[1.6rem]">📋</span>
                            <div>
                                <p class="text-white font-bold text-sm m-0">Sem atribuições para
                                    este dia.</p>
                                @if ($ultimaDataComDados)
                                    <p class="text-blue-100 text-[0.78rem] font-bold mt-1 mb-0">
                                        Último dia de trabalho com dados:
                                        <span class="text-yellow-300 font-black">
                                            {{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                @else
                                    <p class="text-blue-100 text-[0.78rem] font-bold mt-1 mb-0">Não há dias anteriores
                                        com dados guardados.</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2.5 shrink-0 flex-wrap">
                            @if ($ultimaDataComDados)
                                {{-- Copiar da última data --}}
                                @if($diaPendenteConfirmacao)
                                    <button type="button" disabled
                                        title="Confirme o dia {{ $diaPendenteConfirmacao->fecha->format('d/m/Y') }} antes de importar"
                                        class="bg-yellow-400/30 text-blue-900/50 font-bold py-[9px] px-5 rounded-lg text-[0.85rem] cursor-not-allowed border-none whitespace-nowrap opacity-50">
                                        &#8595; Copiar equipas de
                                        {{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m') }}
                                    </button>
                                @else
                                    <button wire:click="copiarDeUltimaData" type="button"
                                        class="bg-yellow-400 text-blue-900 font-bold py-[9px] px-5 rounded-lg text-[0.85rem] cursor-pointer border-none whitespace-nowrap hover:opacity-85 transition-opacity">
                                        &#8595; Copiar equipas de
                                        {{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m') }}
                                    </button>
                                @endif
                            @endif
                            {{-- Pesquisar outra data --}}
                            <button wire:click="$toggle('showPesquisarData')" type="button"
                                class="bg-transparent text-[#93c5fd] border border-blue-500 py-[9px] px-3.5 rounded-lg text-[0.85rem] cursor-pointer whitespace-nowrap hover:bg-blue-500/15 transition-colors">
                                🔍 Pesquisar data
                            </button>
                            @if($diaPendenteConfirmacao)
                                <button type="button" disabled
                                    title="Confirme o dia {{ $diaPendenteConfirmacao->fecha->format('d/m/Y') }} antes de continuar"
                                    class="bg-transparent text-[#93c5fd]/40 border border-blue-500/30 py-[9px] px-4 rounded-lg text-[0.85rem] cursor-not-allowed whitespace-nowrap opacity-50">
                                    Começar em branco
                                </button>
                            @else
                                <button wire:click="descartarBannerCopia" type="button"
                                    class="bg-transparent text-[#93c5fd] border border-blue-500 py-[9px] px-4 rounded-lg text-[0.85rem] cursor-pointer whitespace-nowrap hover:bg-blue-500/15 transition-colors">
                                    Começar em branco
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Pesquisador de data histórica --}}
                    @if ($showPesquisarData)
                        <div class="mt-3.5 pt-3.5 border-t border-blue-400/40 flex items-center gap-2.5 flex-wrap">
                            <span class="text-blue-200 text-[0.82rem] font-semibold">Copiar de outra data:</span>
                            <input type="date" wire:model="pesquisarDataInput"
                                class="bg-blue-900 text-white border border-blue-500 py-1.5 px-2.5 rounded-md text-[0.83rem]"
                                style="color-scheme:dark;">
                            <button wire:click="pesquisarDiaHistorico" type="button"
                                class="bg-blue-500 text-white font-bold py-1.5 px-3.5 rounded-md text-[0.82rem] cursor-pointer border-none hover:bg-blue-600 transition-colors">
                                Pesquisar
                            </button>
                            @if ($erroPesquisarData)
                                <span class="text-red-300 text-sm">&#9888; {{ $erroPesquisarData }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
            {{-- Fim Banner --}}

            {{-- Barra de ações do dia (toggle TV + eliminar) --}}
            @if ($diaAtualTemDados)
                <div class="mb-3 flex items-center justify-between gap-2 flex-wrap">
                    {{-- Toggle: Pronto para TV --}}
                    @php $bloqueadoPorConfirmacao = $diaPendenteConfirmacao && !$prontoParaTv; @endphp
                    <button wire:click="toggleProntoParaTv" type="button"
                        @if($bloqueadoPorConfirmacao) disabled title="Confirme o dia {{ $diaPendenteConfirmacao->fecha->format('d/m/Y') }} antes de publicar um novo dia" @else title="{{ $prontoParaTv ? 'Clique para remover do painel TV' : 'Clique para marcar como pronto e publicar na TV' }}" @endif
                        class="flex items-center gap-2 py-1.5 px-4 rounded-lg text-[0.82rem] font-bold border-2 transition-all duration-150 {{ $bloqueadoPorConfirmacao ? 'border-amber-400/50 bg-amber-950/30 text-amber-600/60 cursor-not-allowed opacity-60' : ($prontoParaTv ? 'border-green-600 bg-green-100 text-green-800 cursor-pointer' : 'border-gray-300 bg-gray-50 text-gray-500 cursor-pointer') }}">
                        <span
                            class="inline-block w-8 h-4.5 rounded-full relative transition-colors duration-200 {{ $prontoParaTv ? 'bg-green-600' : 'bg-gray-300' }}">
                            <span
                                class="absolute top-0.5 w-3.5 h-3.5 rounded-full bg-white transition-all duration-200 {{ $prontoParaTv ? 'right-0.5' : 'left-0.5' }}"></span>
                        </span>
                        {{ $prontoParaTv ? '📺 Pronto para TV' : '📺 Publicar na TV' }}
                    </button>

                    {{-- Erro eliminar + botão eliminar --}}
                    <div class="flex items-center gap-2">
                        @if ($erroEliminar)
                            <span class="text-red-600 text-[0.78rem]">&#9888; {{ $erroEliminar }}</span>
                            <button wire:click="$set('erroEliminar',null)" type="button"
                                class="text-gray-400 text-[0.78rem] cursor-pointer bg-transparent border-none hover:text-gray-600 transition-colors">&#10005;</button>
                        @endif
                        <button wire:click="$set('confirmarEliminar',true)" type="button"
                            class="bg-red-100 text-red-800 border border-red-300 py-1 px-3.5 rounded-md text-[0.78rem] font-bold cursor-pointer hover:bg-red-200 transition-colors">
                            🗑 Apagar este dia
                        </button>
                    </div>
                </div>
            @endif
            {{-- Fim barra de ações --}}

            {{-- Toolbar: Ordenar y Filtrar --}}
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-3 mb-4 flex flex-wrap gap-3 items-center">
                <span
                    class="text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Ordenar:</span>

                <select wire:model.live="sortBy"
                    class="bg-white text-gray-900 font-bold border border-gray-400 py-1 px-2.5 rounded-md text-sm">
                    <option value="predefinida">Predefinida</option>
                    <option value="nombre">Nome</option>
                    <option value="localizacao">Localização</option>
                    <option value="tipo_trabalho">Tipo de Trabalho</option>
                </select>

                @if ($sortBy !== 'predefinida')
                    <button wire:click="toggleSortDir" type="button"
                        title="{{ $sortDir === 'asc' ? 'Ascendente — clique para inverter' : 'Descendente — clique para inverter' }}"
                        class="bg-white text-gray-900 border border-gray-400 py-1 px-3 rounded-md text-sm font-black cursor-pointer hover:bg-gray-50 transition-colors">
                        {{ $sortDir === 'asc' ? '↑ A–Z' : '↓ Z–A' }}
                    </button>
                @endif

                <div class="w-px h-5 bg-gray-300 shrink-0"></div>

                <span
                    class="text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Filtrar:</span>

                <select wire:model.live="filterLocalizacao"
                    class="bg-white text-gray-900 font-bold border border-gray-400 py-1 px-2.5 rounded-md text-sm">
                    <option value="">Todas as localizações</option>
                    @foreach ($localizacoes as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTipo"
                    class="bg-white text-gray-900 font-bold border border-gray-400 py-1 px-2.5 rounded-md text-sm">
                    <option value="">Todos os tipos de trabalho</option>
                    @foreach ($tiposTrabalho as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>

                @if ($filterLocalizacao !== '' || $filterTipo !== '' || $sortBy !== 'predefinida' || $sortDir !== 'asc')
                    <button wire:click="clearFilters" type="button"
                        class="bg-red-100 text-red-900 border border-red-400 py-1 px-3 rounded-md text-xs font-black cursor-pointer hover:bg-red-200 transition-colors">
                        ✕ Limpar
                    </button>
                @endif

                <span class="ml-auto text-xs text-gray-700 font-bold whitespace-nowrap">
                    {{ $peps->count() }} PEP{{ $peps->count() !== 1 ? 's' : '' }}
                </span>
            </div>
            {{-- Fim Toolbar --}}

            <div class="grid gap-3 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1">
                @forelse ($peps as $pep)
                    <div wire:key="pep-card-{{ $pep->id }}"
                        class="bg-blue-950/60 rounded-lg border border-white/10 overflow-hidden flex flex-col">
                        <div class="px-4 py-3 bg-blue-800 border-b border-blue-900">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-semibold text-white">{{ $pep->nombre }}</span>
                                <span class="ml-auto px-2 py-0.5 rounded text-white text-xs font-bold truncate max-w-30"
                                    style="background-color: {{ $pep->tipoTrabalho->color ?? '#ca8a04' }};">
                                    {{ $pep->tipoTrabalho->nombre ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <div class="text-xs text-blue-200">{{ $pep->localizacao->nombre ?? 'N/A' }}</div>
                                <button wire:click="abrirModalNotas({{ $pep->id }})" class="text-xs text-yellow-400 hover:text-yellow-300 font-bold flex items-center gap-1 cursor-pointer transition-colors bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded border border-yellow-400/30">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    {{ isset($pepData[$pep->id]['notas']) && $pepData[$pep->id]['notas'] ? 'Editar Notas' : 'Adicionar Notas' }}
                                </button>
                            </div>
                        </div>
                        <div class="px-4 pt-3 pb-4 flex flex-col flex-1">

                            @if(isset($pepData[$pep->id]['notas']) && $pepData[$pep->id]['notas'])
                            <div class="mb-3 bg-yellow-50 border border-yellow-200 rounded-md p-2 text-xs text-yellow-800 shadow-sm relative pr-6">
                                <strong class="uppercase text-[0.6rem] text-yellow-600 block mb-0.5">Nota / Tarefa do Dia:</strong>
                                {{ $pepData[$pep->id]['notas'] }}
                            </div>
                            @endif

                            <div class="flex-1" x-data="{ openAuxiliar: false }">
                                {{-- EQUIPO PRINCIPAL --}}
                                <div class="mb-4">
                                    <h4 class="text-xs font-semibold text-white/40 mb-1 uppercase tracking-wider">Equipa
                                        Principal</h4>

                                    {{-- Jefe Principal --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-colaboradores-list min-h-9 mb-1.5 p-1 border border-dashed border-blue-400/30 rounded-md bg-blue-400/5 flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="principal"
                                            data-es-jefe="true">
                                            @if (isset($pepData[$pep->id]['principal']['jefes']) && count($pepData[$pep->id]['principal']['jefes']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['jefes'] as $colaborador)
                                                    <div wire:key="jefe-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        class="px-2 py-1 border border-blue-400/40 rounded bg-blue-400/10 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-bold text-[0.6rem] text-blue-300 pb-0.5 mb-0.5">
                                                            👑 Chefe</div>
                                                        <div class="font-medium text-xs text-white/85 truncate">
                                                            {{ $colaborador->numero_colaborador }} {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div
                                                    class="text-center text-xs text-blue-700 py-1 ptr-placeholder font-bold">
                                                    Chefe (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Colaboradores Principales --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-colaboradores-list min-h-9 mb-1.5 p-1 border border-dashed border-white/15 rounded-md bg-white/3 flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="principal"
                                            data-es-jefe="false">
                                            @if (isset($pepData[$pep->id]['principal']['colaboradores']) &&
                                                    count($pepData[$pep->id]['principal']['colaboradores']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['colaboradores'] as $colaborador)
                                                    <div wire:key="col-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        class="px-2 py-1 border border-white/10 rounded bg-white/5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-medium text-xs text-white/85 truncate">
                                                            {{ $colaborador->numero_colaborador }} {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-xs text-gray-600 py-2 ptr-placeholder font-bold">
                                                    Pessoal (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Vehículos Principales --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-veiculos-list min-h-9 p-1 border border-dashed border-white/15 rounded-md bg-white/3 flex flex-col gap-1"
                                            data-list-type="veiculo" data-equipo-tipo="principal">
                                            @if (isset($pepData[$pep->id]['principal']['veiculos']) && count($pepData[$pep->id]['principal']['veiculos']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['veiculos'] as $veiculo)
                                                    <div wire:key="veic-{{ $pep->id }}-{{ $veiculo->id }}"
                                                        class="bg-white/8 border border-purple-400/30 text-purple-300 rounded-md px-2 py-1 text-xs font-bold cursor-grab select-none draggable-item"
                                                        data-id="{{ $veiculo->id }}" data-type="veiculo">
                                                        <div class="truncate">{{ $veiculo->matricula }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-xs text-gray-600 py-1 ptr-placeholder font-bold">
                                                    Veículos (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- BOTÓN MOSTRAR/OCULTAR EQUIPO AUXILIAR --}}
                                <div class="border-t border-gray-100 pt-3 flex justify-between items-center text-sm">
                                    <span class="text-xs font-semibold text-white/40 uppercase tracking-wider">Equipa
                                        Auxiliar</span>
                                    <button @click="openAuxiliar = !openAuxiliar" type="button"
                                        class="text-white/60 hover:text-yellow-400 font-bold bg-white/5 px-2 py-0.5 rounded text-xs transition-colors">
                                        <span x-show="!openAuxiliar">+ Adicionar</span>
                                        <span x-show="openAuxiliar">- Ocultar</span>
                                    </button>
                                </div>

                                {{-- EQUIPO AUXILIAR (OCULTO POR DEFECTO PERO CON DATOS CARGADOS) --}}
                                <div x-show="openAuxiliar || {{ isset($pepData[$pep->id]['auxiliar']) && count($pepData[$pep->id]['auxiliar']) > 0 ? 'true' : 'false' }}"
                                    class="mt-3 bg-yellow-400/5 p-2 rounded border border-yellow-400/15"
                                    style="display:none;">

                                    {{-- Jefe Auxiliar --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-colaboradores-list min-h-9 mb-1.5 p-1 border border-dashed border-yellow-400/30 rounded bg-yellow-400/5 flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="auxiliar"
                                            data-es-jefe="true">
                                            @if (isset($pepData[$pep->id]['auxiliar']['jefes']) && count($pepData[$pep->id]['auxiliar']['jefes']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['jefes'] as $colaborador)
                                                    <div wire:key="jefe-aux-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        class="px-2 py-1 border border-yellow-400/40 rounded bg-yellow-400/8 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-bold text-[0.6rem] text-yellow-400/70 pb-0.5 mb-0.5">
                                                            ⭐ Líder Aux.</div>
                                                        <div class="font-medium text-xs text-white/85 truncate">
                                                            {{ $colaborador->numero_colaborador }} {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div
                                                    class="text-center text-xs text-yellow-600/60 py-1 ptr-placeholder font-medium">
                                                    Líder Aux. (Arrastar)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Colaboradores Auxiliares --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-colaboradores-list min-h-9 mb-1.5 p-1 border border-dashed border-white/15 rounded bg-white/3 flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="auxiliar"
                                            data-es-jefe="false">
                                            @if (isset($pepData[$pep->id]['auxiliar']['colaboradores']) &&
                                                    count($pepData[$pep->id]['auxiliar']['colaboradores']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['colaboradores'] as $colaborador)
                                                    <div wire:key="col-aux-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        class="px-2 py-1 border border-white/10 rounded bg-white/5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-medium text-xs text-white/85 truncate">
                                                            {{ $colaborador->numero_colaborador }} {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-xs text-gray-400 py-2 ptr-placeholder">
                                                    Pessoal Auxiliar (Arrastar)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Veículos Auxiliares --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div class="pep-veiculos-list min-h-9 p-1 border border-dashed border-white/15 rounded bg-white/3 flex flex-col gap-1"
                                            data-list-type="veiculo" data-equipo-tipo="auxiliar">
                                            @if (isset($pepData[$pep->id]['auxiliar']['veiculos']) && count($pepData[$pep->id]['auxiliar']['veiculos']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['veiculos'] as $veiculo)
                                                    <div wire:key="veic-aux-{{ $pep->id }}-{{ $veiculo->id }}"
                                                        class="bg-white/8 border border-purple-400/30 text-purple-300 rounded-md px-2 py-1 text-xs font-bold cursor-grab select-none draggable-item"
                                                        data-id="{{ $veiculo->id }}" data-type="veiculo">
                                                        <div class="truncate">{{ $veiculo->matricula }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-xs text-gray-400 py-1 ptr-placeholder">
                                                    Veículos Aux. (Arrastar)</div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>{{-- /px-4 inner --}}
                    </div>
                @empty
                    <div class="col-span-full p-4 text-center text-gray-500">
                        Nenhum PEP registado na base de dados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    {{-- FIM Painel Esquerdo --}}

    <!-- Panel Derecho: Estaleiro y Recursos Disponibles -->
    <div class="w-full lg:w-80 lg:shrink-0 sticky top-4 flex flex-col gap-4">

        <div class="bg-blue-950/60 rounded-lg border border-white/10 p-3 flex flex-col gap-3 drop-zone"
            data-pep-id="estaleiro" data-type="estaleiro">
            <div class="flex flex-col gap-1 pb-2 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-white/80 leading-tight">Estaleiro</h2>
                        <span class="text-xs text-white/50 font-bold">Localização: <span
                                class="font-black text-white/90">{{ $pepEstaleiro?->localizacao->nombre ?? 'Funchal' }}</span></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="irDiaAnterior" type="button" title="Dia de trabalho anterior"
                            class="bg-white/8 hover:bg-white/15 border border-white/15 text-white/70 py-1.5 px-2.5 rounded-md text-[0.85rem] cursor-pointer leading-none transition-colors">&#8249;</button>
                        <input type="date" wire:model.live="data"
                            class="text-sm border-white/20 rounded-md shadow-sm focus:ring-yellow-400 focus:border-yellow-400 text-white/85 bg-white/8"
                            style="color-scheme:dark; background:rgba(255,255,255,0.08);">
                        <button wire:click="irDiaSeguinte" type="button" title="Dia de trabalho seguinte"
                            class="bg-white/8 hover:bg-white/15 border border-white/15 text-white/70 py-1.5 px-2.5 rounded-md text-[0.85rem] cursor-pointer leading-none transition-colors">&#8250;</button>
                    </div>
                </div>
            </div>

            {{-- Buscar en Estaleiro --}}
            <div class="relative flex items-center">
                <svg class="absolute left-2.5 text-gray-400 pointer-events-none h-4 w-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="searchEstaleiro" type="search"
                    placeholder="Pesquisar colaborador ou veículo..."
                    class="bg-gray-50 text-gray-800 border border-gray-300 py-1.5 pr-2.5 pl-8 rounded-lg text-[0.8rem] w-full focus:outline-none focus:ring-1 focus:ring-blue-500">
                @if ($searchEstaleiro)
                    <button wire:click="$set('searchEstaleiro','')" type="button"
                        class="absolute right-2 text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none text-base">&#10005;</button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Columna de Colaboradores -->
                <div>
                    <h3 class="font-semibold text-(--cme-blue) mb-2 bg-gray-50 p-2 rounded text-center">
                        Colaboradores</h3>
                    <div id="colaboradores-list"
                        class="flex flex-col gap-2 min-h-[300px] max-h-[55vh] overflow-y-auto pr-1 pb-10 bg-white/3 rounded"
                        data-list-type="colaborador">
                        @forelse ($colaboradores_libres as $colaborador)
                            <div wire:key="libre-col-{{ $colaborador->id }}"
                                class="px-2 py-1 border border-white/8 rounded bg-white/5 cursor-grab select-none hover:border-yellow-400/50 hover:bg-yellow-400/5 transition-colors draggable-item flex items-center gap-2 min-w-0"
                                data-id="{{ $colaborador->id }}" data-type="colaborador">
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <p class="text-[0.7rem] text-white/85 font-medium truncate leading-tight">
                                        {{ $colaborador->numero_colaborador }} {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                                    </p>
                                    <p class="text-[0.58rem] text-white/40 uppercase truncate">
                                        {{ $colaborador->denominacion_cargo }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-gray-400 italic py-4 empty-msg">Nenhum colaborador
                                disponível</div>
                        @endforelse
                    </div>
                </div>

                <!-- Columna de Veículos -->
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2 bg-gray-50 p-2 rounded text-center">Veículos
                    </h3>
                    <div id="veiculos-list"
                        class="flex flex-col gap-2 min-h-[300px] max-h-[55vh] overflow-y-auto pr-1 pb-10 bg-white/3 rounded"
                        data-list-type="veiculo">
                        @forelse ($veiculos_libres as $veiculo)
                            <div wire:key="libre-veic-{{ $veiculo->id }}"
                                class="px-2 py-1 border border-white/8 rounded bg-white/5 cursor-grab select-none hover:border-yellow-400/50 hover:bg-yellow-400/5 transition-colors draggable-item flex items-center gap-2 min-w-0"
                                data-id="{{ $veiculo->id }}" data-type="veiculo">
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <p class="text-[0.7rem] text-white/85 font-bold truncate leading-tight">
                                        {{ $veiculo->matricula }}
                                    </p>
                                    <p class="text-[0.58rem] text-white/40 uppercase truncate">
                                        {{ $veiculo->marca }} {{ $veiculo->modelo }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-gray-400 italic py-4 empty-msg">Nenhum veículo
                                disponível</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ZONA BAJAS / LICENCIAS / VACACIONES (3 cards) ===== --}}
        <div class="mt-4 flex flex-col gap-2">

            {{-- Bajas --}}
            <div class="bg-red-50 rounded-xl border border-red-200 shadow p-3 min-h-[140px] flex flex-col drop-zone"
                data-pep-id="baixa" data-type="estado">
                <span class="font-semibold text-red-700 mb-2 text-center text-xs uppercase tracking-wide">Baixas</span>
                <div class="pep-colaboradores-list w-full flex-1 min-h-[60px] p-1.5 border border-dashed border-red-300 rounded bg-white flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['baixa'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="baja-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                class="p-1.5 border border-red-200 rounded shadow-sm bg-red-100 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div class="font-medium text-xs text-red-800 leading-tight">
                                    {{ $col->numero_colaborador }} – {{ $col->apellido }}, {{ $col->nombre }}</div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-xs text-red-600 font-black py-2 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Licenças --}}
            <div class="bg-amber-50 rounded-xl border border-amber-200 shadow p-3 min-h-[140px] flex flex-col drop-zone"
                data-pep-id="licenca" data-type="estado">
                <span
                    class="font-semibold text-amber-700 mb-2 text-center text-xs uppercase tracking-wide">Licenças</span>
                <div class="pep-colaboradores-list w-full flex-1 min-h-[60px] p-1.5 border border-dashed border-amber-300 rounded bg-white flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['licenca'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="licenca-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                class="p-1.5 border border-amber-200 rounded shadow-sm bg-amber-100 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div class="font-medium text-xs text-amber-800 leading-tight">
                                    {{ $col->numero_colaborador }} – {{ $col->apellido }}, {{ $col->nombre }}</div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-xs text-amber-700 font-black py-2 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Vacaciones --}}
            <div class="bg-green-50 rounded-xl border border-green-200 shadow p-3 min-h-[140px] flex flex-col drop-zone"
                data-pep-id="ferias" data-type="estado">
                <span
                    class="font-semibold text-green-700 mb-2 text-center text-xs uppercase tracking-wide">Férias</span>
                <div class="pep-colaboradores-list w-full flex-1 min-h-[60px] p-1.5 border border-dashed border-green-300 rounded bg-white flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['ferias'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="ferias-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                class="p-1.5 border border-green-200 rounded shadow-sm bg-green-100 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div class="font-medium text-xs text-green-800 leading-tight">
                                    {{ $col->numero_colaborador }} – {{ $col->apellido }}, {{ $col->nombre }}</div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-xs text-green-700 font-black py-2 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ===== CONSULTAS MÉDICAS / FORMACIÓN (apiladas) ===== --}}
        <div class="mt-2 flex flex-col gap-2">

            {{-- Consultas Médicas --}}
            <div class="bg-teal-50 rounded-xl border border-teal-200 shadow p-3 min-h-[140px] flex flex-col drop-zone"
                data-pep-id="consulta_medica" data-type="estado">
                <span class="font-semibold text-teal-700 mb-2 text-center text-xs uppercase tracking-wide">🩺 Consultas
                    Médicas</span>
                <div class="pep-colaboradores-list w-full flex-1 min-h-[60px] p-1.5 border border-dashed border-teal-300 rounded bg-white flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['consulta_medica'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="consulta-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                class="p-1.5 border border-teal-200 rounded shadow-sm bg-teal-100 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div class="font-medium text-xs text-teal-800 leading-tight">
                                    {{ $col->numero_colaborador }} – {{ $col->apellido }}, {{ $col->nombre }}</div>
                                @if ($entry['fecha_hora_evento'])
                                    <div class="text-xs text-teal-600 mt-0.5">📅
                                        {{ \Carbon\Carbon::parse($entry['fecha_hora_evento'])->format('d/m H:i') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-xs text-teal-300 py-2 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Formación --}}
            <div class="bg-purple-50 rounded-xl border border-purple-200 shadow p-3 min-h-[140px] flex flex-col drop-zone"
                data-pep-id="formacao" data-type="estado">
                <span class="font-semibold text-purple-700 mb-2 text-center text-xs uppercase tracking-wide">📚
                    Formação</span>
                <div class="pep-colaboradores-list w-full flex-1 min-h-[60px] p-1.5 border border-dashed border-purple-300 rounded bg-white flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['formacao'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="formacao-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                class="p-1.5 border border-purple-200 rounded shadow-sm bg-purple-100 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div class="font-medium text-xs text-purple-800 leading-tight">
                                    {{ $col->numero_colaborador }} – {{ $col->apellido }}, {{ $col->nombre }}
                                </div>
                                @if ($entry['fecha_hora_evento'])
                                    <div class="text-xs text-purple-600 mt-0.5">📅
                                        {{ \Carbon\Carbon::parse($entry['fecha_hora_evento'])->format('d/m H:i') }}
                                    </div>
                                @endif
                                @if ($entry['descripcion_evento'])
                                    <div class="text-xs text-purple-500 italic mt-0.5">
                                        {{ $entry['descripcion_evento'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-xs text-purple-300 py-2 ptr-placeholder italic">(Largar aqui)
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ===== EQUIPOS EN REPARACIÓN ===== --}}
        <div class="mt-2 bg-orange-50 rounded-xl border border-orange-200 shadow p-3 flex flex-col drop-zone"
            data-pep-id="reparacao" data-type="estado">
            <span class="font-semibold text-orange-700 mb-2 text-center text-xs uppercase tracking-wide">🔧
                Equipamentos em Reparação</span>
            <div class="pep-veiculos-list min-h-[60px] p-1.5 border border-dashed border-orange-300 rounded bg-white flex flex-wrap gap-2"
                data-list-type="veiculo">
                @forelse($estadoData['reparacao'] ?? [] as $entry)
                    @foreach ($entry['veiculos'] as $veh)
                        <div wire:key="reparacao-{{ $entry['atrib_id'] }}-{{ $veh->id }}"
                            class="p-2 border border-orange-200 rounded shadow-sm bg-orange-100 cursor-grab select-none draggable-item min-w-[130px]"
                            data-id="{{ $veh->id }}" data-type="veiculo">
                            <div class="font-bold text-xs text-orange-900">{{ $veh->matricula }}</div>
                            @if ($entry['fecha_entrada_taller'])
                                <div class="text-xs text-orange-600 mt-0.5">Entrada:
                                    {{ \Carbon\Carbon::parse($entry['fecha_entrada_taller'])->format('d/m/Y') }}</div>
                            @endif
                            @if ($entry['nombre_taller'])
                                <div class="text-xs text-orange-500 italic mt-0.5">{{ $entry['nombre_taller'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @empty
                    <div class="text-center text-xs text-orange-300 py-2 ptr-placeholder italic w-full">(Arrastar
                        veículos / ferramentas aqui)</div>
                @endforelse
            </div>
        </div>

    </div>
    {{-- FIM Painel Direito --}}

    {{-- ===== MODAL PARA DATOS EXTRA DE EVENTO ===== --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:key="modal-evento">
            <div class="bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.35)] p-7 w-full max-w-[420px] mx-4">

                @if ($modalTipo === 'notas')
                    <h3 class="text-base font-extrabold text-blue-800 m-0 mb-5">📝 Instruções / Notas Diárias</h3>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Tarefa, presupuesto ou observação para a equipa</label>
                        <textarea wire:model="modalNotas" rows="4"
                            placeholder="Descreva a tarefa específica, orçamento alocado ou qualquer aviso para a equipa hoje..."
                            class="w-full border-[1.5px] border-blue-200 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        ></textarea>
                    </div>
                @elseif ($modalTipo === 'consulta_medica')
                    <h3 class="text-base font-extrabold text-teal-700 m-0 mb-5">🩺 Dados da Consulta
                        Médica</h3>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Data
                            e hora da consulta</label>
                        <input type="datetime-local" wire:model="modalDataHora"
                            class="w-full border-[1.5px] border-teal-200 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-teal-50 focus:outline-none focus:ring-2 focus:ring-teal-400"
                            style="color-scheme:light;" />
                    </div>
                @elseif($modalTipo === 'formacion')
                    <h3 class="text-base font-extrabold text-purple-700 m-0 mb-5">📚 Dados da Formação</h3>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Data
                            e hora</label>
                        <input type="datetime-local" wire:model="modalDataHora"
                            class="w-full border-[1.5px] border-purple-300 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-400"
                            style="color-scheme:light;" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Descrição breve</label>
                        <input type="text" wire:model="modalDescricao" maxlength="255"
                            placeholder="Ex: Formação em altura — Módulo 2"
                            class="w-full border-[1.5px] border-purple-300 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-400" />
                    </div>
                @elseif($modalTipo === 'reparacion')
                    <h3 class="text-base font-extrabold text-orange-700 m-0 mb-5">🔧 Dados de Reparação</h3>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Nome da oficina</label>
                        <input type="text" wire:model="modalNomeOficina" maxlength="150"
                            placeholder="Ex: Oficina Central Estaleiro"
                            class="w-full border-[1.5px] border-orange-200 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-[0.82rem] font-semibold text-gray-700 mb-1.5">Data de entrada na
                            oficina</label>
                        <input type="date" wire:model="modalDataEntradaOficina"
                            class="w-full border-[1.5px] border-orange-200 rounded-lg py-2.5 px-3 text-[0.9rem] text-gray-900 bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400"
                            style="color-scheme:light;" />
                    </div>
                @endif

                <div class="flex justify-end gap-2.5 mt-2">
                    <button wire:click="fecharModalEvento"
                        class="py-2 px-5 text-[0.88rem] font-semibold rounded-lg border-[1.5px] border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                        Ignorar
                    </button>
                    <button wire:click="guardarMetadadosEvento"
                        class="py-2 px-5 text-[0.88rem] font-bold rounded-lg border-none bg-slate-800 text-white hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 transition-colors">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ===== MODAL CONFIRMAR ELIMINAÇÃO DE DIA ===== --}}
    @if ($confirmarEliminar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/65" wire:key="modal-eliminar">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                {{-- Cabeçalho vermelho --}}
                <div class="bg-red-600 px-6 pt-5 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-[1.8rem]">⚠️</span>
                        <div>
                            <h3 class="text-white text-base font-extrabold m-0">Apagar dia de trabalho
                            </h3>
                            <p class="text-red-200 text-[0.82rem] m-0 mt-1">
                                {{ \Carbon\Carbon::parse($data)->format('l, d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                {{-- Cuerpo --}}
                <div class="p-6">
                    <p class="text-gray-900 text-[0.92rem] font-semibold m-0 mb-2.5">Tem a certeza de que
                        quer apagar todas as atribuições deste dia?</p>
                    <div class="bg-red-50 border border-red-300 rounded-lg py-3 px-3.5 mb-5">
                        <p class="text-red-800 text-[0.82rem] font-bold m-0 mb-1.5">⚠ Esta ação é
                            irreversível:</p>
                        <ul class="text-red-700 text-[0.8rem] m-0 pl-4 leading-relaxed list-disc">
                            <li>Serão eliminadas todas as atribuições de colaboradores e veículos.</li>
                            <li>Serão perdidos os dados de baixas, licenças, consultas, formação e reparações.</li>
                            <li><strong>Não é possível recuperar estes dados.</strong></li>
                        </ul>
                    </div>
                    @if ($erroEliminar)
                        <div
                            class="bg-red-50 border border-red-300 rounded-lg py-2.5 px-3.5 mb-4 text-red-800 text-[0.82rem] font-semibold">
                            ⚠ {{ $erroEliminar }}
                        </div>
                    @endif
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('confirmarEliminar',false)" type="button"
                            class="bg-gray-100 text-gray-700 border border-gray-300 py-2.5 px-5 rounded-lg text-[0.88rem] font-bold cursor-pointer hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="eliminarDia" type="button"
                            class="bg-red-600 text-white py-2.5 px-5 rounded-lg text-[0.88rem] font-bold cursor-pointer border-none hover:bg-red-700 transition-colors">
                            Sim, apagar definitivamente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== MODAL CONFIRMAR DIA ===== --}}
    @if ($modalConfirmacaoOpen && $diaPendenteConfirmacao)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/65" wire:key="modal-confirmacao">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                <div class="bg-amber-500 px-6 pt-5 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-950/20 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-amber-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-amber-950 text-base font-extrabold m-0">Confirmar dia de trabalho</h3>
                            <p class="text-amber-900 text-[0.82rem] m-0 mt-0.5">
                                {{ $diaPendenteConfirmacao->fecha->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 text-sm mb-4">
                        Ao confirmar, declara que os equipamentos, colaboradores e veículos registados para este dia refletem o que realmente aconteceu.
                    </p>

                    @if ($erroConfirmacao)
                        <div class="bg-red-50 border border-red-300 rounded-lg py-2.5 px-3.5 mb-4 text-red-800 text-sm font-semibold">
                            {{ $erroConfirmacao }}
                        </div>
                    @endif

                    {{-- Assinatura guardada --}}
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Assinatura do Responsável</label>
                        @if(Auth::user()->signature)
                            <div class="border border-gray-200 rounded-xl bg-gray-50 flex items-center justify-center p-3" style="min-height:100px;">
                                <img src="{{ Auth::user()->signature }}" alt="Assinatura" style="max-height:80px;max-width:100%;object-fit:contain;">
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">{{ Auth::user()->name }}</p>
                        @else
                            <div class="bg-orange-50 border border-orange-300 rounded-xl py-3 px-4 flex items-start gap-3">
                                <span class="text-orange-500 text-lg leading-none mt-0.5">⚠</span>
                                <div>
                                    <p class="text-orange-800 text-sm font-semibold m-0">Não tem assinatura guardada.</p>
                                    <p class="text-orange-700 text-xs mt-1 m-0">
                                        Para confirmar dias de trabalho precisa de criar a sua assinatura primeiro.
                                        <a href="{{ route('utilizadores.index') }}" class="font-bold underline">Ir para Utilizadores →</a>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3">
                        <button wire:click="fecharModalConfirmacao" type="button"
                            class="bg-gray-100 text-gray-700 border border-gray-300 py-2.5 px-5 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        @if(Auth::user()->signature)
                            <button wire:click="confirmarDia" type="button"
                                class="bg-amber-500 hover:bg-amber-400 text-amber-950 py-2.5 px-6 rounded-lg text-sm font-black transition-colors">
                                Confirmar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
