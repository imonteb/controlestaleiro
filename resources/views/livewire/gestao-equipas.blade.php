<div class="flex flex-col lg:flex-row gap-6 w-full items-start" x-data="dragAndDrop()">

    <!-- Painel Esquerdo: PEPs e Áreas Especiais -->
    <div class="w-full lg:flex-1 lg:min-w-0 flex flex-col gap-6">

        <div class="mb-6">
            @php
                $hoje = \Carbon\Carbon::now();
                // Mostrar o lembrete nos primeiros 5 dias do mês (margem para impressões mensais)
                $mostrarLembreteEpi = $hoje->day <= 5;
                $mesAnterior = $hoje->copy()->subMonth();
            @endphp

            @if($mostrarLembreteEpi)
                <div x-data="{ visible: true }" x-show="visible" class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4 relative">
                    <div class="bg-[#09143B] px-4 py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="exclamation-triangle" class="text-[#FFD300] w-4 h-4" />
                            <span class="text-white font-medium text-sm">Relatórios de EPI Pendentes</span>
                        </div>
                        <button @click="visible = false" type="button" title="Fechar aviso"
                                class="text-white/40 hover:text-white/80 transition-colors bg-transparent border-none cursor-pointer p-1 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="bg-[#EEECEA] px-4 py-3 flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-[12px] text-[#4A4845] m-0">
                            O mês de <span class="text-[#09143B] font-bold uppercase">{{ $mesAnterior->translatedFormat('F') }}</span> terminou. É necessário imprimir as fichas mensais para arquivo.
                        </p>
                        <a href="{{ route('epis.imprimir-mensal', ['month' => $mesAnterior->month, 'year' => $mesAnterior->year]) }}"
                           target="_blank"
                           class="shrink-0 flex items-center gap-1.5 text-[11px] font-bold px-4 py-1.5 rounded-lg transition-opacity hover:opacity-85"
                           style="background:#09143B; color:#FFD300;">
                            Gerar PDF Mensal
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Banner: dia pendente de confirmação --}}
            @if ($diaPendenteConfirmacao)
                @php $vendoODiaPendente = $data === $diaPendenteConfirmacao->fecha->toDateString(); @endphp
                <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4">
                    <div class="bg-[#09143B] px-4 py-2.5 flex items-center gap-2">
                        <flux:icon name="check-circle" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-sm">Dia pendente de confirmação</span>
                    </div>
                    <div class="bg-[#EEECEA] px-4 py-3 flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-[12px] text-[#4A4845] m-0">
                            @if($vendoODiaPendente)
                                Revise as equipas e confirme este dia quando estiver tudo correto.
                            @else
                                O dia <strong style="color:#09143B;">{{ $diaPendenteConfirmacao->fecha->locale('pt')->isoFormat('dddd, D [de] MMMM') }}</strong> ainda não foi confirmado. Veja o dia antes de confirmar.
                            @endif
                        </p>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($vendoODiaPendente)
                                <button wire:click="abrirModalConfirmacao" type="button"
                                    class="text-[11px] font-bold px-4 py-1.5 rounded-lg transition-opacity hover:opacity-85"
                                    style="background:#09143B; color:#FFD300;">
                                    ✓ Confirmar dia
                                </button>
                            @else
                                <button wire:click="irParaDiaPendente" type="button"
                                    class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-[rgba(9,20,59,0.20)] transition-colors hover:bg-[#E4E2DF]"
                                    style="background:#EEECEA; color:#09143B;">
                                    Ver dia →
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="rounded-t-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-2">
                <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon name="calendar-days" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-sm">Centros de Custo (PEP)</span>
                    </div>
                </div>
            </div>

            {{-- Banner: copiar do último dia com dados --}}
            @if ($semDados)
                <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4">
                    <div class="bg-[#09143B] px-4 py-2.5 flex items-center gap-2">
                        <flux:icon name="clipboard" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-sm">Sem atribuições para este dia</span>
                    </div>
                    <div style="background:#F0EEEB !important;" class="px-4 py-3">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div>
                                @if ($ultimaDataComDados)
                                    <p class="text-[12px] text-[#4A4845] m-0">
                                        Último dia com dados:
                                        <span class="font-bold" style="color:#09143B;">{{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m/Y') }}</span>
                                    </p>
                                @else
                                    <p class="text-[12px] text-[#7A7775] m-0">Não há dias anteriores com dados guardados.</p>
                                @endif
                            </div>
                            <div class="flex gap-2 shrink-0 flex-wrap">
                                @if ($ultimaDataComDados)
                                    @if($diaPendenteConfirmacao)
                                        <button type="button" disabled
                                            title="Confirme o dia {{ $diaPendenteConfirmacao->fecha->format('d/m/Y') }} antes de importar"
                                            class="text-[11px] font-bold py-1.5 px-4 rounded-lg cursor-not-allowed opacity-40 whitespace-nowrap"
                                            style="background:#09143B; color:#FFD300;">
                                            ↓ Copiar equipas de {{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m') }}
                                        </button>
                                    @else
                                        <button wire:click="copiarDeUltimaData" type="button"
                                            class="text-[11px] font-bold py-1.5 px-4 rounded-lg cursor-pointer whitespace-nowrap hover:opacity-85 transition-opacity"
                                            style="background:#09143B; color:#FFD300;">
                                            ↓ Copiar equipas de {{ \Carbon\Carbon::parse($ultimaDataComDados)->format('d/m') }}
                                        </button>
                                    @endif
                                @endif
                                <button wire:click="$toggle('showPesquisarData')" type="button"
                                    class="text-[11px] font-bold py-1.5 px-3.5 rounded-lg cursor-pointer whitespace-nowrap border border-[rgba(9,20,59,0.20)] hover:bg-[#E4E2DF] transition-colors"
                                    style="background:#EEECEA; color:#09143B;">
                                    Pesquisar data
                                </button>
                                @if($diaPendenteConfirmacao)
                                    <button type="button" disabled
                                        title="Confirme o dia {{ $diaPendenteConfirmacao->fecha->format('d/m/Y') }} antes de continuar"
                                        class="text-[11px] font-bold py-1.5 px-4 rounded-lg cursor-not-allowed whitespace-nowrap opacity-40 border border-[rgba(9,20,59,0.20)]"
                                        style="background:#EEECEA; color:#09143B;">
                                        Começar em branco
                                    </button>
                                @else
                                    <button wire:click="descartarBannerCopia" type="button"
                                        class="text-[11px] font-bold py-1.5 px-4 rounded-lg cursor-pointer whitespace-nowrap border border-[rgba(9,20,59,0.20)] hover:bg-[#E4E2DF] transition-colors"
                                        style="background:#EEECEA; color:#09143B;">
                                        Começar em branco
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($showPesquisarData)
                            <div class="mt-3 pt-3 border-t border-[rgba(9,20,59,0.10)] flex items-center gap-2.5 flex-wrap">
                                <span class="text-[11px] font-semibold text-[#4A4845]">Copiar de outra data:</span>
                                <input type="date" wire:model="pesquisarDataInput"
                                    class="border border-[rgba(9,20,59,0.20)] py-1.5 px-2.5 rounded-md text-[11px] text-[#1A1A1A]"
                                    style="background:#F0EEEB;">
                                <button wire:click="pesquisarDiaHistorico" type="button"
                                    class="text-[11px] font-bold py-1.5 px-3.5 rounded-md cursor-pointer border-none hover:opacity-85 transition-opacity"
                                    style="background:#09143B; color:#FFD300;">
                                    Pesquisar
                                </button>
                                @if ($erroPesquisarData)
                                    <span class="text-[11px]" style="color:#A32D2D;">&#9888; {{ $erroPesquisarData }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
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
            <div style="background:#E4E2DF; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl px-4 py-3 mb-4 flex flex-wrap items-center gap-3">
                <span style="color:#7A7775;" class="text-[10px] uppercase tracking-wide font-medium whitespace-nowrap">Ordenar:</span>

                <select wire:model.live="sortBy"
                    style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18); border-radius:8px; padding:6px 10px; font-size:12px; outline:none; cursor:pointer;">
                    <option value="predefinida">Predefinida</option>
                    <option value="nombre">Nome</option>
                    <option value="localizacao">Localização</option>
                    <option value="tipo_trabalho">Tipo de Trabalho</option>
                </select>

                @if ($sortBy !== 'predefinida')
                    <button wire:click="toggleSortDir" type="button"
                        title="{{ $sortDir === 'asc' ? 'Ascendente — clique para inverter' : 'Descendente — clique para inverter' }}"
                        class="text-[12px] bg-white border border-[rgba(9,20,59,0.18)] text-[#1A1A1A] rounded-lg px-2 py-1.5 outline-none cursor-pointer font-bold hover:bg-[#F0EEEB] transition-colors">
                        {{ $sortDir === 'asc' ? '↑ A–Z' : '↓ Z–A' }}
                    </button>
                @endif

                <div class="w-px h-4 shrink-0" style="background:rgba(9,20,59,0.14);"></div>

                <span style="color:#7A7775;" class="text-[10px] uppercase tracking-wide font-medium whitespace-nowrap">Filtrar:</span>

                <select wire:model.live="filterLocalizacao"
                    style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18); border-radius:8px; padding:6px 10px; font-size:12px; outline:none; cursor:pointer;">
                    <option value="">Todas as localizações</option>
                    @foreach ($localizacoes as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTipo"
                    style="background:white !important; color:#1A1A1A !important; border:1px solid rgba(9,20,59,0.18); border-radius:8px; padding:6px 10px; font-size:12px; outline:none; cursor:pointer;">
                    <option value="">Todos os tipos de trabalho</option>
                    @foreach ($tiposTrabalho as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>

                @if ($filterLocalizacao !== '' || $filterTipo !== '' || $sortBy !== 'predefinida' || $sortDir !== 'asc')
                    <button wire:click="clearFilters" type="button"
                        class="text-[11px] font-bold py-1.5 px-3 rounded-lg cursor-pointer border border-[rgba(9,20,59,0.18)] hover:opacity-85 transition-opacity"
                        style="background:#fde8e8; color:#A32D2D;">
                        ✕ Limpar
                    </button>
                @endif

                <span style="color:#7A7775; margin-left:auto;" class="text-[10px] font-medium whitespace-nowrap">
                    {{ $peps->count() }} PEP{{ $peps->count() !== 1 ? 's' : '' }}
                </span>
            </div>
            {{-- Fim Toolbar --}}

            <div class="grid gap-3 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                @forelse ($peps as $pep)
                    <div wire:key="pep-card-{{ $pep->id }}"
                        style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden flex flex-col">
                        <div style="background:#09143B;" class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span style="color:#FFD300;" class="text-[11px] font-bold">{{ $pep->nombre }}</span>
                                <span class="ml-auto px-2 py-0.5 rounded text-white text-[0.6rem] font-bold truncate max-w-30"
                                    style="background-color: {{ $pep->tipoTrabalho->color ?? '#ca8a04' }};">
                                    {{ $pep->tipoTrabalho->nombre ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <div style="color:rgba(255,255,255,0.6);" class="text-[10px]">{{ $pep->localizacao->nombre ?? 'N/A' }}</div>
                                <button wire:click="abrirModalNotas({{ $pep->id }})" title="{{ isset($pepData[$pep->id]['notas']) && $pepData[$pep->id]['notas'] ? 'Editar Notas' : 'Adicionar Notas' }}" class="text-xs text-yellow-400 hover:text-yellow-300 flex items-center cursor-pointer transition-colors bg-white/10 hover:bg-white/20 px-1.5 py-0.5 rounded border border-yellow-400/30">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                            </div>
                        </div>
                        <div style="background:#F0EEEB;" class="p-2 flex flex-col gap-1.5 flex-1">

                            @if(isset($pepData[$pep->id]['notas']) && $pepData[$pep->id]['notas'])
                            <div class="mb-3 bg-yellow-50 border border-yellow-200 rounded-md p-2 text-xs text-yellow-800 shadow-sm relative pr-6">
                                <strong class="uppercase text-[0.6rem] text-yellow-600 block mb-0.5">Nota / Tarefa do Dia:</strong>
                                {{ $pepData[$pep->id]['notas'] }}
                            </div>
                            @endif

                            <div class="flex-1"
                                wire:key="pep-alpine-{{ $pep->id }}-{{ $data }}"
                                x-data="{ openAuxiliar: false, hasAuxiliar: {{ isset($pepData[$pep->id]['auxiliar']) && count($pepData[$pep->id]['auxiliar']) > 0 ? 'true' : 'false' }} }">
                                {{-- EQUIPO PRINCIPAL --}}
                                <div class="mb-2">
                                    <h4 style="color:rgba(9,20,59,0.40);" class="text-[10px] font-bold uppercase tracking-wide mb-1">Equipa Principal</h4>

                                    {{-- Jefe Principal --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#EEECEA; border:1px dashed rgba(9,20,59,0.20);" class="pep-colaboradores-list min-h-7 mb-1 p-0.5 rounded-md flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="principal"
                                            data-es-jefe="true">
                                            @if (isset($pepData[$pep->id]['principal']['jefes']) && count($pepData[$pep->id]['principal']['jefes']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['jefes'] as $colaborador)
                                                    <div wire:key="jefe-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium flex items-center gap-1.5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div style="color:rgba(255,211,0,0.7);" class="text-[9px] font-bold pb-0.5 mb-0.5">
                                                            👑 Chefe</div>
                                                        <div class="font-medium text-xs text-white truncate">
                                                            @php
                                                            $partsNome = explode(' ', trim($colaborador->nombre));
                                                            $partsApelido = explode(' ', trim($colaborador->apellido));
                                                            $firstName = $partsNome[0] ?? '';
                                                            $stopWords = ['de','da','do','dos','das','e'];
                                                            $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                                            $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                                        @endphp
                                                            {{ $colaborador->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-1">
                                                    Chefe (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Colaboradores Principales --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#EEECEA; border:1px dashed rgba(9,20,59,0.20);" class="pep-colaboradores-list min-h-7 mb-1 p-0.5 rounded-md flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="principal"
                                            data-es-jefe="false">
                                            @if (isset($pepData[$pep->id]['principal']['colaboradores']) &&
                                                    count($pepData[$pep->id]['principal']['colaboradores']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['colaboradores'] as $colaborador)
                                                    <div wire:key="col-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium flex items-center gap-1.5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-medium text-xs text-white truncate">
                                                            @php
                                                            $partsNome = explode(' ', trim($colaborador->nombre));
                                                            $partsApelido = explode(' ', trim($colaborador->apellido));
                                                            $firstName = $partsNome[0] ?? '';
                                                            $stopWords = ['de','da','do','dos','das','e'];
                                                            $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                                            $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                                        @endphp
                                                            {{ $colaborador->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-2">
                                                    Pessoal (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Vehículos Principales --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#EEECEA; border:1px dashed rgba(9,20,59,0.20);" class="pep-veiculos-list min-h-7 p-0.5 rounded-md flex flex-col gap-1"
                                            data-list-type="veiculo" data-equipo-tipo="principal">
                                            @if (isset($pepData[$pep->id]['principal']['veiculos']) && count($pepData[$pep->id]['principal']['veiculos']) > 0)
                                                @foreach ($pepData[$pep->id]['principal']['veiculos'] as $veiculo)
                                                    <div wire:key="veic-{{ $pep->id }}-{{ $veiculo->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium cursor-grab select-none draggable-item"
                                                        data-id="{{ $veiculo->id }}" data-type="veiculo">
                                                        <div class="truncate">{{ $veiculo->matricula }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-1">
                                                    Veículos (Arrastar aqui)</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- BOTÓN MOSTRAR/OCULTAR EQUIPO AUXILIAR --}}
                                <div style="background:#E4E2DF; border-top:1px solid rgba(9,20,59,0.08);" class="px-3 py-1.5 flex items-center justify-between -mx-2 -mb-1.5 mt-1">
                                    <span style="color:#7A7775;" class="text-[9px] uppercase tracking-wide">Equipa Auxiliar</span>
                                    <button @click="openAuxiliar = !openAuxiliar" type="button"
                                        style="color:#09143B;" class="text-[10px] font-bold hover:underline bg-transparent border-none cursor-pointer">
                                        <span x-show="!openAuxiliar">+ Adicionar</span>
                                        <span x-show="openAuxiliar">- Ocultar</span>
                                    </button>
                                </div>

                                {{-- EQUIPO AUXILIAR (OCULTO POR DEFECTO PERO CON DATOS CARGADOS) --}}
                                <div x-show="openAuxiliar || hasAuxiliar"
                                    style="background:#EEECEA; border:1px solid rgba(9,20,59,0.10);" class="mt-2 p-2 rounded-lg">

                                    {{-- Jefe Auxiliar --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#F0EEEB; border:1px dashed rgba(9,20,59,0.20);" class="pep-colaboradores-list min-h-7 mb-1 p-0.5 rounded flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="auxiliar"
                                            data-es-jefe="true">
                                            @if (isset($pepData[$pep->id]['auxiliar']['jefes']) && count($pepData[$pep->id]['auxiliar']['jefes']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['jefes'] as $colaborador)
                                                    <div wire:key="jefe-aux-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium flex items-center gap-1.5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div style="color:rgba(255,211,0,0.7);" class="text-[9px] font-bold pb-0.5 mb-0.5">
                                                            ⭐ Líder Aux.</div>
                                                        <div class="font-medium text-xs text-white truncate">
                                                            @php
                                                            $partsNome = explode(' ', trim($colaborador->nombre));
                                                            $partsApelido = explode(' ', trim($colaborador->apellido));
                                                            $firstName = $partsNome[0] ?? '';
                                                            $stopWords = ['de','da','do','dos','das','e'];
                                                            $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                                            $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                                        @endphp
                                                            {{ $colaborador->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-1">
                                                    Líder Aux. (Arrastar)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Colaboradores Auxiliares --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#F0EEEB; border:1px dashed rgba(9,20,59,0.20);" class="pep-colaboradores-list min-h-7 mb-1 p-0.5 rounded flex flex-col gap-1"
                                            data-list-type="colaborador" data-equipo-tipo="auxiliar"
                                            data-es-jefe="false">
                                            @if (isset($pepData[$pep->id]['auxiliar']['colaboradores']) &&
                                                    count($pepData[$pep->id]['auxiliar']['colaboradores']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['colaboradores'] as $colaborador)
                                                    <div wire:key="col-aux-{{ $pep->id }}-{{ $colaborador->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium flex items-center gap-1.5 cursor-grab select-none draggable-item min-w-0"
                                                        data-id="{{ $colaborador->id }}" data-type="colaborador">
                                                        <div class="font-medium text-xs text-white truncate">
                                                            @php
                                                            $partsNome = explode(' ', trim($colaborador->nombre));
                                                            $partsApelido = explode(' ', trim($colaborador->apellido));
                                                            $firstName = $partsNome[0] ?? '';
                                                            $stopWords = ['de','da','do','dos','das','e'];
                                                            $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                                            $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                                        @endphp
                                                            {{ $colaborador->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-2">
                                                    Pessoal Auxiliar (Arrastar)</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Veículos Auxiliares --}}
                                    <div class="drop-zone" data-pep-id="{{ $pep->id }}" data-type="pep">
                                        <div style="background:#F0EEEB; border:1px dashed rgba(9,20,59,0.20);" class="pep-veiculos-list min-h-7 p-0.5 rounded flex flex-col gap-1"
                                            data-list-type="veiculo" data-equipo-tipo="auxiliar">
                                            @if (isset($pepData[$pep->id]['auxiliar']['veiculos']) && count($pepData[$pep->id]['auxiliar']['veiculos']) > 0)
                                                @foreach ($pepData[$pep->id]['auxiliar']['veiculos'] as $veiculo)
                                                    <div wire:key="veic-aux-{{ $pep->id }}-{{ $veiculo->id }}"
                                                        style="background:#09143B; color:white;" class="rounded-md px-2 py-1 text-[11px] font-medium cursor-grab select-none draggable-item"
                                                        data-id="{{ $veiculo->id }}" data-type="veiculo">
                                                        <div class="truncate">{{ $veiculo->matricula }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="color:rgba(9,20,59,0.35);" class="text-[10px] ptr-placeholder text-center py-1">
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

        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl overflow-hidden flex flex-col drop-zone"
            data-pep-id="estaleiro" data-type="estaleiro">

            {{-- Header --}}
            <div style="background:#09143B !important;" class="px-4 py-3 flex items-center justify-between">
                <div>
                    <h2 style="color:white;" class="text-sm font-medium leading-tight">Estaleiro</h2>
                    <span style="color:rgba(255,255,255,0.5);" class="text-[10px]">{{ $pepEstaleiro?->localizacao->nombre ?? 'Funchal' }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <button wire:click="irDiaAnterior" type="button" title="Dia de trabalho anterior"
                        style="background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); border-radius:6px; padding:3px 8px; font-size:14px; cursor:pointer; line-height:1;">&#8249;</button>
                    <input type="date" wire:model.live="data"
                        style="background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); border-radius:6px; padding:3px 8px; font-size:11px; outline:none; color-scheme:dark;">
                    <button wire:click="irDiaSeguinte" type="button" title="Dia de trabalho seguinte"
                        style="background:rgba(255,255,255,0.1); color:white; border:1px solid rgba(255,255,255,0.2); border-radius:6px; padding:3px 8px; font-size:14px; cursor:pointer; line-height:1;">&#8250;</button>
                </div>
            </div>

            {{-- Buscar --}}
            <div style="background:#EEECEA !important; border-bottom:1px solid rgba(9,20,59,0.10); padding:8px 12px;" class="relative">
                <input id="estaleiro-search" wire:model.live.debounce.300ms="searchEstaleiro" type="search"
                    placeholder="Pesquisar colaborador ou veículo..."
                    style="width:100%; background:white !important; border:1px solid rgba(9,20,59,0.25) !important; border-radius:8px; padding:6px 10px; font-size:11px; color:#1A1A1A !important; outline:none;">
                @if ($searchEstaleiro)
                    <button wire:click="$set('searchEstaleiro','')" type="button"
                        class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none text-base">&#10005;</button>
                @endif
            </div>

            {{-- Tabs --}}
            <div x-data="{ tab: 'colaboradores' }" class="flex flex-col flex-1">
                <div style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);" class="flex">
                    <button @click="tab='colaboradores'"
                        :style="tab==='colaboradores' ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;'"
                        class="flex-1 text-[11px] font-medium py-2 transition-colors border-none cursor-pointer">
                        Colaboradores
                    </button>
                    <button @click="tab='veiculos'"
                        :style="tab==='veiculos' ? 'background:#09143B; color:#FFD300;' : 'background:transparent; color:#7A7775;'"
                        class="flex-1 text-[11px] font-medium py-2 transition-colors border-none cursor-pointer">
                        Veículos
                    </button>
                </div>

                {{-- Lista Colaboradores --}}
                <div x-show="tab==='colaboradores'" style="background:#F0EEEB;" class="overflow-y-auto max-h-[55vh]">
                    <div id="colaboradores-list" class="flex flex-col" data-list-type="colaborador">
                        @forelse ($colaboradores_libres as $colaborador)
                            <div wire:key="libre-col-{{ $colaborador->id }}"
                                style="border-bottom:1px solid rgba(9,20,59,0.06);"
                                class="px-3 py-2 flex flex-col gap-0.5 cursor-grab select-none draggable-item hover:bg-[#E4E2DF] transition-colors"
                                data-id="{{ $colaborador->id }}" data-type="colaborador">
                                <p style="color:#1A1A1A;" class="text-[12px] font-medium truncate leading-tight m-0">
                                    @php
                                        $partsNome = explode(' ', trim($colaborador->nombre));
                                        $partsApelido = explode(' ', trim($colaborador->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $colaborador->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </p>
                                <p style="color:#7A7775;" class="text-[10px] uppercase truncate m-0">
                                    {{ $colaborador->denominacion_cargo }}
                                </p>
                            </div>
                        @empty
                            <div class="text-center py-6 text-[12px]" style="color:#7A7775;">Nenhum colaborador disponível</div>
                        @endforelse
                    </div>
                </div>

                {{-- Lista Veículos --}}
                <div x-show="tab==='veiculos'" style="background:#F0EEEB;" class="overflow-y-auto max-h-[55vh]">
                    <div id="veiculos-list" class="flex flex-col" data-list-type="veiculo">
                        @forelse ($veiculos_libres as $veiculo)
                            <div wire:key="libre-veic-{{ $veiculo->id }}"
                                style="border-bottom:1px solid rgba(9,20,59,0.06);"
                                class="px-3 py-2 flex flex-col gap-0.5 cursor-grab select-none draggable-item hover:bg-[#E4E2DF] transition-colors"
                                data-id="{{ $veiculo->id }}" data-type="veiculo">
                                <p style="color:#1A1A1A;" class="text-[12px] font-medium truncate m-0">{{ $veiculo->matricula }}</p>
                                <p style="color:#7A7775;" class="text-[10px] uppercase truncate m-0">{{ $veiculo->marca }} {{ $veiculo->modelo }}</p>
                            </div>
                        @empty
                            <div class="text-center py-6 text-[12px]" style="color:#7A7775;">Nenhum veículo disponível</div>
                        @endforelse
                    </div>
                </div>

                {{-- Zona drop: colaboradores e veículos de volta ao estaleiro --}}
                <div style="background:#E4E2DF; border:2px dashed rgba(9,20,59,0.20);" class="rounded-xl mx-3 mb-3 mt-2 p-2">
                    <div class="pep-colaboradores-list flex flex-col min-h-[36px]" data-list-type="colaborador">
                        <div style="color:rgba(9,20,59,0.35);" class="text-center text-[11px] font-medium py-2 ptr-placeholder">
                            Largar aqui para remover do PEP
                        </div>
                    </div>
                    <div class="pep-veiculos-list flex flex-col" data-list-type="veiculo"></div>
                </div>
            </div>
        </div>

        {{-- ===== ZONAS DE ESTADO ===== --}}
        <div class="mt-4 flex flex-col gap-2">

            {{-- Baixas --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="baixa" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Baixas</span>
                    <span style="background:#fde8e8; color:#A32D2D;" class="text-[10px] px-2 py-0.5 rounded font-bold">Baixas</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-colaboradores-list flex-1 min-h-[60px] p-2 flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['baixa'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="baja-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                style="background:#fde8e8; border:1px solid rgba(163,45,45,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div style="color:#A32D2D;" class="text-[11px] font-medium leading-tight">
                                    @php
                                        $partsNome = explode(' ', trim($col->nombre));
                                        $partsApelido = explode(' ', trim($col->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $col->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Licenças --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="licenca" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Licenças</span>
                    <span style="background:#fdf0c2; color:#854F0B;" class="text-[10px] px-2 py-0.5 rounded font-bold">Licenças</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-colaboradores-list flex-1 min-h-[60px] p-2 flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['licenca'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="licenca-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                style="background:#fdf0c2; border:1px solid rgba(133,79,11,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div style="color:#854F0B;" class="text-[11px] font-medium leading-tight">
                                    @php
                                        $partsNome = explode(' ', trim($col->nombre));
                                        $partsApelido = explode(' ', trim($col->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $col->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Férias --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="ferias" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Férias</span>
                    <span style="background:#d4ede4; color:#0F6E56;" class="text-[10px] px-2 py-0.5 rounded font-bold">Férias</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-colaboradores-list flex-1 min-h-[60px] p-2 flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['ferias'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="ferias-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                style="background:#d4ede4; border:1px solid rgba(15,110,86,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div style="color:#0F6E56;" class="text-[11px] font-medium leading-tight">
                                    @php
                                        $partsNome = explode(' ', trim($col->nombre));
                                        $partsApelido = explode(' ', trim($col->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $col->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Consultas Médicas --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="consulta_medica" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Consultas Médicas</span>
                    <span style="background:#d4edf0; color:#0F6E6E;" class="text-[10px] px-2 py-0.5 rounded font-bold">🩺 Consultas</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-colaboradores-list flex-1 min-h-[60px] p-2 flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['consulta_medica'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="consulta-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                style="background:#d4edf0; border:1px solid rgba(15,110,110,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div style="color:#0F6E6E;" class="text-[11px] font-medium leading-tight">
                                    @php
                                        $partsNome = explode(' ', trim($col->nombre));
                                        $partsApelido = explode(' ', trim($col->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $col->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </div>
                                @if ($entry['fecha_hora_evento'])
                                    <div style="color:#0F6E6E;" class="text-[10px] mt-0.5">📅 {{ \Carbon\Carbon::parse($entry['fecha_hora_evento'])->format('d/m H:i') }}</div>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Formação --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="formacao" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Formação</span>
                    <span style="background:#ede8f5; color:#5B2D8E;" class="text-[10px] px-2 py-0.5 rounded font-bold">📚 Formação</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-colaboradores-list flex-1 min-h-[60px] p-2 flex flex-col gap-1.5"
                    data-list-type="colaborador">
                    @forelse($estadoData['formacao'] ?? [] as $entry)
                        @foreach ($entry['colaboradores'] as $col)
                            <div wire:key="formacao-{{ $entry['atrib_id'] }}-{{ $col->id }}"
                                style="background:#ede8f5; border:1px solid rgba(91,45,142,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item"
                                data-id="{{ $col->id }}" data-type="colaborador">
                                <div style="color:#5B2D8E;" class="text-[11px] font-medium leading-tight">
                                    @php
                                        $partsNome = explode(' ', trim($col->nombre));
                                        $partsApelido = explode(' ', trim($col->apellido));
                                        $firstName = $partsNome[0] ?? '';
                                        $stopWords = ['de','da','do','dos','das','e'];
                                        $lastParts = array_filter($partsApelido, fn($p) => !in_array(strtolower($p), $stopWords));
                                        $lastName = !empty($lastParts) ? end($lastParts) : ($partsApelido[count($partsApelido)-1] ?? '');
                                    @endphp
                                    {{ $col->numero_colaborador }} · {{ $firstName }} {{ $lastName }}
                                </div>
                                @if ($entry['fecha_hora_evento'])
                                    <div style="color:#5B2D8E;" class="text-[10px] mt-0.5">📅 {{ \Carbon\Carbon::parse($entry['fecha_hora_evento'])->format('d/m H:i') }}</div>
                                @endif
                                @if ($entry['descripcion_evento'])
                                    <div style="color:#5B2D8E;" class="text-[10px] italic mt-0.5">{{ $entry['descripcion_evento'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic">(Largar aqui)</div>
                    @endforelse
                </div>
            </div>

            {{-- Equipamentos em Reparação --}}
            <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);"
                class="rounded-xl overflow-hidden flex flex-col drop-zone"
                data-pep-id="reparacao" data-type="estado">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center justify-between">
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Em Reparação</span>
                    <span style="background:#fff3e0; color:#E65100;" class="text-[10px] px-2 py-0.5 rounded font-bold">🔧 Reparação</span>
                </div>
                <div style="background:#EEECEA;"
                    class="pep-veiculos-list min-h-[60px] p-2 flex flex-wrap gap-1.5"
                    data-list-type="veiculo">
                    @forelse($estadoData['reparacao'] ?? [] as $entry)
                        @foreach ($entry['veiculos'] as $veh)
                            <div wire:key="reparacao-{{ $entry['atrib_id'] }}-{{ $veh->id }}"
                                style="background:#fff3e0; border:1px solid rgba(230,81,0,0.20);"
                                class="rounded-lg px-2 py-1.5 cursor-grab select-none draggable-item min-w-[120px]"
                                data-id="{{ $veh->id }}" data-type="veiculo">
                                <div style="color:#E65100;" class="text-[11px] font-bold">{{ $veh->matricula }}</div>
                                @if ($entry['fecha_entrada_taller'])
                                    <div style="color:#E65100;" class="text-[10px] mt-0.5">Entrada: {{ \Carbon\Carbon::parse($entry['fecha_entrada_taller'])->format('d/m/Y') }}</div>
                                @endif
                                @if ($entry['nombre_taller'])
                                    <div style="color:#E65100;" class="text-[10px] italic mt-0.5">{{ $entry['nombre_taller'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    @empty
                        <div style="color:rgba(9,20,59,0.30);" class="text-center text-[11px] py-3 ptr-placeholder italic w-full">(Arrastar veículos / ferramentas aqui)</div>
                    @endforelse
                </div>
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
                        <label class="block text-[0.82rem] font-semibold mb-1.5" style="color:#6b7280!important">Tarefa, presupuesto ou observação para a equipa</label>
                        <textarea wire:model="modalNotas" rows="4"
                            placeholder="Descreva a tarefa específica, orçamento alocado ou qualquer aviso para a equipa hoje..."
                            class="w-full border-[1.5px] border-blue-200 rounded-lg py-2.5 px-3 text-[0.9rem] bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            style="color:#111827!important"
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

    <style>
        #estaleiro-search::placeholder { color: #7A7775 !important; opacity: 1 !important; }
    </style>
</div>
