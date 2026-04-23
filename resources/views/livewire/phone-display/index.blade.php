<div class="min-h-screen flex flex-col" wire:poll.30s>

    @include('livewire.phone-display._header')
    @include('livewire.phone-display._login-modal')
    @include('livewire.phone-display._terms-modal')
    @include('livewire.phone-display._pin-change-modal')

    {{-- ── SCANNER OVERLAY (parent-managed) ────────────────────── --}}
    @if($scanning)
    <div class="px-3.5 pt-4 pb-1.5">
        <div class="bg-white/5 border border-white/10 rounded-xl p-3.5">
            <div class="flex justify-between items-center mb-2.5">
                <p class="text-white text-[0.85rem] font-bold m-0">Scanner de Equipamento</p>
                <button wire:click="$toggle('scanning')" class="bg-none border-none text-white/50 text-xl cursor-pointer">✕</button>
            </div>

            <div wire:ignore x-data="{
                    scanner: null,
                    loading: true,
                    startScanner() {
                        if (typeof Html5Qrcode === 'undefined') {
                            let script = document.createElement('script');
                            script.src = 'https://unpkg.com/html5-qrcode';
                            script.onload = () => this.initScanner();
                            document.head.appendChild(script);
                        } else {
                            this.initScanner();
                        }
                    },
                    initScanner() {
                        this.loading = false;
                        if (this.scanner) return;

                        this.scanner = new Html5Qrcode('qr-reader');
                        this.scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => {
                                this.scanner.stop();
                                $wire.set('assetToken', decodedText);
                                $wire.call('checkAsset');
                            },
                            (err) => {}
                        ).catch(err => {
                            console.error('Erro na câmera', err);
                            this.loading = false;
                            alert('Não foi possível aceder à câmera.');
                        });
                    }
                }" x-init="startScanner()" class="rounded-xl overflow-hidden mb-4 relative bg-black">
                <div id="qr-reader" class="w-full min-h-62.5"></div>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-black/80 text-white text-[0.8rem] z-10">A iniciar câmera...</div>
            </div>

            <p class="text-white text-xs mb-2">Ou introduza o código manualmente:</p>
            <div class="flex gap-2">
                <input wire:model="assetToken" type="text" placeholder="Código do token..."
                       class="flex-1 bg-black/30 border border-white/20 rounded-xl px-2.5 py-2.5 text-white text-[0.9rem]">
                <button wire:click="checkAsset" class="bg-blue-500 text-white font-bold px-4 py-2.5 rounded-[10px] text-[0.85rem] border-none cursor-pointer">
                    Validar
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(!$diaAtivo)
    {{-- ── SEM DIA ATIVO ──────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center gap-5 p-8">
        <img src="/images/procme_logo.svg" alt="CME" class="h-16 opacity-30">
        <p class="text-white text-base font-semibold text-center leading-relaxed">
            Painel indisponível.<br>Contacte o responsável de turno.
        </p>
    </div>

    @elseif($activeColaboradorId)

    {{-- ── NOTIFICAÇÕES GLOBAIS (parent-managed) ───────────────── --}}
    @include('livewire.phone-display._notifications')

    {{-- ── TAB: EQUIPA ─────────────────────────────────────────── --}}
    @if($activeTab === 'equipa')
        @livewire('phone-display.equipa-tab', ['colaboradorId' => $activeColaboradorId], key('equipa-'.$activeColaboradorId))
    @endif

    {{-- ── TAB: EPI ─────────────────────────────────────────────── --}}
    @if($activeTab === 'epi')
        @livewire('phone-display.epi-tab', ['colaboradorId' => $activeColaboradorId], key('epi-'.$activeColaboradorId))
    @endif

    {{-- ── TAB: GUIAS ───────────────────────────────────────────── --}}
    @if($activeTab === 'guias')
        @livewire('phone-display.guias-tab', ['colaboradorId' => $activeColaboradorId], key('guias-'.$activeColaboradorId))
    @endif

    {{-- ── TAB: SOS ─────────────────────────────────────────────── --}}
    @if($activeTab === 'sos')
        @livewire('phone-display.sos-tab', ['colaboradorId' => $activeColaboradorId], key('sos-'.$activeColaboradorId))
    @endif

    @else
    {{-- Not logged in — login modal handles the overlay --}}
    <div class="flex-1"></div>
    @endif

    @include('livewire.phone-display._footer')
</div>
