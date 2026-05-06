@if($showPinChange)
{{-- ── MODAL ALTERAR PIN ─────────────────────────────────────── --}}
<div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[7000] flex items-center justify-center px-5">
    <div class="bg-slate-800 border border-white/15 rounded-3xl w-full max-w-sm p-6 shadow-[0_25px_50px_rgba(0,0,0,0.5)]">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-white text-lg font-black m-0 leading-none">Alterar PIN</h3>
                <p class="text-white/50 text-xs mt-1 m-0">PIN de 6 dígitos</p>
            </div>
            @if(! $showForcePinChange)
            <button wire:click="$set('showPinChange', false)"
                class="bg-white/10 border-none text-white w-8 h-8 rounded-2xl flex items-center justify-center text-sm cursor-pointer">✕</button>
            @endif
        </div>

        <form wire:submit="alterarPin" class="flex flex-col gap-3.5">
            @if($showForcePinChange)
            <div class="bg-yellow-500/20 border border-yellow-400/40 rounded-xl p-3 text-center">
                <p class="text-yellow-300 text-xs font-bold m-0">🔐 Por segurança, define um novo PIN de 6 dígitos</p>
            </div>
            @endif

            <div x-data="{ show: false }">
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">PIN Atual</label>
                <div class="relative">
                    <input wire:model="pinAtual"
                        :type="show ? 'text' : 'password'" inputmode="numeric" maxlength="6" autocomplete="current-password"
                        placeholder="••••••"
                        class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 pr-11 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80 transition-colors border-none bg-transparent cursor-pointer p-1">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('pinAtual')
                    <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <div x-data="{ show: false }">
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">Novo PIN</label>
                <div class="relative">
                    <input wire:model="pinNovo"
                        :type="show ? 'text' : 'password'" inputmode="numeric" maxlength="6" autocomplete="new-password"
                        placeholder="••••••"
                        class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 pr-11 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80 transition-colors border-none bg-transparent cursor-pointer p-1">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('pinNovo')
                    <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <div x-data="{ show: false }">
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">Confirmar Novo PIN</label>
                <div class="relative">
                    <input wire:model="pinNovoConfirmacao"
                        :type="show ? 'text' : 'password'" inputmode="numeric" maxlength="6" autocomplete="new-password"
                        placeholder="••••••"
                        class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 pr-11 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white/80 transition-colors border-none bg-transparent cursor-pointer p-1">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('pinNovoConfirmacao')
                    <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="bg-yellow-300 text-slate-900 font-black py-3.5 rounded-xl text-sm uppercase tracking-widest shadow-[0_4px_15px_rgba(253,224,71,0.3)] cursor-pointer border-none mt-1">
                Guardar PIN
            </button>
        </form>
    </div>
</div>
@endif
