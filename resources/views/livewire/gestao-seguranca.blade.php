<div class="px-6 py-8 w-full max-w-7xl mx-auto space-y-10 animate-in fade-in duration-500">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-blue-700/30 pb-10">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase leading-none">
                Gestão de <span class="text-yellow-500">Segurança</span> & Apoio
            </h1>
            <p class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em] flex items-center gap-2">
                <span class="w-4 h-[2px] bg-yellow-500"></span>
                Controlo de Incidentes e Protocolos C016
            </p>
        </div>
        <div class="flex gap-4">
            @if($activeTab === 'contactos')
                <button wire:click="openModal" class="bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black px-6 py-3 rounded-xl transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-2 shadow-xl shadow-yellow-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Novo Contacto
                </button>
            @elseif($activeTab === 'procedimentos')
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-xl transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-2 shadow-xl shadow-blue-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Novo Protocolo
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl flex items-center gap-4 animate-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Professional Tabs Navigation --}}
    <div class="flex flex-wrap bg-blue-900/20 p-1.5 rounded-2xl border border-blue-700/20 w-fit backdrop-blur-md">
        <button wire:click="$set('activeTab', 'contactos')" 
                class="px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ $activeTab === 'contactos' ? 'bg-blue-600 text-white shadow-xl scale-105' : 'text-white/40 hover:text-white/70' }}">
            Contactos Emergência
        </button>
        <button wire:click="$set('activeTab', 'procedimentos')" 
                class="px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ $activeTab === 'procedimentos' ? 'bg-blue-600 text-white shadow-xl scale-105' : 'text-white/40 hover:text-white/70' }}">
            Procedimentos
        </button>
        <button wire:click="$set('activeTab', 'incidentes')" 
                class="px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ $activeTab === 'incidentes' ? 'bg-blue-600 text-white shadow-xl scale-105' : 'text-white/40 hover:text-white/70' }}">
            Relatórios
        </button>
    </div>

    {{-- Content Area --}}
    <div class="space-y-10">
        {{-- TAB: CONTACTOS --}}
        @if($activeTab === 'contactos')
        <div class="animate-in fade-in slide-in-from-left-4 duration-500">
            <div class="bg-blue-900/30 backdrop-blur-md rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-blue-800/50">
                                <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Ordem</th>
                                <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Entidade / Contacto</th>
                                <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Notas</th>
                                <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-800/30">
                            @forelse($contactos as $c)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-8 py-6 text-sm text-white/50 font-black italic">{{ sprintf('%02d', $c->ordem) }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        @if($c->logo)
                                            <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/10 overflow-hidden shrink-0 flex items-center justify-center p-1.5 backdrop-blur-sm">
                                                <img src="{{ filter_var($c->logo, FILTER_VALIDATE_URL) ? $c->logo : Storage::url($c->logo) }}" alt="{{ $c->nome }}" class="w-full h-full object-contain">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-blue-600/20 border border-blue-500/20 shrink-0 flex items-center justify-center text-blue-400">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-black text-white group-hover:text-yellow-500 transition-colors uppercase tracking-tight">{{ $c->nome }}</div>
                                            <div class="text-lg font-black text-white/40 mt-1 tabular-nums">{{ $c->telefone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm text-white/60 font-medium italic">{{ $c->descricao ?: '—' }}</td>
                                <td class="px-8 py-6 text-right space-x-4">
                                    <button wire:click="editarContato({{ $c->id }})" class="text-blue-400 hover:text-white transition-colors text-xs font-black uppercase tracking-widest">Editar</button>
                                    <button wire:click="apagarContato({{ $c->id }})" onclick="confirm('Apagar este contacto?') || event.stopImmediatePropagation()" class="text-red-400/50 hover:text-red-400 transition-colors text-xs font-black uppercase tracking-widest">Eliminar</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="text-white/20 text-[10px] font-black uppercase tracking-[0.6em]">Sem contactos registados</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: PROCEDIMENTOS --}}
        @if($activeTab === 'procedimentos')
        <div class="animate-in fade-in slide-in-from-left-4 duration-500 space-y-6">
            @forelse($procedimentos as $p)
            <div wire:key="proc-item-{{ $p->id }}" class="bg-blue-900/30 backdrop-blur-md rounded-3xl border border-blue-700/40 p-8 hover:border-blue-500/50 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 blur-3xl -mr-16 -mt-16 z-0"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="space-y-4 flex-1">
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg text-white @if($p->tipo=='acidente') bg-red-600 @elseif($p->tipo=='transito') bg-yellow-500 text-[#09143B]! @elseif($p->tipo=='seguro') bg-green-600 @else bg-blue-700 @endif">
                                {{ $p->tipo }}
                            </span>
                            <div class="flex flex-col">
                                <h3 class="text-sm text-blue-400 font-bold uppercase tracking-widest">{{ $p->ordem }}. {{ $p->titulo }}</h3>
                                @if($p->subtitulo)
                                    <span class="text-lg font-black text-white group-hover:text-yellow-500 transition-colors uppercase tracking-tight">{{ $p->subtitulo }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-white/60 text-sm leading-relaxed whitespace-pre-line bg-blue-950/20 p-6 rounded-2xl border border-blue-800/30 italic">
                            {{ $p->conteudo }}
                        </div>
                    </div>
                    <div class="flex items-center md:flex-col gap-3">
                        <button wire:click="editarProcedimento({{ $p->id }})" class="p-3 bg-white/5 hover:bg-white/10 text-white rounded-xl border border-white/5 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                        <button wire:click="apagarProcedimento({{ $p->id }})" onclick="confirm('Eliminar este protocolo?') || event.stopImmediatePropagation()" class="p-3 bg-red-900/20 hover:bg-red-900/40 text-red-400 rounded-xl border border-red-500/10 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-20 text-center bg-blue-900/10 rounded-[3rem] border border-blue-800/20 italic text-white/20 text-xs font-black uppercase tracking-widest">
                Nenhum procedimento operacional publicado.
            </div>
            @endforelse
        </div>
        @endif

        {{-- TAB: INCIDENTES --}}
        @if($activeTab === 'incidentes')
        <div class="bg-blue-900/30 backdrop-blur-md rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-2xl animate-in fade-in slide-in-from-left-4 duration-500">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-800/50">
                            <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Cronologia</th>
                            <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Colaborador / Risco</th>
                            <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Localização & Factos</th>
                            <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest">Evidência</th>
                            <th class="px-8 py-5 text-[10px] font-black text-blue-400 uppercase tracking-widest text-right">Estado / Gestão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-800/30">
                        @forelse($incidentes as $inc)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-white italic">{{ $inc->data_hora->format('d/m/Y') }}</div>
                                <div class="text-[10px] font-black text-white/30 uppercase tracking-widest mt-1">{{ $inc->data_hora->format('H:i') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-white group-hover:text-yellow-500 transition-colors uppercase tracking-tight">
                                    {{ $inc->colaborador->nome }} {{ $inc->colaborador->apellido }}
                                </div>
                                <span class="inline-flex mt-2 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-white @if($inc->tipo=='acidente_trabalho') bg-red-600 @elseif($inc->tipo=='acidente_viacao') bg-yellow-600 @elseif($inc->tipo=='quase_acidente') bg-orange-600 @else bg-blue-600 @endif">
                                    {{ str_replace('_', ' ', $inc->tipo) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2 text-xs font-black text-blue-400 uppercase mb-2 italic">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $inc->localizacao }}
                                </div>
                                <p class="text-sm text-white/60 leading-relaxed font-medium line-clamp-2 max-w-sm" title="{{ $inc->descricao }}">{{ $inc->descricao }}</p>
                            </td>
                            <td class="px-8 py-6">
                                @if($inc->photo_path)
                                    <a href="{{ Storage::url($inc->photo_path) }}" target="_blank" class="p-3 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 rounded-2xl border border-blue-500/20 transition-all flex items-center justify-center w-12 h-12 group/img">
                                        <svg class="w-5 h-5 group-hover/img:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </a>
                                @else
                                    <span class="text-white/10 text-xs font-black uppercase tracking-widest italic">N/D</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right space-y-3">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border @if($inc->estado=='novo') bg-red-600/20 text-red-400 border-red-500/30 @elseif($inc->estado=='em_analise') bg-yellow-600/20 text-yellow-500 border-yellow-500/30 @else bg-green-600/20 text-green-400 border-green-500/30 @endif">
                                    {{ str_replace('_', ' ', $inc->estado) }}
                                </span>
                                @if($inc->estado != 'resolvido')
                                    <select wire:change="alterarEstadoIncidente({{ $inc->id }}, $event.target.value)" class="block ml-auto text-[10px] font-black bg-blue-950 border-blue-700 text-white rounded-lg focus:ring-yellow-500 focus:border-yellow-500 p-1.5 px-3 transition-all cursor-pointer">
                                        <option value="" disabled selected>GERIR ESTADO</option>
                                        @if($inc->estado == 'novo')
                                            <option value="em_analise">MARCAR EM ANÁLISE</option>
                                        @endif
                                        <option value="resolvido">MARCAR RESOLVIDO</option>
                                    </select>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="text-white/20 text-[10px] font-black uppercase tracking-[0.5em]">Nenhum reporte via App</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($incidentes->hasPages())
            <div class="px-8 py-6 bg-blue-950/50 border-t border-blue-800/30">
                {{ $incidentes->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- MODAL SYSTEM --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#09143B]/80 backdrop-blur-xl animate-in fade-in duration-300">
        <div class="bg-blue-900/40 backdrop-blur-2xl rounded-[3rem] border border-blue-700/40 overflow-hidden shadow-2xl w-full max-w-2xl animate-in zoom-in-95 duration-300">
            <div class="bg-linear-to-r from-blue-800 to-blue-900 px-10 py-6 border-b border-blue-700/50 flex justify-between items-center">
                <span class="text-white font-black uppercase tracking-widest text-sm flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full {{ $activeTab === 'contactos' ? 'bg-yellow-500' : 'bg-blue-400' }}"></div>
                    @if($activeTab === 'contactos')
                        {{ $contato_id ? 'Editar Registo' : 'Novo Contacto' }}
                    @else
                        {{ $procedimento_id ? 'Editar Protocolo' : 'Novo Protocolo' }}
                    @endif
                </span>
                <button wire:click="closeModal" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-10 max-h-[70vh] overflow-y-auto custom-scrollbar">
                @if($activeTab === 'contactos')
                    <form wire:submit.prevent="salvarContato" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Nome / Entidade</label>
                                <input type="text" wire:model="nome" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                                @error('nome') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Telefone / Contacto</label>
                                <input type="text" wire:model="telefone" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                                @error('telefone') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Descrição curta</label>
                            <input type="text" wire:model="descricao" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Ordem Visual</label>
                                <input type="number" wire:model="ordem" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Link do Logótipo (URL)</label>
                                <input type="text" wire:model="logo" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                        </div>
                        <div class="flex gap-4 pt-6 mt-6 border-t border-white/5">
                            <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-[#09143B] font-black py-4 rounded-2xl shadow-xl shadow-yellow-500/20 transition-all active:scale-95 text-xs uppercase tracking-widest">
                                {{ $contato_id ? 'Confirmar Alterações' : 'Guardar Contacto' }}
                            </button>
                        </div>
                    </form>
                @else
                    <form wire:submit.prevent="salvarProcedimento" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Título do Procedimento</label>
                            <input type="text" wire:model="titulo" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            @error('titulo') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Subtítulo / Contexto</label>
                            <input type="text" wire:model="subtitulo" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Categoria de Risco</label>
                                <select wire:model="tipo_proc" class="w-full bg-blue-900 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                                    <option value="geral">Instruções Gerais</option>
                                    <option value="acidente">Acidente de Trabalho</option>
                                    <option value="transito">Acidente de Viação</option>
                                    <option value="seguro">Seguros e Apólices</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Ordem Visual</label>
                                <input type="number" wire:model="ordem_proc" class="w-full bg-white/5 border-blue-700/50 rounded-xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Corpo das Instruções</label>
                            <textarea wire:model="conteudo" rows="12" class="w-full bg-white/5 border-blue-700/50 rounded-2xl text-white text-sm focus:ring-yellow-500 focus:border-yellow-500 transition-all leading-relaxed"></textarea>
                            @error('conteudo') <span class="text-[10px] text-red-400 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex gap-4 pt-6 mt-6 border-t border-white/5">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 text-xs uppercase tracking-widest">
                                {{ $procedimento_id ? 'Atualizar Protocolo' : 'Publicar Protocolo' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
