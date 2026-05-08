<div class="p-4 w-full max-w-7xl mx-auto">
<div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">

    {{-- ── HEADER ───────────────────────────────────────────────────── --}}
    <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon name="home" class="text-[#FFD300] w-4 h-4" />
            <span class="text-white font-medium text-sm">Painel de Controlo</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] bg-[rgba(255,211,0,0.15)] text-[#FFD300] px-2 py-1 rounded font-medium tracking-wide">
                {{ $hoje->translatedFormat('d \d\e F \d\e Y') }}
            </span>
            <a href="{{ route('gestao-equipas') }}" wire:navigate
               class="text-[10px] bg-[#FFD300] text-[#09143B] px-3 py-1 rounded font-black tracking-wide hover:bg-yellow-300 transition-colors">
                + Gestão de Equipas
            </a>
        </div>
    </div>

    {{-- ── BODY ─────────────────────────────────────────────────────── --}}
    <div class="bg-[#EEECEA] p-4 space-y-4">

        {{-- Métricas --}}
        @if(!empty($resumoAtividade))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div style="background:#FFFFFF; border:1px solid rgba(9,20,59,0.18);" class="rounded-lg px-3 py-2.5">
                <div style="color:#7A7775;" class="text-[10px] uppercase tracking-wide mb-1">Projetos Ativos</div>
                <div style="color:#1A1A1A;" class="text-2xl font-bold leading-none">{{ $resumoAtividade['peps_ativos'] }}</div>
                <div style="color:#7A7775;" class="text-[10px] mt-1">PEPs em curso</div>
            </div>
            <div style="background:#FFFFFF; border:1px solid rgba(9,20,59,0.18);" class="rounded-lg px-3 py-2.5">
                <div style="color:#7A7775;" class="text-[10px] uppercase tracking-wide mb-1">Equipas Hoje</div>
                <div style="color:#1A1A1A;" class="text-2xl font-bold leading-none">{{ $resumoAtividade['peps_com_equipa'] }}</div>
                <div style="color:#7A7775;" class="text-[10px] mt-1">com pessoal atribuído</div>
            </div>
            <div style="background:#FFFFFF; border:1px solid rgba(9,20,59,0.18);" class="rounded-lg px-3 py-2.5">
                <div style="color:#7A7775;" class="text-[10px] uppercase tracking-wide mb-1">Pessoal em Campo</div>
                <div style="color:#1A1A1A;" class="text-2xl font-bold leading-none">{{ $resumoAtividade['colaboradores_em_campo'] }}</div>
                <div style="color:#7A7775;" class="text-[10px] mt-1">colaboradores ativos</div>
            </div>
            <div style="background:#FFFFFF; border:1px solid rgba(9,20,59,0.18);" class="rounded-lg px-3 py-2.5">
                <div style="color:#7A7775;" class="text-[10px] uppercase tracking-wide mb-1">Viaturas em Campo</div>
                <div style="color:#1A1A1A;" class="text-2xl font-bold leading-none">{{ $resumoAtividade['veiculos_em_campo'] }}</div>
                <div style="color:#7A7775;" class="text-[10px] mt-1">veículos afetos</div>
            </div>
        </div>
        @endif

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Coluna esquerda --}}
            <div class="lg:col-span-8 space-y-4">

                @if($minhaEquipa)
                {{-- Widget: Minha Equipa --}}
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center gap-2">
                        <flux:icon name="user-group" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-[11px] uppercase tracking-widest">A Minha Equipa / Projeto</span>
                    </div>
                    <div class="bg-[#F0EEEB] p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-wide text-[#7A7775] mb-1">Projeto Atual</div>
                            <div class="text-base font-bold text-[#1A1A1A]">{{ $minhaEquipa->pep?->nombre ?? 'Sem Projeto Fixo' }}</div>
                            <div class="text-[11px] text-[#4A4845] mt-0.5 italic">{{ $minhaEquipa->pep?->localizacao?->nombre }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between px-3 py-2 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg">
                                <span class="text-[11px] text-[#4A4845] font-medium">Colaboradores</span>
                                <span class="text-base font-bold text-[#09143B]">{{ $minhaEquipa->colaboradores->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between px-3 py-2 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg">
                                <span class="text-[11px] text-[#4A4845] font-medium">Veículos</span>
                                <span class="text-base font-bold text-[#09143B]">{{ $minhaEquipa->veiculos->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Widget: Avisos e Comunicados --}}
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center gap-2">
                        <flux:icon name="megaphone" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-[11px] uppercase tracking-widest">Avisos e Comunicados</span>
                    </div>
                    <div class="bg-[#F0EEEB] divide-y divide-[rgba(9,20,59,0.06)]">
                        @forelse($avisos as $aviso)
                        <div class="flex gap-3 p-3 hover:bg-[#E4E2DF] transition-colors">
                            <div class="h-12 w-12 flex-none rounded-lg overflow-hidden bg-[#E4E2DF] border border-[rgba(9,20,59,0.10)] flex items-center justify-center">
                                @if($aviso->imagem)
                                    <img src="{{ asset('storage/' . $aviso->imagem) }}" class="h-full w-full object-cover">
                                @else
                                    <flux:icon name="photo" class="w-5 h-5 text-[#7A7775]" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[12px] font-semibold text-[#1A1A1A] leading-tight">{{ $aviso->titulo }}</div>
                                <div class="text-[11px] text-[#7A7775] mt-0.5 line-clamp-2">{{ Str::limit($aviso->conteudo, 120) }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-[11px] text-[#7A7775] italic">Sem avisos recentes.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Widget: Inspeções Próximas --}}
                @php
                $extValidos = collect($inspecoesProximas['extintores'] ?? [])->filter(fn($e) => $e->proxima_revisao && \Carbon\Carbon::parse($e->proxima_revisao)->year > 2000);
                $ferrValidas = collect($inspecoesProximas['ferramentas'] ?? [])->filter(fn($f) => $f->ultimoLog?->proxima_verificacao && \Carbon\Carbon::parse($f->ultimoLog->proxima_verificacao)->year > 2000 && $f->nome);
                @endphp
                @if((auth()->user()->hasRole('logi') || auth()->user()->isAdmin()) && ($extValidos->count() > 0 || $ferrValidas->count() > 0))
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center gap-2">
                        <flux:icon name="shield-exclamation" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-[11px] uppercase tracking-widest">Inspeções Próximas</span>
                    </div>
                    <div class="bg-[#F0EEEB] divide-y divide-[rgba(9,20,59,0.06)]">
                        @foreach($extValidos as $ext)
                        <div class="flex items-center justify-between px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <flux:icon name="fire" class="text-[#A32D2D] w-4 h-4" />
                                <span class="text-[12px] text-[#1A1A1A]">{{ $ext->referencia ?? $ext->numero_serie }}</span>
                            </div>
                            <span class="text-[10px] bg-[#fde8e8] text-[#A32D2D] px-2 py-0.5 rounded font-medium">
                                {{ \Carbon\Carbon::parse($ext->proxima_revisao)->format('d/m/Y') }}
                            </span>
                        </div>
                        @endforeach
                        @foreach($ferrValidas as $ferr)
                        <div class="flex items-center justify-between px-3 py-2.5">
                            <div class="flex items-center gap-2">
                                <flux:icon name="wrench" class="text-[#854F0B] w-4 h-4" />
                                <span class="text-[12px] text-[#1A1A1A]">{{ $ferr->nome }}</span>
                            </div>
                            <span class="text-[10px] bg-[#fdf0c2] text-[#854F0B] px-2 py-0.5 rounded font-medium">
                                {{ \Carbon\Carbon::parse($ferr->ultimoLog->proxima_verificacao)->format('d/m/Y') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Widget: Acessos Rápidos --}}
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center gap-2">
                        <flux:icon name="squares-2x2" class="text-[#FFD300] w-4 h-4" />
                        <span class="text-white font-medium text-[11px] uppercase tracking-widest">Acessos Rápidos</span>
                    </div>
                    <div class="bg-[#F0EEEB] p-3 grid grid-cols-3 gap-2">
                        <a href="{{ route('gestao-equipas') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="calendar-days" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Gestão Equipas</span>
                        </a>
                        <a href="{{ route('colaboradores.index') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="users" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Colaboradores</span>
                        </a>
                        <a href="{{ route('veiculos.index') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="truck" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Veículos</span>
                        </a>
                        @if(auth()->user()->hasRole('epi') || auth()->user()->isAdmin())
                        <a href="{{ route('epis.entregas.index') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="hand-raised" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Entregas EPI</span>
                        </a>
                        @endif
                        @if(auth()->user()->hasRole('logi') || auth()->user()->isAdmin())
                        <a href="{{ route('ferramentas.index') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="wrench" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Ferramentas</span>
                        </a>
                        @endif
                        <a href="{{ route('resumo-mensal') }}" wire:navigate
                           class="flex flex-col items-center gap-1.5 p-3 bg-[#EEECEA] border border-[rgba(9,20,59,0.08)] rounded-lg hover:bg-[#E4E2DF] transition-colors">
                            <flux:icon name="chart-bar" class="w-5 h-5 text-[#09143B]" />
                            <span class="text-[10px] text-[#4A4845] font-medium text-center">Resumo Mensal</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Coluna direita --}}
            <div class="lg:col-span-4 space-y-3">

                @if(auth()->user()->hasRole('epi') || auth()->user()->isAdmin())

                {{-- Widget: Pedidos de EPI --}}
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="bell" class="text-[#FFD300] w-4 h-4" />
                            <span class="text-white font-medium text-[11px] uppercase tracking-widest">Pedidos de EPI</span>
                        </div>
                        @if($pedidosPendentesCount > 0)
                        <span class="text-[10px] bg-[#fde8e8] text-[#A32D2D] px-1.5 py-0.5 rounded font-bold">
                            {{ $pedidosPendentesCount }} pendentes
                        </span>
                        @endif
                    </div>
                    <div class="bg-[#F0EEEB] px-3 py-3 flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-[#1A1A1A]">{{ $pedidosPendentesCount }}</div>
                            <div class="text-[10px] text-[#7A7775]">solicitações aguardando</div>
                        </div>
                        <a href="{{ route('epis.pedidos') }}" wire:navigate
                           class="text-[10px] bg-[#09143B] text-[#FFD300] px-3 py-1.5 rounded font-bold hover:bg-[#0d1a4a] transition-colors">
                            Ver Pedidos →
                        </a>
                    </div>
                </div>

                {{-- Widget: Assinaturas Pendentes --}}
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="pencil-square" class="text-[#FFD300] w-4 h-4" />
                            <span class="text-white font-medium text-[11px] uppercase tracking-widest">Assinaturas de EPI</span>
                        </div>
                        @if($assinaturasPendentesCount > 0)
                        <span class="text-[10px] bg-[#fdf0c2] text-[#854F0B] px-1.5 py-0.5 rounded font-bold">
                            {{ $assinaturasPendentesCount }} pendentes
                        </span>
                        @endif
                    </div>
                    <div class="bg-[#F0EEEB] px-3 py-3 flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-[#1A1A1A]">{{ $assinaturasPendentesCount }}</div>
                            <div class="text-[10px] text-[#7A7775]">pendentes de validação</div>
                        </div>
                        <a href="{{ route('epis.entregas.index') }}" wire:navigate
                           class="text-[10px] bg-[#09143B] text-[#FFD300] px-3 py-1.5 rounded font-bold hover:bg-[#0d1a4a] transition-colors">
                            Gerir →
                        </a>
                    </div>
                </div>

                {{-- Widget: Stock Crítico --}}
                @if(!empty($stockCritico))
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="exclamation-triangle" class="text-[#FFD300] w-4 h-4" />
                            <span class="text-white font-medium text-[11px] uppercase tracking-widest">Stock Crítico</span>
                        </div>
                        <span class="text-[10px] bg-[#fde8e8] text-[#A32D2D] px-1.5 py-0.5 rounded font-bold">
                            alerta
                        </span>
                    </div>
                    <div class="bg-[#F0EEEB] px-3 py-3 flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-[#A32D2D]">{{ count($stockCritico) }}</div>
                            <div class="text-[10px] text-[#7A7775]">{{ count($stockCritico) === 1 ? 'item abaixo do mínimo' : 'itens abaixo do mínimo' }}</div>
                        </div>
                        <a href="{{ route('epis.index') }}" wire:navigate
                           class="text-[10px] bg-[#09143B] text-[#FFD300] px-3 py-1.5 rounded font-bold hover:bg-[#0d1a4a] transition-colors">
                            Catálogo →
                        </a>
                    </div>
                </div>
                @endif

                @endif

                @if(auth()->user()->hasRole('logi') || auth()->user()->isAdmin())

                {{-- Widget: Segurança e Conformidade --}}
                @php $totalAlertas = count($inspecoesProximas['extintores'] ?? []) + count($inspecoesProximas['ferramentas'] ?? []); @endphp
                <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.10)]">
                    <div class="bg-[#09143B] px-3 py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                            <span class="text-white font-medium text-[11px] uppercase tracking-widest">Segurança</span>
                        </div>
                        @if($totalAlertas > 0)
                        <span class="text-[10px] bg-[#fdf0c2] text-[#854F0B] px-1.5 py-0.5 rounded font-bold">
                            {{ $totalAlertas }} a verificar
                        </span>
                        @endif
                    </div>
                    <div class="bg-[#F0EEEB] px-3 py-3">
                        <div class="text-2xl font-bold text-[#1A1A1A] mb-0.5">{{ $totalAlertas }}</div>
                        <div class="text-[10px] text-[#7A7775] mb-3">{{ $totalAlertas === 1 ? 'equipamento a verificar' : 'equipamentos a verificar' }}</div>
                        <div class="flex gap-2">
                            <a href="{{ route('extintores.index') }}" wire:navigate
                               class="flex-1 text-center text-[10px] bg-[#EEECEA] border border-[rgba(9,20,59,0.12)] text-[#1A1A1A] font-semibold py-1.5 rounded-lg hover:bg-[#E4E2DF] transition-colors">
                                Extintores →
                            </a>
                            <a href="{{ route('ferramentas.index') }}" wire:navigate
                               class="flex-1 text-center text-[10px] bg-[#EEECEA] border border-[rgba(9,20,59,0.12)] text-[#1A1A1A] font-semibold py-1.5 rounded-lg hover:bg-[#E4E2DF] transition-colors">
                                Ferramentas →
                            </a>
                        </div>
                    </div>
                </div>

                @endif

            </div>

        </div>

        <x-global-footer />

    </div>{{-- /body --}}
</div>{{-- /wrapper --}}
</div>
