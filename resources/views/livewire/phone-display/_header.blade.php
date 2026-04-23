{{-- ── HEADER ─────────────────────────────────────────────── --}}
<div class="sticky top-0 z-[6000] bg-blue-700 border-b border-white/20 px-4 py-3.5">
    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2">
        {{-- Esquerda: Logo e Info --}}
        <div class="flex items-center gap-2 min-w-0">
            <img src="/images/procme_logo.svg" alt="CME" class="h-7 bg-white rounded-md px-1.5 py-0.5 shrink-0">
            <div class="min-w-0">
                <div class="text-yellow-300 text-[0.6rem] font-black tracking-[0.04em] uppercase leading-none mb-0.5">
                    CME C016
                </div>
                @if($activeColaboradorId)
                @php $col = \App\Models\Colaborador::find($activeColaboradorId); @endphp
                <div class="text-white text-xs font-bold truncate">
                    {{ explode(' ', $col->nombre)[0] }}
                </div>
                @else
                <div class="text-white text-[0.7rem] font-semibold">Sem Login</div>
                @endif
            </div>
        </div>

        {{-- Centro: Botão Plano Pastor --}}
        <div class="flex justify-center  border border-white/20 rounded-xl  ">
            <a href="https://apps.cme.pt/ppd/" target="_blank"
                class="bg-blue-500 text-white text-[0.65rem] font-black px-2.5 py-1.5 rounded-[10px] border border-white no-underline flex flex-col items-center leading-[1.1] shrink-0 shadow-[0_0_15px_rgba(16,185,129,0.1)] transition-all duration-200">
                <span class="text-[0.55rem] opacity-80">LINK</span>
                <span class="tracking-[0.02em]">P. PASTOR</span>
            </a>
        </div>

        {{-- Direita: Ações --}}
        <div class="flex items-center justify-end gap-1.5 shrink-0">
            @if(!$activeColaboradorId)
            <button wire:click="$set('isLoggingIn', true)"
                class="bg-blue-500 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg border-none">ENTRAR</button>
            @endif

            <button @click="$wire.set('scanning', ! $wire.scanning)"
                class="{{ $scanning ? 'bg-amber-500' : 'bg-amber-500' }} border border-white/20 text-white p-2 rounded-lg">
                <div class="flex gap-1">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h3m-3 3h3m7 4V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z" />
                    </svg>
                    <span class="text-xs">Scanner</span>
                </div>
            </button>

            @if($activeColaboradorId)
            <button wire:click="logoutColaborador"
                class="bg-red-500/15 border border-red-500/30 text-red-300 p-2 rounded-lg flex items-center justify-center"
                title="Sair">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>

{{-- ── PENDING SIGNATURES ALERT ────────────────────────────── --}}
@if($activeColaboradorId && isset($assinaturasPendentes) && $assinaturasPendentes->count() > 0)
<div class="px-3.5 pt-2.5">
    @foreach($assinaturasPendentes as $sig)
    <div class="relative mb-2">
        <a href="{{ route('signature.show', $sig->token) }}"
            class="flex items-center gap-3 bg-red-500/15 border border-red-500/40 rounded-xl p-3 no-underline animate-[pulse-border_2s_infinite]">
            <div class="bg-red-500 text-white w-8 h-8 rounded-lg flex items-center justify-center text-base shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div class="flex-1">
                @php
                $sigItems = $sig->metadata['items'] ?? null;
                $sigTitle = $sigItems && count($sigItems) > 1
                    ? count($sigItems).' EPIs pendentes de assinatura'
                    : 'Firma Pendente: '.($sigItems[0]['nombre'] ?? $sig->signable?->epiItem?->nombre ?? 'EPI');
            @endphp
                <div class="text-white text-[0.8rem] font-extrabold">{{ $sigTitle }}</div>
                <div class="text-red-300 text-[0.65rem] font-semibold">Por favor, assine a receção no telemóvel.</div>
            </div>
            <div class="text-white text-xl opacity-50">→</div>
        </a>
        <button wire:click.prevent="dismissAlert('signature', {{ $sig->id }})"
            class="absolute top-1.5 right-1.5 bg-black/30 border border-white/20 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] cursor-pointer z-10"
            title="Descartar">✕</button>
    </div>
    @endforeach
</div>
<style>
    @keyframes pulse-border {
        0% {
            border-color: rgba(239, 68, 68, 0.4);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.2);
        }

        50% {
            border-color: rgba(239, 68, 68, 0.8);
            box-shadow: 0 0 10px 0 rgba(239, 68, 68, 0.4);
        }

        100% {
            border-color: rgba(239, 68, 68, 0.4);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.2);
        }
    }
</style>
@endif
