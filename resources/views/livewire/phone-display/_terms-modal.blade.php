    {{-- ── MODAL NORMAS 1.ª SESSÃO (terms_accepted_at null) ─── --}}
    @if($showTermsModal)
    <div class="fixed inset-0 z-[1100] flex flex-col items-center px-6 py-6 overflow-y-auto bg-linear-to-b from-blue-900 via-slate-900 to-[#09143b]">
        <div class="bg-slate-800 border border-yellow-300/30 rounded-3xl w-full max-w-sm p-8 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] m-auto">

            <div class="text-center mb-6">
                <div class="bg-yellow-300/10 border-2 border-yellow-300 w-15 h-15 rounded-2xl flex items-center justify-center mx-auto mb-4 text-4xl">📋</div>
                <h3 class="text-[#FFD300] text-xl font-black m-0 mb-2 tracking-tight uppercase">Normas de Uso</h3>
                <p class="text-white/60 text-[0.8rem] m-0">Leia e aceite as normas antes de continuar</p>
            </div>

            <div class="bg-white/4 border border-white/8 rounded-2xl p-4.5 mb-5 flex flex-col gap-2.5">
                <div class="flex gap-2.5 items-start">
                    <span class="text-yellow-300 text-base shrink-0">⚠️</span>
                    <p class="text-white text-[0.78rem] leading-relaxed m-0">O acesso a esta plataforma é <strong class="text-yellow-300">exclusivamente profissional</strong>. Todos os registos efetuados têm <strong class="text-yellow-300">valor declarativo oficial</strong>.</p>
                </div>
                <div class="flex gap-2.5 items-start">
                    <span class="text-yellow-300 text-base shrink-0">🔐</span>
                    <p class="text-white text-[0.78rem] leading-relaxed m-0">O seu PIN é pessoal e intransmissível. Qualquer registo efetuado sob o seu número é da sua responsabilidade.</p>
                </div>
                <div class="flex gap-2.5 items-start">
                    <span class="text-yellow-300 text-base shrink-0">📊</span>
                    <p class="text-white/80 text-[0.72rem] leading-snug m-0">Os seus dados de acesso (IP, horário de entrada e saída) são registados para efeitos de segurança, nos termos da <a href="/legal" target="_blank" class="text-blue-300">Política de Privacidade</a> da CME.</p>
                </div>
            </div>

            <button wire:click="acceptFirstTerms"
                    class="w-full bg-yellow-300 text-blue-900 font-black py-4 rounded-[14px] border-none text-base tracking-[0.03em] cursor-pointer shadow-[0_10px_20px_-5px_rgba(253,224,71,0.4)]">
                ✅ Li e Aceito as Normas de Uso
            </button>

            <p class="text-center text-white/30 text-[0.65rem] mt-3">Esta confirmação é registada com data e hora nos termos do RGPD.</p>
        </div>
    </div>
    @endif
