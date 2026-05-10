<div class="flex flex-col gap-6 w-full max-w-6xl mx-auto" x-data="{
    signaturePad: null,
    hasSignature: false,
    showSignatureModal: @entangle('showSignatureModal'),
    init() {
        this.$watch('showSignatureModal', value => {
            if (value) {
                this.$nextTick(() => { this.setupPad(); });
            }
        });
        window.addEventListener('signature-ready', (e) => {
            if (this.signaturePad && e.detail.signature) {
                this.signaturePad.fromDataURL(e.detail.signature);
                this.hasSignature = true;
            }
        });
    },
    setupPad() {
        if (typeof SignaturePad === 'undefined') {
            let script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
            script.onload = () => { this.doSetup(); };
            document.head.appendChild(script);
        } else {
            this.doSetup();
        }
    },
    doSetup() {
        let canvas = this.$refs.signatureCanvas;
        if (!canvas) return;
        const resize = () => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        };
        resize();
        this.signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(0,0,0,0)',
            penColor: 'rgb(30,64,175)'
        });
        this.signaturePad.addEventListener('endStroke', () => {
            this.hasSignature = true;
            @this.set('assinaturaRetroativaBase64', this.signaturePad.toSVG());
        });
        window.addEventListener('resize', resize);
    }
}">

    {{-- Page Header --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div style="background:#09143B;" class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="hand-raised" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Entregas de EPI</span>
            </div>
            <div class="flex flex-wrap gap-2">
                {{-- Botão Relatório Mensal Global --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center gap-2 font-bold py-2 px-4 rounded-lg transition-all shadow-md active:scale-95 text-sm"
                        style="background:rgba(255,255,255,0.12); color:white; border:1px solid rgba(255,255,255,0.2);">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Relatório Mensal
                        <svg class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50 animate-in fade-in zoom-in duration-100"
                        x-cloak>
                        <p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Selecionar Período</p>
                        <a href="{{ route('epis.imprimir-mensal', ['month' => now()->month, 'year' => now()->year]) }}" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Mês Atual ({{ now()->translatedFormat('F') }})
                        </a>
                        <a href="{{ route('epis.imprimir-mensal', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year]) }}" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Mês Anterior ({{ now()->subMonth()->translatedFormat('F') }})
                        </a>
                    </div>
                </div>

                <a href="{{ route('epis.entregas.crear') }}" wire:navigate
                    class="inline-flex items-center gap-2 font-bold py-2 px-5 rounded-lg transition-colors shadow-md whitespace-nowrap text-sm"
                    style="background:#FFD300; color:#09143B;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Entrega
                </a>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div style="background:#F0EEEB !important; border:1px solid rgba(9,20,59,0.14); border-radius:10px; padding:10px 14px;" class="flex items-center justify-between gap-2 flex-wrap">
        <div class="flex items-center gap-2">
            <select wire:model.live="filtroEpi" class="rounded-lg text-sm"
                style="background:white;color:#111827;border:1px solid #d1d5db;padding:8px 12px;outline:none;">
                <option value="">Todos os EPIs</option>
                @foreach ($epiItems as $epi)
                    <option value="{{ $epi->id }}">
                        {{ $epi->nombre }}{{ $epi->codigo ? ' (' . $epi->codigo . ')' : '' }}</option>
                @endforeach
            </select>
            <select wire:model.live="filtroEstado" class="rounded-lg text-sm"
                style="background:white;color:#111827;border:1px solid #d1d5db;padding:8px 12px;outline:none;">
                <option value="">Todos os estados</option>
                <option value="entregue">Entregue</option>
                <option value="devolvido">Devolvido</option>
            </select>
        </div>
        <div style="position:relative;display:flex;align-items:center;">
            <svg style="position:absolute;left:10px;color:#9ca3af;pointer-events:none;" class="h-4 w-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar..."
                style="background:white;color:#1f2937;border:1px solid #d1d5db;padding:8px 10px 8px 34px;border-radius:8px;font-size:0.875rem;width:240px;outline:none;">
        </div>
    </div>

    {{-- Table --}}
    <div class="space-y-4">
        @forelse($colaboradores as $cId => $cData)
            <div x-data="{ open: false }" class="rounded-xl overflow-hidden transition-all duration-300"
                 style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);">

                {{-- Collaborator Header --}}
                <div @click="open = !open"
                     class="px-5 py-4 flex items-center justify-between cursor-pointer transition-colors"
                     :style="open ? 'background:#E4E2DF;' : 'background:#F0EEEB;'">

                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center text-yellow-400 font-bold text-lg shadow-sm"
                             style="background:#09143B;">
                            {{ substr($cData['info']->nombre, 0, 1) }}{{ substr($cData['info']->apellido, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-lg leading-tight" style="color:#1A1A1A;">
                                {{ $cData['info']->nombre }} {{ $cData['info']->apellido }}
                            </h3>
                            <p class="text-xs font-medium uppercase tracking-wider mt-0.5" style="color:#7A7775;">
                                {{ $cData['info']->denominacion_cargo }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-[10px] font-bold uppercase tracking-tighter" style="color:#7A7775;">Total Registos</span>
                            <span class="text-sm font-black" style="color:#09143B;">
                                {{ $cData['meses']->reduce(fn($carry, $mes) => $carry + count($mes['items']), 0) }}
                            </span>
                        </div>
                        <div class="p-2 rounded-lg transition-colors" style="background:#E4E2DF; color:#7A7775;">
                            <svg class="h-5 w-5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Accordion Content (Months) --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="border-top:1px solid rgba(9,20,59,0.08);">
                    <div class="p-4 space-y-4" style="background:#EEECEA;">
                        @foreach($cData['meses'] as $mes)
                            <div class="rounded-xl overflow-hidden" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.12);">
                                {{-- Month Header --}}
                                <div class="px-4 py-2.5 flex items-center justify-between" style="background:#09143B;">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded-md" style="background:rgba(255,211,0,0.15); color:#FFD300;">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-medium text-[11px] uppercase tracking-widest" style="color:white;">{{ $mes['label'] }}</h4>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('epis.ficha', [$cId, 'month' => $mes['month'], 'year' => $mes['year']]) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-[10px] font-bold px-3 py-1.5 rounded-lg transition-all active:scale-95"
                                           style="background:#FFD300; color:#09143B;">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Imprimir Ficha ({{ $mes['month'] }}/{{ $mes['year'] }})
                                        </a>
                                    </div>
                                </div>

                                {{-- Deliveries Table --}}
                                <div class="overflow-x-auto" style="background:#FFFFFF !important;">
                                    <table class="w-full text-sm" style="background:#FFFFFF !important;">
                                        <thead style="background:#E4E2DF !important;">
                                            <tr class="text-[10px] font-bold uppercase tracking-widest" style="background:#E4E2DF !important;">
                                                <th class="px-5 py-3 text-left" style="color:#7A7775;">Data</th>
                                                <th class="px-5 py-3 text-left" style="color:#7A7775;">EPI</th>
                                                <th class="px-5 py-3 text-center" style="color:#7A7775;">Qtd</th>
                                                <th class="px-5 py-3 text-left" style="color:#7A7775;">Tamanho</th>
                                                <th class="px-5 py-3 text-center" style="color:#7A7775;">Estado</th>
                                                <th class="px-5 py-3 text-right" style="color:#7A7775;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody style="background:#FFFFFF !important;">
                                            @foreach($mes['items'] as $e)
                                                <tr class="transition-colors ent-row"
                                                    style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                                                    <td class="px-5 py-4 font-medium" style="color:#1A1A1A;">{{ $e->fecha_entrega->format('d/m/Y') }}</td>
                                                    <td class="px-5 py-4">
                                                        <div class="flex flex-col">
                                                            <span class="font-bold" style="color:#1A1A1A; font-weight:600;">{{ $e->epiItem->nombre ?? '—' }}</span>
                                                            @if($e->epiItem->codigo)
                                                                <span class="text-[11px] font-mono" style="color:#7A7775;">{{ $e->epiItem->codigo }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-center">
                                                        <span class="font-bold" style="color:#1A1A1A;">{{ $e->cantidad }}</span>
                                                        <span class="text-[10px] block" style="color:#7A7775;">{{ $e->epiItem->unidade ?? '' }}</span>
                                                    </td>
                                                    <td class="px-5 py-4 font-medium italic" style="color:#1A1A1A;">{{ $e->talla ?? '—' }}</td>
                                                    <td class="px-5 py-4 text-center">
                                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                                              style="{{ $e->estado->value === 'entregue' ? 'background:#d4ede4; color:#0F6E56; border-color:rgba(15,110,86,0.25);' : 'background:#E4E2DF; color:#4A4845; border-color:rgba(9,20,59,0.14);' }}">
                                                            {{ strtoupper($e->estado->label()) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <div class="flex items-center justify-end gap-2">

                                                            {{-- Indicador firma entrega --}}
                                                            @if ($e->estado->contaComoSaida())
                                                                @if ($e->firma)
                                                                    <span style="display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.25); border-radius:20px; padding:2px 8px;" title="Assinado">
                                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                        Ass.
                                                                    </span>
                                                                @else
                                                                    <button wire:click="solicitarAssinatura({{ $e->id }})"
                                                                            style="display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.25); border-radius:20px; padding:2px 8px;"
                                                                            title="Sem assinatura — clique para assinar">
                                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                        Ass.
                                                                    </button>
                                                                @endif
                                                            @endif

                                                            {{-- Indicador firma devolução --}}
                                                            @if ($e->estado === \App\Enums\EpiEntregaEstado::Devolvido)
                                                                @if ($e->firma_devolucion)
                                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5" title="Devolução assinada">
                                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                        Dev. assinada
                                                                    </span>
                                                                @else
                                                                    <button wire:click="abrirModalAssinaturaDevolucao({{ $e->id }})"
                                                                            class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 border border-red-300 rounded-full px-2 py-0.5 hover:bg-red-100 transition-colors"
                                                                            title="Sem assinatura de devolução — clique para assinar">
                                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                        Dev. sem ass.
                                                                    </button>
                                                                @endif
                                                            @endif

                                                            {{-- Devolver (solo Entregue) --}}
                                                            @if ($e->estado === \App\Enums\EpiEntregaEstado::Entregue)
                                                                <button wire:click="marcarDevolvido({{ $e->id }})"
                                                                        class="p-2 rounded-lg transition-colors ent-btn-devolver"
                                                                        style="color:#0F6E56;"
                                                                        title="Marcar Devolvido">
                                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 15L12 19L8 15M12 19V5" />
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                            {{-- Eliminar (sempre visível) --}}
                                                            <button wire:click="eliminar({{ $e->id }})"
                                                                    wire:confirm="Tem a certeza que quer eliminar esta entrega?"
                                                                    class="p-2 rounded-lg transition-colors ent-btn-eliminar"
                                                                    style="color:#A32D2D;"
                                                                    title="Eliminar">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>

                                                        </div>
                                                    </td>
                                                </tr>
                                                {{-- Inline Return Panel --}}
                                                @if ($devolvendoId === $e->id)
                                                    <tr style="background:#d4ede4;">
                                                        <td colspan="6" class="px-5 py-4" style="border-left:4px solid #0F6E56;">
                                                            <div class="flex items-center gap-4">
                                                                <div>
                                                                    <label class="text-[10px] font-bold text-green-700 uppercase tracking-widest block mb-1">Data de devolução</label>
                                                                    <input type="date" wire:model="dataDevolucao"
                                                                        class="rounded-lg text-sm border-green-200 focus:ring-green-500 focus:border-green-500 py-1.5 outline-none px-3"
                                                                        style="background:white; color:#111827;">
                                                                </div>
                                                                <div class="flex gap-2 mt-4">
                                                                    <button wire:click="confirmarDevolucao" class="px-4 py-2 rounded-lg text-xs font-bold bg-green-600 text-white shadow-sm hover:bg-green-700 transition-all active:scale-95">Confirmar</button>
                                                                    <button wire:click="cancelarDevolucao" class="px-4 py-2 rounded-lg text-xs font-bold bg-white text-gray-500 border border-gray-200 hover:bg-gray-50">Cancelar</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl py-20 flex flex-col items-center gap-4" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);">
                <div class="p-6 rounded-full" style="background:#E4E2DF; color:rgba(9,20,59,0.25);">
                    <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold" style="color:#1A1A1A;">Nenhum colaborador encontrado</h3>
                    <p class="text-sm" style="color:#7A7775;">Tente ajustar a sua pesquisa ou filtros.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal Firma Retroactiva --}}
    <div x-data="{
        open: false,
        signaturePad: null,
        hasSignature: false,
        isSaving: false,
        init() {
            window.addEventListener('abrir-modal-firma', () => {
                this.open = true;
                this.hasSignature = false;
                $wire.set('assinaturaRetroativaBase64', null);
                this.$nextTick(() => {
                    setTimeout(() => {
                        if (typeof SignaturePad === 'undefined') {
                            let script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
                            script.onload = () => { this.initCanvas(); };
                            document.head.appendChild(script);
                        } else {
                            this.initCanvas();
                        }
                    }, 150);
                });
            });
            window.addEventListener('cerrar-modal-firma', () => {
                this.open = false;
                if (this.signaturePad) this.signaturePad.clear();
                this.hasSignature = false;
                this.isSaving = false;
            });
            window.addEventListener('firma-guardada', () => {
                if (this.$wire) {
                    this.$wire.$refresh();
                }
            });
        },
        initCanvas() {
            let canvas = this.$refs.signatureCanvasModal;
            if (!canvas) return;
    
            const resizeCanvas = () => {
                let ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
            };
    
            const observer = new ResizeObserver(() => {
                if (canvas.offsetWidth > 0 && canvas.offsetHeight > 0) {
                    if (this.signaturePad) {
                        const data = this.signaturePad.toData();
                        resizeCanvas();
                        this.signaturePad.fromData(data);
                    } else {
                        resizeCanvas();
                        this.signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(0,0,0,0)',
                            penColor: 'rgb(30,64,175)',
                            minWidth: 2.0,
                            maxWidth: 4.5
                        });
                        this.signaturePad.addEventListener('endStroke', () => {
                            this.hasSignature = true;
                        });
                    }
                }
            });
    
            observer.observe(canvas.parentElement);
        }
    }" @keydown.escape.window="open = false; $wire.set('assinandoColaboradorId', null);" x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore>

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop --}}
            <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" style="z-index:40;"
                @click="open = false; $wire.set('assinandoColaboradorId', null);" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Card --}}
            <div x-show="open" @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 w-full"
                style="position:relative; z-index:50;">

                <form wire:submit.prevent="guardarAssinaturaRetroativa">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Assinatura de {{ $assinandoColaboradorNome }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Esta assinatura será aplicada a todos os EPIs em baixo. Esta ação é irreversível.</p>
                                @if (!empty($entregasPendentesAssinatura))
                                <ul class="mt-2 text-left inline-block text-xs text-gray-600 space-y-0.5">
                                    @foreach($entregasPendentesAssinatura as $ep)
                                    <li class="flex items-center gap-2">
                                        <svg class="h-3 w-3 text-yellow-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                        {{ $ep['nombre'] }}{{ $ep['talla'] ? ' ('.$ep['talla'].')' : '' }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6">
                        <div class="bg-gray-50 p-4 border border-gray-200 rounded-lg" wire:ignore>
                            <div class="border-2 border-dashed border-gray-400 bg-white rounded-lg overflow-hidden flex justify-center w-full relative"
                                style="height: 220px; z-index: 10;">
                                <canvas x-ref="signatureCanvasModal" @mousedown.stop @touchstart.stop @pointerdown.stop
                                    class="w-full h-full cursor-crosshair touch-none block"
                                    style="width:100%; height:100%;"></canvas>
                            </div>
                            <div class="flex justify-between items-center mt-3">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            @click="if(signaturePad) signaturePad.clear(); hasSignature = false;"
                                            class="text-xs font-semibold px-3 py-1.5 rounded bg-gray-200 text-gray-700 hover:bg-red-100 hover:text-red-600 transition-colors">
                                            Limpar Assinatura
                                        </button>
                                        
                                        <button type="button" wire:click="startMobileSignatureIndex"
                                            class="text-xs font-semibold px-3 py-1.5 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                            Firmar en Móvil
                                        </button>
                                    </div>
                                <span class="text-xs font-medium text-gray-400">Assine com o dedo ou rato</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit"
                            @click.prevent="
                                if (!hasSignature || !signaturePad || signaturePad.isEmpty()) {
                                    alert('Por favor, assine antes de confirmar.');
                                    return;
                                }
                                $wire.set('assinaturaRetroativaBase64', signaturePad.toSVG()).then(() => {
                                    $wire.guardarAssinaturaRetroativa();
                                });
                            "
                            :class="hasSignature ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none sm:col-start-2 sm:text-sm transition-colors">
                            <span wire:loading.remove wire:target="guardarAssinaturaRetroativa">Confirmar Assinatura</span>
                            <span wire:loading wire:target="guardarAssinaturaRetroativa">A guardar...</span>
                        </button>
                        <button type="button" @click="open = false; $wire.set('assinandoColaboradorId', null); $wire.set('assinaturaRetroativaBase64', null);"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-500 hover:text-white focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Firma Devolução --}}
    <div x-data="{
        open: false,
        signaturePad: null,
        init() {
            window.addEventListener('abrir-modal-firma-devolucion', () => {
                this.open = true;
                this.$nextTick(() => {
                    setTimeout(() => {
                        if (typeof SignaturePad === 'undefined') {
                            let script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js';
                            script.onload = () => { this.initCanvas(); };
                            document.head.appendChild(script);
                        } else {
                            this.initCanvas();
                        }
                    }, 150);
                });
            });
            window.addEventListener('cerrar-modal-firma-devolucion', () => {
                this.open = false;
                if (this.signaturePad) this.signaturePad.clear();
            });
        },
        initCanvas() {
            let canvas = this.$refs.canvasDev;
            if (!canvas) return;
            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
            };
            const observer = new ResizeObserver(() => {
                if (canvas.offsetWidth > 0 && canvas.offsetHeight > 0) {
                    if (!this.signaturePad) {
                        resizeCanvas();
                        this.signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(0,0,0,0)',
                            penColor: 'rgb(30,64,175)', minWidth: 1.5, maxWidth: 3.5
                        });
                    }
                }
            });
            observer.observe(canvas.parentElement);
        }
    }" @keydown.escape.window="open = false; $wire.set('firmandoDevolucaoEntregaId', null);"
        x-show="open" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" aria-modal="true" wire:ignore>

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" style="z-index:40;"
                @click="open = false; $wire.set('firmandoDevolucaoEntregaId', null);"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="open" @click.stop class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 w-full"
                style="position:relative; z-index:50;">
                <form wire:submit.prevent="guardarAssinaturaDevolucao">
                    <div class="text-center mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Assinatura de Devolução</h3>
                        <p class="text-sm text-gray-500 mt-1">O responsável pela receção assina neste espaço para confirmar a devolução do EPI.</p>
                    </div>

                    <div class="bg-gray-50 p-4 border border-gray-200 rounded-lg" wire:ignore>
                        <div class="border-2 border-dashed border-blue-300 bg-white rounded-lg overflow-hidden flex justify-center" style="height:200px;">
                            <canvas x-ref="canvasDev" @mousedown.stop @touchstart.stop @pointerdown.stop
                                class="w-full h-full cursor-crosshair touch-none block" style="width:100%;height:100%;"></canvas>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <button type="button" @click="if(signaturePad) signaturePad.clear();"
                                class="text-xs font-semibold px-3 py-1.5 rounded bg-gray-200 text-gray-700 hover:bg-red-100 hover:text-red-600 transition-colors">
                                Limpar
                            </button>
                            <span class="text-xs font-medium text-gray-400">Assine com o dedo ou rato</span>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit"
                            @click="$wire.assinaturaDevolucaoBase64 = signaturePad ? signaturePad.toSVG() : ''"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:col-start-2 sm:text-sm transition-colors bg-blue-600 hover:bg-blue-700">
                            <span wire:loading.remove wire:target="guardarAssinaturaDevolucao">Confirmar Assinatura</span>
                            <span wire:loading wire:target="guardarAssinaturaDevolucao">A guardar...</span>
                        </button>
                        <button type="button" @click="open = false; $wire.set('firmandoDevolucaoEntregaId', null);"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de Firma Móvil --}}
    @if($showSignatureModal)
    <div class="fixed inset-0 z-100 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             wire:click="$set('showSignatureModal', false)"></div>

        {{-- Modal Card --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border-t-8 border-yellow-400 animate-in fade-in zoom-in duration-200" style="z-index: 110;">
            <div class="p-6">
                <div class="flex flex-col items-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Firmar en Móvil</h3>
                    <p class="text-sm text-gray-600 text-center mb-1 font-semibold">Como escanear?</p>
                    <p class="text-xs text-gray-500 text-center mb-6 px-4">
                        Aponte a câmera do seu celular para o código abaixo. 
                        Recomendamos o uso do <span class="text-blue-600 font-bold">Google Lens</span> ou o leitor padrão do seu dispositivo.
                    </p>
                    
                    <div class="bg-white p-3 border-2 border-gray-100 rounded-xl mb-6 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($this->signature_url) }}" 
                             alt="QR Code" width="250" height="250" class="block mx-auto">
                    </div>

                    <div class="w-full bg-gray-50 p-4 rounded-xl mb-6 flex flex-col gap-2">
                        <span class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Link de Firma</span>
                        <div class="flex gap-2">
                            <input type="text" readonly value="{{ $this->signature_url }}" id="sig-url-index" 
                                   class="flex-1 bg-white border border-gray-200 rounded-lg text-xs py-2 px-3 focus:ring-2 focus:ring-blue-500 outline-none"
                                   style="color:#111827 !important;">
                            <button type="button" 
                                    onclick="
                                        const el = document.getElementById('sig-url-index');
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(el.value).then(() => alert('Link copiado!'));
                                        } else {
                                            el.select();
                                            document.execCommand('copy');
                                            alert('Link copiado!');
                                        }
                                    " 
                                    class="bg-gray-200 p-2 rounded-lg hover:bg-gray-300 transition-colors">
                                <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-blue-600 animate-pulse mb-4">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="text-sm font-bold" x-text="hasSignature ? 'Firma capturada!' : 'Aguardando assinatura...'"></span>
                    </div>

                    {{-- Signature Canvas Preview --}}
                    <div class="w-full border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 overflow-hidden mb-6" 
                         x-show="hasSignature" x-cloak>
                        <div class="bg-gray-100 px-3 py-1 text-[10px] font-bold text-gray-500 uppercase flex justify-between items-center">
                            <span>Pré-visualização</span>
                            <button type="button" @click="signaturePad.clear(); hasSignature = false; @this.set('assinaturaRetroativaBase64', '')" class="text-red-500 hover:text-red-700 text-[9px] font-black uppercase">Limpar</button>
                        </div>
                        <div style="height: 120px;" class="bg-white">
                            <canvas x-ref="signatureCanvas" class="w-full h-full cursor-crosshair"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col gap-2">
                <button type="button" wire:click="guardarAssinaturaRetroativa" 
                        x-show="hasSignature" x-cloak
                        class="w-full inline-flex justify-center rounded-xl border border-transparent px-4 py-2.5 bg-green-600 text-sm font-bold text-white hover:bg-green-700 transition-all active:scale-95 shadow-lg">
                    Confirmar e Gravar Signature
                </button>
                <button type="button" wire:click="$set('showSignatureModal', false)" 
                        class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                    Cancelar
                </button>
                <div wire:poll.3s="checkSignatureStatus"></div>
            </div>
        </div>
    </div>
    @endif

    <livewire:epis.entrega-modal />
</div>

<style>
.ent-row:nth-child(even) { background: #F0EEEB !important; }
.ent-row:nth-child(odd)  { background: #FFFFFF !important; }
.ent-row:hover { background: #E4E2DF !important; }
.ent-btn-devolver:hover { background: #d4ede4 !important; }
.ent-btn-eliminar:hover { background: #fde8e8 !important; }
</style>
