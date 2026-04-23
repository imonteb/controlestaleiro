<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Ficha EPI — {{ $colaborador->nombre }} {{ $colaborador->apellido }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 9px;
            color: #111;
            background: #e5e7eb;
        }

        /* A4 page container — SVG fills the full page */
        .page {
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
            background: white;
        }

        @media screen {
            .page { margin: 0 auto 24px; box-shadow: 0 4px 20px rgba(0,0,0,.35); }
        }

        /* SVG form as base layer */
        img.bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            display: block;
        }

        /* All data overlay elements */
        .f {
            position: absolute;
            z-index: 1;
            font-family: 'Calibri', Arial, sans-serif;
            font-size: 9px;
            line-height: 1;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Signature image wrapper */
        .sig-wrap {
            position: absolute;
            z-index: 2;
        }

        .sig-wrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left center;
        }

        @media print {
            body { background: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>

<body>

    {{-- ══ Barra de impressão (só no browser) ══ --}}
    <div class="no-print"
         style="display:flex; align-items:center; gap:16px; padding:12px 24px; background:#1e3a5f;">
        <img src="{{ asset('storage/logos/ProCME.png') }}" alt="ProCME"
             style="height:36px; background:white; border-radius:6px; padding:4px 8px;">
        @if (isset($mesLabel))
            <span style="color:#fbbf24; font-weight:700; font-size:15px; text-transform:uppercase; flex:1;">
                Ficha EPI — {{ ucfirst($mesLabel) }}
            </span>
        @else
            <span style="flex:1;"></span>
        @endif
        <button onclick="window.print()"
                style="background:#eab308; color:#1e3a5f; font-weight:700; padding:10px 28px; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
            🖨️ Imprimir / Guardar PDF
        </button>
        <a href="{{ route('epis.ficha', $colaborador->id) }}"
           style="background:#6b7280; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px;">
            ← Escolher Mês
        </a>
        <a href="{{ route('epis.historico') }}@if (isset($month) && isset($year))?month={{ $month }}&year={{ $year }}@endif"
           style="background:#374151; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px;">
            Histórico
        </a>
    </div>

    @php
        $pages = $entregas->chunk(11);
        if ($pages->isEmpty()) {
            $pages = collect([collect([])]);
        }

        $unitAbbr = [
            'unidade' => 'un', 'unidades' => 'un',
            'metro' => 'mt', 'metros' => 'mt',
            'caixa' => 'cx', 'caixas' => 'cx',
            'par' => 'pr', 'pares' => 'pr',
            'litro' => 'lt', 'litros' => 'lt',
            'kilogramo' => 'kg', 'quilo' => 'kg',
            'grama' => 'gr', 'saco' => 'sc',
        ];

        /*
         * EPI row text-baseline y-positions (mm) derived from SVG translate analysis.
         * Each row is ~12mm tall; text baselines land here.
         * Cols 5-6 internal layout (relative to rowY):
         *   +3mm → "Data: ___/___/___" line   (date fill at x=150.3mm / 184.2mm)
         *   +9mm → "Ass.: __________" line     (sig  fill at x=151mm   / 184.9mm)
         */
        $rowY = [74, 86, 99, 111, 123, 135, 148, 160, 172, 184, 197];
    @endphp

    @foreach ($pages as $pageIndex => $pageItems)
        @php
            $pageSignature = $pageItems->filter(fn ($e) => ! empty($e->firma))->sortByDesc('updated_at')->first();
            $firstWithResp = $pageItems->first(fn ($e) => $e->entregadoPor);
            $entregador = $firstWithResp ? $firstWithResp->entregadoPor : null;
            $entregadorSig = $entregador ? $entregador->signature : null;
            $dataEntrega = $pageSignature
                ? ($pageSignature->fecha_entrega
                    ? $pageSignature->fecha_entrega->format('d/m/Y')
                    : $pageSignature->updated_at->format('d/m/Y'))
                : '___/___/___';
            $dataAssinatura = $pageSignature
                ? $pageSignature->updated_at->format('d/m/Y')
                : '___/___/___';
        @endphp

        <div class="page" style="{{ ! $loop->first ? 'page-break-before: always;' : '' }}">

            {{-- ── SVG FORM BACKGROUND ── --}}
            <img class="bg" src="{{ asset('storage/epifichaform/formulario-epi.svg') }}" alt="">

            {{-- ══════════════════════════════════════════════════ --}}
            {{-- IDENTIFICAÇÃO                                       --}}
            {{-- Label positions from SVG: Nome y=53mm x=15mm        --}}
            {{--                           Categoria y=61mm x=92mm   --}}
            {{-- ══════════════════════════════════════════════════ --}}

            <span class="f" style="top:50mm; left:25mm; width:68mm; font-weight:700; font-size:9.5px;">
                {{ $colaborador->nombre }} {{ $colaborador->apellido }}
            </span>
            {{-- N.º Mecanográfico: etiqueta muy larga (~50mm), valor va más a la derecha --}}
            <span class="f" style="top:50mm; left:160mm; width:35mm; font-weight:700; font-size:9.5px;">
                {{ $colaborador->numero_colaborador ?? '' }}
            </span>
            <span class="f" style="top:58mm; left:28mm; width:64mm; font-weight:700; font-size:9.5px;">
                {{ config('app.empresa', 'CME  C016') }}
            </span>
            <span class="f" style="top:58mm; left:127mm; width:68mm; font-weight:700; font-size:9.5px;">
                {{ $colaborador->denominacion_cargo ?? '' }}
            </span>

            {{-- ══════════════════════════════════════════════════ --}}
            {{-- EPI ROWS                                            --}}
            {{-- Col positions (x, mm):                             --}}
            {{--   Designação 15 | Refª 76 | Un. 91 | Riscos 102   --}}
            {{--   Recepção Data 150 | Ass 151                      --}}
            {{--   Devolução Data 184 | Ass 185                     --}}
            {{-- ══════════════════════════════════════════════════ --}}

            @foreach ($pageItems as $e)
                @php
                    $ry = $rowY[$loop->index];
                    $unRaw = strtolower($e->epiItem->unidade ?? '');
                    $unShow = $unitAbbr[$unRaw] ?? $unRaw;
                    $riscosNums = [];
                    if ($e->epiItem->riscos) {
                        foreach ($e->epiItem->riscos as $r) {
                            $riscosNums[] = $riscosMap[$r] ?? $r;
                        }
                    }
                @endphp

                {{-- Col 1: Designação --}}
                <span class="f" style="top:{{ $ry + 2 }}mm; left:15mm; width:60mm;
                                       white-space:normal; font-size:10px; line-height:1.3;">
                    {{ $e->epiItem->nombre ?? '—' }}{{ $e->talla ? ' (' . $e->talla . ')' : '' }}
                </span>

                {{-- Col 2: Refª --}}
                <span class="f" style="top:{{ $ry + 2 }}mm; left:76mm; width:14mm;
                                       text-align:center; font-size:8.5px;">
                    {{ $e->epiItem->codigo ?? '' }}
                </span>

                {{-- Col 3: Un. --}}
                <span class="f" style="top:{{ $ry + 2 }}mm; left:91mm; width:10mm;
                                       text-align:center; font-size:9.5px;">
                    {{ $e->cantidad }}{{ $unShow ? ' ' . $unShow : '' }}
                </span>

                {{-- Col 4: Riscos --}}
                <span class="f" style="top:{{ $ry - 2 }}mm; left:102mm; width:22mm;
                                       text-align:center; font-size:9px;">
                    {{ implode(', ', $riscosNums) }}
                </span>

                {{-- Col 5: Recepção — Date (3 spans sobre ___/___/___) --}}
                {{-- "Data:" label en SVG x≈124.9mm; valor empieza en ≈132mm --}}
                @if ($e->fecha_entrega)
                    <span class="f" style="top:{{ $ry + 0.5 }}mm; left:133mm;   width:4.5mm; font-size:8px; text-align:center;">{{ $e->fecha_entrega->format('d') }}</span>
                    <span class="f" style="top:{{ $ry + 0.5 }}mm; left:139mm;   width:4.5mm; font-size:8px; text-align:center;">{{ $e->fecha_entrega->format('m') }}</span>
                    <span class="f" style="top:{{ $ry + 0.5 }}mm; left:144mm;   width:6mm;   font-size:8px; text-align:center;">{{ $e->fecha_entrega->format('Y') }}</span>
                @endif

                {{-- Col 5: Recepção — Signature --}}
                @if ($e->firma)
                    <div class="sig-wrap" style="top:{{ $ry + 1 }}mm; left:131mm; width:28mm; height:10mm;">
                        <img src="{{ $e->firma }}" alt="">
                    </div>
                @endif

                {{-- Col 6: Devolução --}}
                @if ($e->estado === \App\Enums\EpiEntregaEstado::Perdido)
                    <span class="f" style="top:{{ $ry - 2 }}mm; left:161mm; width:44mm;
                                           font-size:9px; font-weight:700; color:#dc2626;">
                        PERDIDO
                    </span>
                @elseif ($e->estado === \App\Enums\EpiEntregaEstado::Danificado)
                    <span class="f" style="top:{{ $ry - 2 }}mm; left:161mm; width:44mm;
                                           font-size:9px; font-weight:700; color:#c2410c;">
                        DANIFICADO
                    </span>
                @else
                    {{-- Col 6: Devolução — Date (3 spans sobre ___/___/___) --}}
                    {{-- "Data:" label en SVG x≈158.8mm; valor empieza en ≈167mm --}}
                    @if ($e->fecha_devolucion)
                        <span class="f" style="top:{{ $ry + 0.5 }}mm; left:167mm;   width:4.5mm; font-size:8px; text-align:center;">{{ $e->fecha_devolucion->format('d') }}</span>
                        <span class="f" style="top:{{ $ry + 0.5 }}mm; left:173mm;   width:4.5mm; font-size:8px; text-align:center;">{{ $e->fecha_devolucion->format('m') }}</span>
                        <span class="f" style="top:{{ $ry + 0.5 }}mm; left:178mm;   width:6mm;   font-size:8px; text-align:center;">{{ $e->fecha_devolucion->format('Y') }}</span>
                    @endif

                    {{-- Col 6: Devolução — Signature --}}
                    @if ($e->firma_devolucion)
                        <div class="sig-wrap" style="top:{{ $ry + 1 }}mm; left:164mm; width:33mm; height:10mm;">
                            <img src="{{ $e->firma_devolucion }}" alt="">
                        </div>
                    @endif
                @endif

            @endforeach

            {{-- ══════════════════════════════════════════════════ --}}
            {{-- DECLARAÇÃO — signature overlays only               --}}
            {{-- SVG already has all text; we overlay the values.   --}}
            {{-- Positions derived from SVG translate analysis.     --}}
            {{-- Worker sig line:  y≈247mm, x=118–163mm             --}}
            {{-- Worker date fill: y≈251mm, x≈153mm                 --}}
            {{-- Responsável sig:  y≈260mm, x≈16mm                  --}}
            {{-- Responsável nome: y≈267mm, x≈45mm                  --}}
            {{-- Responsável data: y≈272mm, x≈109mm                 --}}
            {{-- ══════════════════════════════════════════════════ --}}

            {{-- Worker signature --}}
            @if ($pageSignature && $pageSignature->firma)
                <div class="sig-wrap" style="top:249mm; left:39mm; width:42mm; height:18mm;">
                    <img src="{{ $pageSignature->firma }}" alt="">
                </div>
            @endif

            {{-- Worker date --}}
            @php $ds = $pageSignature ? $pageSignature->updated_at : null; @endphp
            @if ($ds)
                <span class="f" style="top:258mm; left:90mm;   width:4.5mm; font-size:9px; text-align:center;">{{ $ds->format('d') }}</span>
                <span class="f" style="top:258mm; left:96.5mm; width:4.5mm; font-size:9px; text-align:center;">{{ $ds->format('m') }}</span>
                <span class="f" style="top:258mm; left:103mm;  width:6mm;   font-size:9px; text-align:center;">{{ $ds->format('Y') }}</span>
            @endif

            {{-- Responsible signature --}}
            @if ($entregadorSig)
                <div class="sig-wrap" style="top:264mm; left:29mm; width:52mm; height:13mm; mix-blend-mode: multiply;">
                    <img src="{{ $entregadorSig }}" alt="">
                </div>
            @endif

            {{-- Responsible name --}}
            @if ($entregador)
                <span class="f" style="top:265mm; left:44mm; width:63mm; font-size:8.5px;">
                    {{ $entregador->name }}
                </span>
            @endif

            {{-- Responsible date (3 spans sobre ___/___/___) --}}
            @php
                $de = $pageSignature && $pageSignature->fecha_entrega
                    ? $pageSignature->fecha_entrega
                    : ($pageSignature ? $pageSignature->updated_at : null);
            @endphp
            @if ($de)
                <span class="f" style="top:270mm; left:91mm;    width:4.5mm; font-size:9px; text-align:center;">{{ $de->format('d') }}</span>
                <span class="f" style="top:270mm; left:96.5mm;  width:4.5mm; font-size:9px; text-align:center;">{{ $de->format('m') }}</span>
                <span class="f" style="top:270mm; left:103mm;   width:6mm;   font-size:9px; text-align:center;">{{ $de->format('Y') }}</span>
            @endif

        </div>
    @endforeach

</body>

</html>
