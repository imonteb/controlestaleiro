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
            <div class="bg-blue-600 rounded-3xl p-6 shadow-xl shadow-blue-900/20 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 text-white opacity-20 group-hover:scale-110 transition-transform duration-500">
                    <flux:icon.bell class="size-24" />
                </div>
                <h4 class="text-white font-black uppercase tracking-widest text-xs">Pedidos de EPI Mobiles</h4>
                <p class="text-4xl font-black text-white mt-2">{{ $pedidosPendentesCount }}</p>
                <p class="text-white/80 text-xs font-bold mt-1">Solicitações aguardando resposta</p>
                <a href="{{ route('epis.pedidos') }}" wire:navigate class="mt-4 block w-full bg-white text-blue-600 text-center py-2 rounded-xl text-xs font-black uppercase tracking-tighter hover:bg-blue-50 transition-colors">Ver Pedidos</a>
            </div>

            {{-- Widget: Assinaturas Pendentes --}}
            <div class="bg-yellow-500 rounded-3xl p-6 shadow-xl shadow-yellow-900/20 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 text-yellow-600 opacity-20 group-hover:scale-110 transition-transform duration-500">
                    <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M14.06,9.02L12.98,7.94L11.9,9.02L14.06,9.02ZM15.84,7.24C16.23,7.63 16.23,8.26 15.84,8.65L14.34,10.15L12.18,10.15L15.84,7.24ZM3,17.25L3,21L6.75,21L17.81,9.94L14.06,6.19L3,17.25Z" /></svg>
                </div>
                <h4 class="text-blue-950 font-black uppercase tracking-widest text-xs">Assinaturas de EPI</h4>
                <p class="text-4xl font-black text-blue-950 mt-2">{{ $assinaturasPendentesCount }}</p>
                <p class="text-blue-950/70 text-xs font-bold mt-1">Pendentes de validação</p>
                <a href="{{ route('epis.entregas.index') }}" wire:navigate class="mt-4 block w-full bg-blue-950 text-white text-center py-2 rounded-xl text-xs font-black uppercase tracking-tighter hover:bg-blue-900 transition-colors">Gerir Entregas</a>
            </div>

            {{-- Widget: Stock Crítico --}}
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xl shadow-blue-950/10">
                <div class="bg-red-50 border-b border-red-100 px-6 py-4 flex items-center justify-between">
                    <span class="text-red-700 font-black uppercase tracking-widest text-xs">Stock Crítico / Alerta</span>
                    <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded-full font-bold uppercase">{{ count($stockCritico) }} Itens</span>
                </div>
                <div class="p-2">
                    @foreach($stockCritico as $item)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-2xl transition-all border-b border-gray-50 last:border-0">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $item->nombre }}</p>
                            <p class="text-xs text-gray-600 font-bold uppercase">{{ $item->talla ?: 'S/T' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <span class="block text-xs font-black {{ $item->stock_total <= 0 ? 'text-red-600' : 'text-orange-600' }}">{{ $item->stock_total }} {{ $item->unidade ?: 'UN' }}</span>
                                <span class="text-xs text-gray-600 uppercase">Mín. {{ $item->stock_minimo }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ route('epis.index') }}" wire:navigate class="block w-full text-center py-3 text-xs font-black text-gray-600 uppercase hover:text-blue-600 transition-colors">Ver catálogo completo</a>
                </div>
            </div>
            @endif

            @if(auth()->user()->hasRole('logi') || auth()->user()->isAdmin())
            {{-- Widget: Inspeções Urgentes --}}
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xl shadow-blue-950/10">
                <div class="bg-orange-50 border-b border-orange-100 px-6 py-4 flex items-center justify-between">
                    <span class="text-orange-700 font-black uppercase tracking-widest text-xs">Segurança e Conformidade</span>
                    <svg class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($inspecoesProximas['extintores'] as $ext)
                    <div class="flex items-center justify-between p-4 bg-red-50/50 rounded-2xl border border-red-100/30">
                        <div>
                            <p class="text-xs font-bold text-gray-900 uppercase">Extintor {{ $ext->num_serie }}</p>
                            <p class="text-xs text-red-600 font-black uppercase">{{ $ext->proxima_revisao->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="h-2 w-2 rounded-full bg-red-500 shadow-sm animate-pulse"></div>
                    </div>
                    @endforeach

                    @foreach($inspecoesProximas['ferramentas'] as $ferr)
                    <div class="flex items-center justify-between p-4 bg-orange-50/30 rounded-2xl border border-orange-100/30">
                        <div class="min-w-0 pr-4">
                            <p class="text-xs font-bold text-gray-900 truncate uppercase">{{ $ferr->designacao }}</p>
                            <p class="text-xs text-orange-600 font-black uppercase">Inspeção: {{ $ferr->ultimoLog?->proxima_verificacao?->format('d/m/Y') }}</p>
                        </div>
                        <svg class="h-4 w-4 text-orange-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                    </div>
                    @endforeach

                    @if(count($inspecoesProximas['extintores']) === 0 && count($inspecoesProximas['ferramentas']) === 0)
                        <div class="p-8 text-center text-xs font-bold text-gray-600 uppercase italic">Tudo conforme para já.</div>
                    @endif
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
