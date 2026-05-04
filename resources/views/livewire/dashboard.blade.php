<div class="px-6 py-6 w-full max-w-7xl mx-auto space-y-8 animate-in fade-in duration-500">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-blue-700/30 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-yellow-500 tracking-tight uppercase">Painel de Controlo</h1>
            <p class="text-white/60 font-medium">Bem-vindo, {{ auth()->user()->name }}. Resumo de hoje, {{ $hoje->translatedFormat('d \d\e F \d\e Y') }}.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('gestao-equipas') }}" wire:navigate 
                class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg active:scale-95 text-sm uppercase tracking-wider">
                <svg class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Gestão de Equipas
            </a>
        </div>
    </div>

    {{-- Top Stats Row --}}
    @if(!empty($resumoAtividade))
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-900/40 backdrop-blur-sm p-4 rounded-2xl border border-blue-700/30">
            <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Projetos Ativos</p>
            <p class="text-2xl font-black text-white">{{ $resumoAtividade['peps_ativos'] }}</p>
        </div>
        <div class="bg-blue-900/40 backdrop-blur-sm p-4 rounded-2xl border border-blue-700/30">
            <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Equipas Hoje</p>
            <p class="text-2xl font-black text-white">{{ $resumoAtividade['peps_com_equipa'] }}</p>
        </div>
        <div class="bg-blue-900/40 backdrop-blur-sm p-4 rounded-2xl border border-blue-700/30">
            <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Pessoal em Campo</p>
            <p class="text-2xl font-black text-white">{{ $resumoAtividade['colaboradores_em_campo'] }}</p>
        </div>
        <div class="bg-blue-900/40 backdrop-blur-sm p-4 rounded-2xl border border-blue-700/30">
            <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Viaturas em Campo</p>
            <p class="text-2xl font-black text-white">{{ $resumoAtividade['veiculos_em_campo'] }}</p>
        </div>
    </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left Column (Announcements / My Team) --}}
        <div class="lg:col-span-8 space-y-6">
            
            @if($minhaEquipa)
            {{-- Widget: Minha Equipa --}}
            <div class="bg-blue-900/40 backdrop-blur-md rounded-3xl border border-blue-700/50 overflow-hidden shadow-2xl shadow-blue-950/20 group">
                <div class="bg-linear-to-r from-blue-800 to-blue-900 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-yellow-500 rounded-xl shadow-inner">
                            <svg class="h-5 w-5 text-blue-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <span class="text-white font-black uppercase tracking-widest text-sm">A Minha Equipa / Projeto</span>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-black text-blue-400 uppercase tracking-widest mb-2">Projeto Atual</p>
                        <h3 class="text-xl font-bold text-white">{{ $minhaEquipa->pep?->nombre ?? 'Sem Projeto Fixo' }}</h3>
                        <p class="text-sm text-white/50 italic mt-1">{{ $minhaEquipa->pep?->localizacao?->nombre }}</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-xs text-white/70 font-bold uppercase">Colaboradores</span>
                            <span class="text-lg font-black text-yellow-500">{{ $minhaEquipa->colaboradores->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-xs text-white/70 font-bold uppercase">Veículos</span>
                            <span class="text-lg font-black text-yellow-500">{{ $minhaEquipa->veiculos->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Widget: Avisos Importantes --}}
            <div class="bg-linear-to-br from-blue-900/60 to-blue-950/80 backdrop-blur-md rounded-3xl border border-blue-700/30 overflow-hidden shadow-xl">
                <div class="px-6 py-5 border-b border-blue-800/50 flex items-center gap-3">
                    <svg class="h-5 w-5 text-yellow-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                    <span class="text-white font-black uppercase tracking-widest text-sm">Avisos e Comunicados</span>
                </div>
                <div class="p-6">
                    @forelse($avisos as $aviso)
                    <div class="flex gap-4 p-4 rounded-2xl hover:bg-white/5 transition-colors group">
                        <div class="h-16 w-16 flex-none rounded-xl overflow-hidden bg-blue-800 flex items-center justify-center border border-blue-700">
                            @if($aviso->imagem)
                                <img src="{{ asset('storage/' . $aviso->imagem) }}" class="h-full w-full object-cover">
                            @else
                                <svg class="h-6 w-6 text-blue-400 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-bold group-hover:text-yellow-500 transition-colors">{{ $aviso->titulo }}</h4>
                            <p class="text-sm text-white/50 line-clamp-2 mt-1">{{ Str::limit($aviso->conteudo, 120) }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center text-white/30 italic">Sem avisos recentes.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Right Column (Alerts & Stock) --}}
        <div class="lg:col-span-4 space-y-6">

            @if(auth()->user()->hasRole('epi') || auth()->user()->isAdmin())
            {{-- Widget: Pedidos Pendentes --}}
            <div class="bg-blue-600 rounded-3xl p-4 shadow-xl shadow-blue-900/20 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 text-white opacity-20 group-hover:scale-110 transition-transform duration-500">
                    <flux:icon.bell class="size-24" />
                </div>
                <h4 class="text-white font-black uppercase tracking-widest text-xs">Pedidos de EPI Mobiles</h4>
                <p class="text-3xl font-black text-white mt-2">{{ $pedidosPendentesCount }}</p>
                <p class="text-white/80 text-xs font-bold mt-1">Solicitações aguardando resposta</p>
                <a href="{{ route('epis.pedidos') }}" wire:navigate class="mt-4 block w-full bg-white text-blue-600 text-center py-1.5 rounded-xl text-xs font-black uppercase tracking-tighter hover:bg-blue-50 transition-colors">Ver Pedidos</a>
            </div>

            {{-- Widget: Assinaturas Pendentes --}}
            <div class="bg-yellow-500 rounded-3xl p-4 shadow-xl shadow-yellow-900/20 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 text-yellow-600 opacity-20 group-hover:scale-110 transition-transform duration-500">
                    <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M14.06,9.02L12.98,7.94L11.9,9.02L14.06,9.02ZM15.84,7.24C16.23,7.63 16.23,8.26 15.84,8.65L14.34,10.15L12.18,10.15L15.84,7.24ZM3,17.25L3,21L6.75,21L17.81,9.94L14.06,6.19L3,17.25Z" /></svg>
                </div>
                <h4 class="text-blue-950 font-black uppercase tracking-widest text-xs">Assinaturas de EPI</h4>
                <p class="text-3xl font-black text-blue-950 mt-2">{{ $assinaturasPendentesCount }}</p>
                <p class="text-blue-950/70 text-xs font-bold mt-1">Pendentes de validação</p>
                <a href="{{ route('epis.entregas.index') }}" wire:navigate class="mt-4 block w-full bg-blue-950 text-white text-center py-1.5 rounded-xl text-xs font-black uppercase tracking-tighter hover:bg-blue-900 transition-colors">Gerir Entregas</a>
            </div>

            {{-- Widget: Stock Crítico --}}
            @if(!empty($stockCritico))
            <div class="relative overflow-hidden rounded-3xl bg-red-950/60 border border-red-500/30 p-4">
                <div class="absolute top-3 right-4 opacity-10">
                    <svg class="w-16 h-16 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21h22L12 2zm0 3.5L20.5 19h-17L12 5.5zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
                    </svg>
                </div>
                <p class="text-red-400 text-xs font-bold uppercase tracking-widest mb-1">Stock Crítico / Alerta</p>
                <p class="text-3xl font-black text-white mb-1">{{ count($stockCritico) }}</p>
                <p class="text-red-300/70 text-xs mb-4">{{ count($stockCritico) === 1 ? 'item abaixo do mínimo' : 'itens abaixo do mínimo' }}</p>
                <a href="{{ route('epis.index') }}" wire:navigate class="block text-center bg-white/10 hover:bg-white/20 text-white text-xs font-semibold py-1.5 px-4 rounded-xl transition-colors">
                    VER CATÁLOGO COMPLETO →
                </a>
            </div>
            @endif
            @endif

            @if(auth()->user()->hasRole('logi') || auth()->user()->isAdmin())
            {{-- Widget: Segurança e Conformidade --}}
            @php $totalAlertas = count($inspecoesProximas['extintores'] ?? []) + count($inspecoesProximas['ferramentas'] ?? []); @endphp
            <div class="relative overflow-hidden rounded-3xl bg-orange-950/60 border border-orange-500/30 p-4">
                <div class="absolute top-3 right-4 opacity-10">
                    <svg class="w-16 h-16 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12V11c0 4.52-3.08 8.79-7 10.07C8.08 19.79 5 15.52 5 11V6.3l7-3.12zM11 7v6h2V7h-2zm0 8v2h2v-2h-2z"/>
                    </svg>
                </div>
                <p class="text-orange-400 text-xs font-bold uppercase tracking-widest mb-1">Segurança e Conformidade</p>
                <p class="text-3xl font-black text-white mb-1">{{ $totalAlertas }}</p>
                <p class="text-orange-300/70 text-xs mb-4">{{ $totalAlertas === 1 ? 'equipamento a verificar' : 'equipamentos a verificar' }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('extintores.index') }}" wire:navigate class="flex-1 text-center bg-white/10 hover:bg-white/20 text-white text-xs font-semibold py-1.5 px-3 rounded-xl transition-colors">
                        Extintores →
                    </a>
                    <a href="{{ route('ferramentas.index') }}" wire:navigate class="flex-1 text-center bg-white/10 hover:bg-white/20 text-white text-xs font-semibold py-1.5 px-3 rounded-xl transition-colors">
                        Ferramentas →
                    </a>
                </div>
            </div>
            @endif

        </div>

    </div>

    <x-global-footer />

</div>
