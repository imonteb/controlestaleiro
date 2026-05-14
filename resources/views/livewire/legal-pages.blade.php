<div class="w-full max-w-4xl mx-auto px-4 py-8">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ url()->previous() == route('legal') ? route('home') : url()->previous() }}"
                   style="color:rgba(255,255,255,0.5); font-size:11px; margin-right:8px;" class="hover:text-white flex items-center gap-1">
                    ← Voltar
                </a>
                <flux:icon name="document-text" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Info Legal</span>
                <span style="color:rgba(255,255,255,0.4); font-size:11px;">— Unidade de Estaleiro C016</span>
            </div>
            <span style="color:rgba(255,255,255,0.4); font-size:10px; font-weight:800; letter-spacing:0.1em; text-transform:uppercase;">
                {{ $pagina->versao }} · Rev. {{ $pagina->ultima_revisao?->translatedFormat('F Y') }}
            </span>
        </div>

        {{-- Tabs --}}
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-4">
            @foreach(['privacidade' => ['Privacidade', 'shield-check'], 'termos' => ['Termos', 'document-text'], 'cookies' => ['Cookies', 'circle-stack']] as $key => [$label, $icon])
                <button wire:click="$set('tab', '{{ $key }}')"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wide border-b-2 transition-colors
                               {{ $tab === $key
                                   ? 'text-[#FFD300] border-[#FFD300]'
                                   : 'text-white/35 border-transparent hover:text-white/60' }}">
                    <flux:icon name="{{ $icon }}" class="w-3.5 h-3.5" />
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Conteúdo legal --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        {{-- Card header --}}
        <div style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);" class="px-6 py-3 flex items-center gap-2">
            <flux:icon name="document-text" class="w-4 h-4" style="color:#09143B;" />
            <span style="color:#09143B; font-weight:700; font-size:13px;">{{ $pagina->titulo }}</span>
        </div>

        {{-- Body --}}
        <div class="legal-content p-8 md:p-12" style="background:#F0EEEB;">
            {!! $pagina->conteudo !!}
        </div>

        {{-- Footer --}}
        <div style="background:#E4E2DF; border-top:1px solid rgba(9,20,59,0.08);" class="px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-3">
            <p style="color:#7A7775; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.12em;">
                © 2024–2026 Construção e Manutenção Electromecânica S.A.
            </p>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span style="color:#7A7775; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.12em;">RGPD · Lei n.º 58/2019 · Direito Português</span>
            </div>
        </div>
    </div>

</div>

<style>
    .legal-content { color: #1A1A1A; font-size: 0.875rem; line-height: 1.8; }
    .legal-content h1, .legal-content h2 {
        color: #09143B; font-size: 0.875rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin: 2rem 0 0.6rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid rgba(9,20,59,0.12);
    }
    .legal-content h3 {
        color: #4A4845; font-size: 0.8rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        margin: 1.5rem 0 0.4rem;
    }
    .legal-content p { margin: 0.75rem 0; color: #4A4845; }
    .legal-content ul, .legal-content ol { margin: 0.75rem 0 0.75rem 1.5rem; color: #4A4845; }
    .legal-content ul { list-style-type: disc; }
    .legal-content ol { list-style-type: decimal; }
    .legal-content li { margin: 0.4rem 0; }
    .legal-content strong { color: #09143B; font-weight: 700; }
    .legal-content a { color: #09143B; text-decoration: underline; }
    .legal-content a:hover { color: #0d1a4a; }
    .legal-content code {
        background: rgba(9,20,59,0.08); color: #09143B;
        padding: 0.1em 0.4em; border-radius: 4px;
        font-size: 0.8em; font-family: monospace;
    }
    .legal-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.8rem; }
    .legal-content th {
        background: #E4E2DF; color: #09143B; font-weight: 800;
        text-align: left; padding: 0.6rem 0.9rem;
        border-bottom: 1px solid rgba(9,20,59,0.12);
        font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .legal-content td {
        padding: 0.55rem 0.9rem;
        border-bottom: 1px solid rgba(9,20,59,0.06);
        vertical-align: top; color: #4A4845;
    }
    .legal-content tr:last-child td { border-bottom: none; }
    .legal-content blockquote {
        border-left: 3px solid #FFD300;
        padding-left: 1rem; margin: 1rem 0;
        color: #7A7775; font-style: italic;
        background: rgba(255,211,0,0.05);
        border-radius: 0 6px 6px 0;
    }
</style>
