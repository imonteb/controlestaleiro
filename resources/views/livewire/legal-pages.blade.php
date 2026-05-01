<div class="min-h-screen bg-linear-to-b from-blue-900 via-blue-950 to-[#09143B] py-12 px-6 animate-in fade-in duration-700">
    <div class="max-w-5xl mx-auto space-y-10">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-blue-700/40 pb-10">
            <div class="flex items-center gap-6">
                <a href="{{ url()->previous() == route('legal') ? route('home') : url()->previous() }}" 
                   class="group flex items-center justify-center w-14 h-14 bg-blue-800 hover:bg-blue-700 text-white rounded-2xl transition-all shadow-2xl active:scale-95 border border-blue-600/30">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter uppercase leading-none">
                        Info <span class="text-yellow-500">Legal</span>
                    </h1>
                    <p class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em] mt-3 flex items-center gap-2">
                        <span class="w-4 h-[2px] bg-yellow-500"></span>
                        Unidade de Estaleiro C016
                    </p>
                </div>
            </div>
        </div>

        {{-- Interactive Tabs --}}
        <div class="inline-flex bg-blue-950/50 p-1.5 rounded-2xl border border-blue-800/50 backdrop-blur-xl">
            @foreach(['privacidade' => 'Privacidade', 'termos' => 'Termos', 'cookies' => 'Cookies'] as $key => $label)
                <button wire:click="$set('tab', '{{ $key }}')" 
                        class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 {{ $tab === $key ? 'bg-blue-600 text-white shadow-[0_0_30px_rgba(37,99,235,0.4)] scale-105' : 'text-white/30 hover:text-white/60' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Expert Legal Card --}}
        <div class="group relative bg-blue-900/40 backdrop-blur-2xl rounded-[2.5rem] border border-blue-700/40 overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] transition-all hover:border-blue-500/30">
            <!-- Decorative Accent -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[100px] -mr-32 -mt-32"></div>
            
            {{-- Header with Dynamic Label --}}
            <div class="bg-linear-to-r from-blue-800/80 to-blue-900/80 px-10 py-6 border-b border-blue-700/50 flex items-center justify-between">
                <span class="text-white font-black uppercase tracking-widest text-xs flex items-center gap-3">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    {{ $pagina->titulo }}
                </span>
                <span class="text-white/20 text-[10px] font-black uppercase tracking-widest">{{ $pagina->versao }} · Rev. {{ $pagina->ultima_revisao?->translatedFormat('F Y') }}</span>
            </div>

            {{-- Body Section --}}
            <div class="legal-content p-10 md:p-16">
                {!! $pagina->conteudo !!}
            </div>
            <style>
                .legal-content { color: rgba(255,255,255,0.75); font-size: 0.875rem; line-height: 1.75; }
                .legal-content h1,.legal-content h2 { color: #fff; font-size: 1rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; margin: 2.5rem 0 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
                .legal-content h3 { color: #93c5fd; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; margin: 1.75rem 0 0.5rem; }
                .legal-content p { margin: 0.75rem 0; }
                .legal-content ul,.legal-content ol { margin: 0.75rem 0 0.75rem 1.5rem; }
                .legal-content ul { list-style-type: disc; }
                .legal-content ol { list-style-type: decimal; }
                .legal-content li { margin: 0.4rem 0; }
                .legal-content strong { color: #fff; font-weight: 700; }
                .legal-content a { color: #60a5fa; text-decoration: underline; }
                .legal-content a:hover { color: #93c5fd; }
                .legal-content code { background: rgba(255,255,255,0.08); color: #93c5fd; padding: 0.1em 0.4em; border-radius: 4px; font-size: 0.8em; font-family: monospace; }
                .legal-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.8rem; }
                .legal-content th { background: rgba(255,255,255,0.05); color: #fff; font-weight: 800; text-align: left; padding: 0.6rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.15); }
                .legal-content td { padding: 0.55rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.06); vertical-align: top; }
                .legal-content tr:last-child td { border-bottom: none; }
                .legal-content blockquote { border-left: 3px solid #3b82f6; padding-left: 1rem; margin: 1rem 0; color: rgba(255,255,255,0.5); font-style: italic; }
            </style>
            
            {{-- Card Footer --}}
            <div class="bg-blue-950/50 px-10 py-6 border-t border-blue-800/50 text-center flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.4em]">
                    © 2024–2026 Construção e Manutenção Electromecânica S.A. · v1.0.0
                </p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-[9px] font-black text-white/50 uppercase tracking-widest">RGPD · Lei n.º 58/2019 · Direito Português</span>
                </div>
            </div>
        </div>
    </div>
</div>
