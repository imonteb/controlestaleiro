    {{-- ── LOGIN MODAL ─────────────────────────────────────────── --}}
    @if($isLoggingIn)
    <div class="fixed inset-0 z-[1000] flex flex-col items-center px-6 py-6 overflow-y-auto bg-linear-to-b from-blue-900 via-slate-900 to-[#09143b]">
        <div class="bg-slate-800 border border-white/10 rounded-3xl w-full max-width-[360px] p-8 relative shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] m-auto">
            @if($activeColaboradorId)
                <button wire:click="$set('isLoggingIn', false)" class="absolute top-5 right-5 bg-white/5 border-none text-white w-8 h-8 rounded-full flex items-center justify-center cursor-pointer">✕</button>
            @endif

            <div class="text-center mb-6">
                <div class="bg-white p-2.5 rounded-xl inline-block mb-4 shadow-[0_4px_12px_rgba(0,0,0,0.1)]">
                    <img src="/images/procme_logo.svg" alt="CME Logo" class="h-9">
                </div>
                <h3 class="text-[#FFD300] text-2xl font-black m-0 tracking-tight uppercase">Identificação</h3>
                <p class="text-white/60 text-sm mt-1">Unidade de Estaleiro C016</p>
            </div>

            <form wire:submit.prevent="loginColaborador" class="flex flex-col gap-4.5">
                <div class="flex flex-col gap-1.5">
                    <label class="text-white/50 text-[0.65rem] font-extrabold uppercase tracking-widest ml-1">Nº de Colaborador</label>
                    <input wire:model="loginNumero" type="text" placeholder="Ex: 1234..."
                           class="w-full bg-black/20 border border-white/10 rounded-[14px] px-3.5 py-3.5 text-white text-base outline-none transition-colors duration-200 focus:border-blue-500/50">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-white/50 text-[0.65rem] font-extrabold uppercase tracking-widest ml-1">PIN de Acesso</label>
                    <input wire:model="loginPin" type="password" maxlength="6" placeholder="••••••"
                           class="w-full bg-black/20 border border-white/10 rounded-[14px] px-3.5 py-3.5 text-white text-2xl tracking-[0.6em] text-center outline-none transition-colors duration-200 focus:border-blue-500/50">
                </div>

                @error('login')
                <div class="bg-red-500/10 border-l-[3px] border-red-500 px-3 py-2 text-red-300 text-xs font-semibold rounded-r">
                    {{ $message }}
                </div>
                @enderror

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 active:scale-[0.98] text-white font-black py-4 rounded-[14px] mt-1.5 border-none text-base tracking-[0.05em] cursor-pointer transition-all duration-200 shadow-[0_10px_20px_-5px_rgba(59,130,246,0.4)]">
                    ENTRAR
                </button>
            </form>
        </div>
    </div>
    @endif

