<div class="px-6 py-8 w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-500">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-blue-700/30 pb-10">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase leading-none">
                Gestão de <span class="text-yellow-500">Notificações</span> PWA
            </h1>
            <p class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em] flex items-center gap-2">
                <span class="w-4 h-[2px] bg-yellow-500"></span>
                Avisos e Alertas em Tempo Real C016
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl flex items-center gap-4 animate-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Coluna Esquerda: Formulário -->
        <div class="lg:col-span-1">
            <div class="bg-blue-900/30 backdrop-blur-md rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-2xl sticky top-24">
                <div class="bg-linear-to-r from-blue-800 to-blue-900 px-8 py-6 border-b border-blue-700/50">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                        Nova Notificação
                    </h2>
                </div>
                <div class="p-8">
                    <form wire:submit.prevent="criarNotificacao" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Tipo de Aviso</label>
                            <select wire:model="tipo" class="w-full bg-blue-950 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all cursor-pointer">
                                <option value="geral">Geral / Informativo</option>
                                <option value="clima">Alerta Meteorológico (Clima)</option>
                                <option value="rrhh">Recursos Humanos (RH)</option>
                                <option value="seguranca">Higiene e Segurança (HSA)</option>
                            </select>
                            @error('tipo') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Título</label>
                            <input type="text" wire:model="titulo" placeholder="Ex: Aviso de Chuva Forte" maxlength="100" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all placeholder:text-white/20">
                            @error('titulo') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Mensagem</label>
                            <textarea wire:model="mensagem" rows="4" placeholder="Detalhes do aviso..." maxlength="1000" class="w-full bg-white/5 border-blue-700/50 rounded-2xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all placeholder:text-white/20 leading-relaxed"></textarea>
                            @error('mensagem') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Válido até</label>
                            <input type="datetime-local" wire:model="data_expiracao" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all [color-scheme:dark]">
                            <p class="text-[10px] text-white/40 font-medium italic mt-2">Após esta data, o aviso expira automaticamente.</p>
                            @error('data_expiracao') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black py-4 rounded-2xl shadow-xl shadow-yellow-500/20 transition-all active:scale-95 text-xs uppercase tracking-widest mt-4">
                            PUBLICAR AGORA
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Lista de Notificações -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Ativas -->
            <div class="bg-blue-900/30 backdrop-blur-md rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-2xl">
                <div class="bg-blue-800/50 px-8 py-5 flex justify-between items-center border-b border-blue-700/30">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                        Emissão Ativa na App
                    </h2>
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] font-black px-3 py-1 rounded-full border border-blue-500/30">{{ $notificacoesActivas->count() }} ALERTS</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-blue-900/50">
                                <th class="px-8 py-4 text-[10px] font-black text-blue-400 uppercase tracking-widest">Tipo / Conteúdo</th>
                                <th class="px-8 py-4 text-[10px] font-black text-blue-400 uppercase tracking-widest">Expiração</th>
                                <th class="px-8 py-4 text-[10px] font-black text-blue-400 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-800/30">
                            @forelse($notificacoesActivas as $notif)
                                <tr class="hover:bg-white/5 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-blue-600/20 border border-blue-500/20 shrink-0 flex items-center justify-center text-xl">
                                                @if($notif->tipo === 'clima') ⛈️ @elseif($notif->tipo === 'seguranca') 🛡️ @elseif($notif->tipo === 'rrhh') 👥 @else ℹ️ @endif
                                            </div>
                                             <div>
                                                @if($notif->tipo === 'seguranca')
                                                    <a href="{{ route('seguranca.index', ['tab' => 'incidentes']) }}" wire:navigate class="text-sm font-black text-white hover:text-yellow-500 transition-colors uppercase tracking-tight flex items-center gap-2 group/link">
                                                        {{ $notif->titulo }}
                                                        <svg class="w-3 h-3 opacity-0 group-hover/link:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                                    </a>
                                                @else
                                                    <div class="text-sm font-black text-white group-hover:text-yellow-500 transition-colors uppercase tracking-tight">{{ $notif->titulo }}</div>
                                                @endif
                                                <div class="text-xs text-white/40 mt-1 line-clamp-1 italic max-w-sm">{{ $notif->mensagem }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($notif->data_expiracao)
                                            <div class="text-sm font-black italic {{ $notif->data_expiracao < now()->addHours(12) ? 'text-orange-500' : 'text-white/60' }}">
                                                {{ $notif->data_expiracao->format('d/m/Y') }}
                                            </div>
                                            <div class="text-[10px] font-black text-white/30 uppercase tracking-widest">{{ $notif->data_expiracao->format('H:i') }}</div>
                                        @else
                                            <span class="text-[10px] font-black text-blue-500/50 uppercase tracking-widest">PERMANENTE</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button wire:click="desactivar({{ $notif->id }})" 
                                                class="text-red-400/50 hover:text-red-400 transition-colors text-[10px] font-black uppercase tracking-widest border border-red-500/10 hover:border-red-500/30 bg-red-950/20 px-4 py-2 rounded-xl active:scale-95"
                                                onclick="confirm('Desativar e remover esta notificação da App?') || event.stopImmediatePropagation()">
                                            Desativar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-20 text-center">
                                        <div class="text-white/20 text-[10px] font-black uppercase tracking-[0.5em]">Nenhum aviso em curso</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Histórico/Inativas -->
            @if($notificacoesInactivas->count() > 0)
            <div class="bg-blue-900/10 backdrop-blur-sm rounded-[2.5rem] border border-blue-700/20 overflow-hidden shadow-xl">
                <div class="px-8 py-4 border-b border-blue-700/20 flex justify-between items-center opacity-60">
                    <h2 class="text-[10px] font-black text-white/50 uppercase tracking-[0.3em]">Arquivo Histórico</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-blue-800/10">
                            @foreach($notificacoesInactivas as $notif)
                                <tr class="opacity-40 hover:opacity-100 transition-opacity">
                                    <td class="px-8 py-4">
                                        <div class="text-sm font-bold text-white line-through decoration-white/20">{{ $notif->titulo }}</div>
                                    </td>
                                    <td class="px-8 py-4 text-[10px] font-black text-white/30 uppercase tracking-widest">
                                        Publicado {{ $notif->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <button wire:click="apagar({{ $notif->id }})" 
                                                class="p-2 text-white/20 hover:text-red-500 transition-colors" 
                                                title="Eliminar Permanentemente" 
                                                onclick="confirm('Eliminar permanentemente este registo?') || event.stopImmediatePropagation()">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
