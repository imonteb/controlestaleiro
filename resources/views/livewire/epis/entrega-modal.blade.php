<div>
@if($open)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-blue-950 border border-blue-700 rounded-2xl w-full max-w-lg shadow-2xl flex flex-col"
         style="max-height:90vh; overflow-y:auto;">

        {{-- Header --}}
        <div class="flex items-center gap-4 p-6 border-b border-blue-800">
            <div class="size-12 bg-green-500/10 text-green-400 rounded-xl flex items-center justify-center shrink-0">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">Entrega Registada</h3>
                <p class="text-blue-300 text-sm">
                    @if($colaborador)
                        {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Items list --}}
        <div class="px-6 pt-4 pb-2">
            <p class="text-xs text-blue-400 font-bold uppercase tracking-wider mb-2">EPIs entregues</p>
            <div class="flex flex-col gap-1.5">
                @foreach($entregas as $e)
                <div class="flex items-center justify-between bg-white/5 rounded-lg px-3 py-2">
                    <span class="text-yellow-400 font-bold text-sm">{{ $e->epiItem->nombre }}</span>
                    @if($e->talla)
                        <span class="text-[10px] px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded-md">{{ $e->talla }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Modo selector --}}
        @if(!$signatureToken)
        <div class="px-6 pt-4 pb-2">
            <p class="text-xs text-blue-400 font-bold uppercase tracking-wider mb-3">Como pretende recolher a assinatura?</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" wire:click="$set('modo', 'agora')"
                    class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-sm font-bold
                        {{ $modo === 'agora' ? 'border-green-500 bg-green-500/10 text-green-400' : 'border-white/10 bg-white/5 text-white/60 hover:border-white/30' }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span class="text-[11px] text-center leading-tight">Assinar<br>agora</span>
                </button>
                <button type="button" wire:click="selecionarRemoto"
                    class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-sm font-bold
                        {{ $modo === 'remota' ? 'border-blue-500 bg-blue-500/10 text-blue-400' : 'border-white/10 bg-white/5 text-white/60 hover:border-white/30' }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h3m-3 3h3m7 4V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z"/>
                    </svg>
                    <span class="text-[11px] text-center leading-tight">QR /<br>WhatsApp</span>
                </button>
                <button type="button" wire:click="$set('modo', 'nenhuma')"
                    class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-sm font-bold
                        {{ $modo === 'nenhuma' ? 'border-gray-500 bg-gray-500/10 text-gray-300' : 'border-white/10 bg-white/5 text-white/60 hover:border-white/30' }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-[11px] text-center leading-tight">Sem<br>assinatura</span>
                </button>
            </div>
        </div>

        {{-- Modo: Assinar agora (canvas) --}}
        @if($modo === 'agora')
        <div class="px-6 pb-4" x-data="{
            pad: null,
            hasSig: false,
            init() {
                this.$nextTick(() => { this.setup(); });
            },
            setup() {
                const canvas = this.$refs.canvas;
                if (!canvas) return;
                const load = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    this.pad = new SignaturePad(canvas, { backgroundColor: 'rgba(0,0,0,0)', penColor: 'rgb(30,64,175)', minWidth: 1.5, maxWidth: 3.5 });
                    this.pad.addEventListener('endStroke', () => {
                        this.hasSig = true;
                        $wire.set('assinaturaBase64', this.pad.toSVG());
                    });
                };
                if (typeof SignaturePad === 'undefined') {
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
                    s.onload = load;
                    document.head.appendChild(s);
                } else { load(); }
            }
        }" wire:ignore>
            <p class="text-xs text-blue-400 font-bold uppercase tracking-wider mb-2 mt-2">Assinatura do colaborador</p>
            <div class="bg-white rounded-xl border-2 border-dashed border-gray-300" style="height:160px; position:relative;">
                <canvas x-ref="canvas" class="w-full h-full rounded-xl cursor-crosshair touch-none" @mousedown.stop @touchstart.stop @pointerdown.stop></canvas>
                <button type="button" x-show="hasSig"
                    @click="pad.clear(); hasSig = false; $wire.set('assinaturaBase64', null)"
                    class="absolute top-2 right-2 text-[10px] font-bold text-gray-400 hover:text-red-500 bg-white/80 px-2 py-0.5 rounded">
                    Limpar
                </button>
                <p x-show="!hasSig" class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm pointer-events-none">
                    Assinar aqui
                </p>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" wire:click="confirmarSemAssinatura"
                    class="flex-1 py-2.5 px-4 rounded-xl border border-white/10 text-white/60 text-sm font-bold hover:bg-white/5 transition-colors">
                    Saltar
                </button>
                <button type="button" wire:click="guardarAssinaturaPresencial" x-bind:disabled="!hasSig"
                    class="flex-1 py-2.5 px-4 rounded-xl bg-green-600 text-white text-sm font-bold hover:bg-green-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    Confirmar Assinatura
                </button>
            </div>
        </div>

        {{-- Modo: Remota — gera automaticamente ao selecionar --}}
        @elseif($modo === 'remota')
        <div class="px-6 pb-4 flex justify-center py-4">
            <div class="flex items-center gap-2 text-blue-400 animate-pulse">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                <span class="text-sm font-bold">A gerar link...</span>
            </div>
        </div>

        {{-- Modo: Sem assinatura --}}
        @elseif($modo === 'nenhuma')
        <div class="px-6 pb-6">
            <p class="text-gray-400 text-sm mt-3 mb-4">A entrega será registada sem assinatura digital. Pode ser solicitada posteriormente.</p>
            <button type="button" wire:click="confirmarSemAssinatura"
                class="w-full py-3 rounded-xl bg-gray-600 hover:bg-gray-700 text-white font-bold text-sm transition-colors">
                Confirmar sem Assinatura
            </button>
        </div>
        @endif

        {{-- Modo: QR ativo (aguardando assinatura remota) --}}
        @else
        <div class="px-6 pb-6 flex flex-col items-center text-center" wire:poll.3s="checkSignatureStatus">
            <div class="bg-white p-3 rounded-2xl my-4 shadow-xl">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('signature.show', $signatureToken)) }}"
                     alt="QR Code" width="200" height="200">
            </div>
            <div class="flex items-center gap-2 text-blue-400 animate-pulse mb-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                <span class="text-sm font-bold uppercase tracking-widest">Aguardando Assinatura</span>
            </div>
            <p class="text-xs text-gray-400 mb-4">O colaborador deve ler o código com a câmara do telemóvel.</p>

            <div class="flex items-center gap-2 w-full mb-3">
                <input type="text" readonly value="{{ route('signature.show', $signatureToken) }}"
                       class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-gray-300 outline-none">
                <button type="button"
                    x-on:click="window.navigator.clipboard.writeText('{{ route('signature.show', $signatureToken) }}'); $flux?.toast?.('Link copiado!')"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 text-white p-2 rounded-lg transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>

            @if($colaborador && $colaborador->telefone)
            @php
                $waItems = $entregas->map(fn($e) => $e->epiItem->nombre.($e->talla ? ' ('.$e->talla.')' : ''))->join(', ');
                $waText = 'Olá '.$colaborador->nombre.', por favor assine a receção dos EPIs ('.$waItems.') neste link: '.route('signature.show', $signatureToken);
                $waPhone = preg_replace('/[^0-9]/', '', $colaborador->telefone);
            @endphp
            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waText) }}" target="_blank"
               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-green-500/30 text-green-400 hover:bg-green-500/10 text-sm font-bold transition-colors mb-4">
                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Partilhar via WhatsApp
            </a>
            @endif

            <button type="button" wire:click="confirmarSemAssinatura"
                class="text-[10px] text-gray-500 uppercase font-black hover:text-red-400 transition-colors tracking-tighter">
                Fechar sem aguardar
            </button>
        </div>
        @endif

    </div>
</div>
@endif
</div>
