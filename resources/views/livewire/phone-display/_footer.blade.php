    {{-- ── TAB BAR ──────────────────────────────────────────────── --}}
    @if($activeColaboradorId && !$showTermsModal && !$isLoggingIn)
    <div class="fixed bottom-0 left-0 right-0 bg-blue-700 border-t border-white/20 z-5000 shadow-[0_-10px_20px_rgba(0,0,0,0.4)]"
         style="padding-bottom: env(safe-area-inset-bottom, 0px);">
        <div class="flex items-stretch">

            {{-- Equipa --}}
            <button wire:click="setTab('equipa')"
                @class([
                    'flex-1 flex flex-col items-center justify-center gap-0.5 pt-2.5 pb-1.5 px-1 border-y-0 border-l-0 border-r border-r-white/20 bg-transparent relative',
                    'text-yellow-300' => $activeTab === 'equipa',
                    'text-white/70'   => $activeTab !== 'equipa',
                ])>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-[0.55rem] font-black uppercase tracking-[0.04em]">Equipa</span>
                @if($activeTab === 'equipa')
                <div class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-green-400"></div>
                @endif
            </button>

            {{-- EPI --}}
            <button wire:click="setTab('epi')"
                @class([
                    'flex-1 flex flex-col items-center justify-center gap-0.5 pt-2.5 pb-1.5 px-1 border-y-0 border-l-0 border-r border-r-white/20 bg-transparent relative',
                    'text-yellow-300' => $activeTab === 'epi',
                    'text-white/70'   => $activeTab !== 'epi',
                ])>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-[0.55rem] font-black uppercase tracking-[0.04em]">EPI</span>
                @if($activeTab === 'epi')
                <div class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-green-400"></div>
                @endif
            </button>

            {{-- Guias --}}
            <button wire:click="setTab('guias')"
                @class([
                    'flex-1 flex flex-col items-center justify-center gap-0.5 pt-2.5 pb-1.5 px-1 border-y-0 border-l-0 border-r border-r-white/20 bg-transparent relative',
                    'text-yellow-300' => $activeTab === 'guias',
                    'text-white/70'   => $activeTab !== 'guias',
                ])>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[0.55rem] font-black uppercase tracking-[0.04em]">Guias</span>
                @if($activeTab === 'guias')
                <div class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-green-400"></div>
                @endif
            </button>

            {{-- SOS --}}
            <button wire:click="setTab('sos')"
                @class([
                    'flex-1 flex flex-col items-center justify-center gap-0.5 pt-2.5 pb-1.5 px-1 border-none bg-transparent relative',
                    'text-red-400'  => $activeTab === 'sos',
                    'text-white/70' => $activeTab !== 'sos',
                ])>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-[0.55rem] font-black uppercase tracking-[0.04em]">SOS</span>
                @if($activeTab === 'sos')
                <div class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-green-400"></div>
                @endif
            </button>

        </div>
    </div>
    @endif

    {{-- ── CONFIRM MODAL ───────────────────────────────────────── --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-9999 flex items-center justify-center px-6">
        <div class="bg-slate-800 border border-yellow-300/30 rounded-3xl w-full max-w-85 p-6 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] text-center">
            <div class="bg-yellow-300/10 text-yellow-300 w-15 h-15 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl border-2 border-yellow-300">
                ⚠️
            </div>
            <h3 class="text-white text-[1.15rem] font-black m-0 mb-2.5 uppercase tracking-[0.02em]">Atenção!</h3>
            <p class="text-white/70 text-[0.85rem] leading-snug m-0 mb-5">
                Tem outro painel aberto. Ao continuar, fechará o painel atual. Deseja mudar de ecrã?
            </p>
            <div class="flex gap-2.5">
                <button wire:click="cancelModalSwitch" class="flex-1 bg-white/10 text-white border-none py-3 rounded-xl font-bold text-[0.9rem] cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="confirmModalSwitch" class="flex-1 bg-yellow-300 text-slate-800 border-none py-3 rounded-xl font-extrabold text-[0.9rem] shadow-[0_4px_15px_rgba(253,224,71,0.4)] cursor-pointer">
                    Sim, continuar
                </button>
            </div>
        </div>
    </div>
    @endif
