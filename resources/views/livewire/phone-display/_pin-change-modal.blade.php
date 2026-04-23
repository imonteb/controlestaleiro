@if($showPinChange)
{{-- ── MODAL ALTERAR PIN ─────────────────────────────────────── --}}
<div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[7000] flex items-center justify-center px-5">
    <div class="bg-slate-800 border border-white/15 rounded-3xl w-full max-w-sm p-6 shadow-[0_25px_50px_rgba(0,0,0,0.5)]">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-white text-lg font-black m-0 leading-none">Alterar PIN</h3>
                <p class="text-white/50 text-xs mt-1 m-0">PIN de 4 dígitos</p>
            </div>
            <button wire:click="$set('showPinChange', false)"
                class="bg-white/10 border-none text-white w-8 h-8 rounded-2xl flex items-center justify-center text-sm cursor-pointer">✕</button>
        </div>

        <form wire:submit="alterarPin" class="flex flex-col gap-3.5">
            <div>
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">PIN Atual</label>
                <input wire:model="pinAtual"
                    type="password" inputmode="numeric" maxlength="4" autocomplete="current-password"
                    placeholder="••••"
                    class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
                @error('pinAtual')
                    <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">Novo PIN</label>
                <input wire:model="pinNovo"
                    type="password" inputmode="numeric" maxlength="4" autocomplete="new-password"
                    placeholder="••••"
                    class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
                @error('pinNovo')
                    <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-yellow-300/80 text-[0.6rem] font-black uppercase tracking-widest mb-1.5 block">Confirmar Novo PIN</label>
                <input wire:model="pinNovoConfirmacao"
                    type="password" inputmode="numeric" maxlength="4" autocomplete="new-password"
                    placeholder="••••"
                    class="w-full bg-white/6 border border-white/15 rounded-xl px-4 py-3 text-white text-xl text-center font-black tracking-[0.5em] outline-none">
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
