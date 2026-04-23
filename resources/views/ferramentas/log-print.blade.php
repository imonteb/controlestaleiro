@php
    $bgImage = base64_encode(file_get_contents(storage_path('app/private/verificacion-planilla/verificacion-f.png')));
@endphp
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>RELATÓRIO DE VERIFICAÇÃO — Folha {{ $folha_id }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            background: radial-gradient(circle at center, #f3f4f6 0%, #d1d5db 100%);
        }

        /* ── Page shell ── */
        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            margin: 30px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,.25);
        }
        .page-bg {
            position: absolute;
            inset: 0;
            background-image: url('data:image/png;base64,{{ $bgImage }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        /* ── ID bar overlays ── */
        /* Responsável name / employee number — value cell of the ID bar */
        .id-responsavel {
            position: absolute;
            top: 32mm;
            left: 185mm;
            width: 155mm;
            height: 10mm;
            display: flex;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
            padding-left: 2mm;
        }
        .id-numero {
            position: absolute;
            top: 32mm;
            left: 222mm;
            width: 155mm;
            height: 10mm;
            display: flex;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
            padding-left: 2mm;
        }
        /* Unidade value cell */
        .id-unidade {
            position: absolute;
            top: 34mm;
            left: 270mm;
            width: 11mm;
            height: 10mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* ── Data rows overlay table ── */
        /* Sits exactly on top of the 9 empty rows in the PNG */
        .data-table {
            position: absolute;
            top: 70.5mm;
            left: 23mm;
            width: 262.5mm;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .data-table td {
            height: 10.3mm;
            text-align: center;
            vertical-align: middle;
            padding: 0 1px;
            font-size: 9.5px;
            font-weight: bold;
            overflow: hidden;
            
        }
        /* Widths from PNG: des(42.5)+id(22.5)+w10x4(40)+w7+w10+w7(25)+w10x2(20)+w10+w12(22.5)+w10+w12(22.5)+w10x2(20)+w10+w12(22.5)+data(10)+rub(15)=262.5mm */
        .c-des  { width: 42.5mm; text-align: left !important; padding-left: 2px; font-size: 7px; }
        .c-id   { width: 22.5mm; font-size: 6.5px; }
        .c-w10  { width: 10mm; }
        .c-w7   { width: 7.5mm; }
        .c-w12  { width: 12.5mm; }
        .c-data { width: 10mm; font-size: 8.5px; }
        .c-rub  { width: 15mm; }
        .cell-2l {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            word-break: break-word;
            line-height: 1.15;
            text-transform: uppercase;
        }

        /* Signature inside rubrica cell */
        .sig-box {
            height: 9mm;
            width: 15mm;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .sig-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform: scale(2.8) translateX(10%);
            transform-origin: center center;
        }

        /* ── Observations overlay ── */
        .obs-overlay {
            position: absolute;
            top: 177mm;
            left: 5mm;
            width: 235mm;
            height: 24mm;
            padding: 2mm 3mm;
            font-size: 8.5px;
            font-weight: bold;
            overflow: hidden;
        }

        /* ── Registo number ── */
        .registo-overlay {
            position: absolute;
            top: 193mm;
            right: 12mm;
            font-size: 11px;
            font-weight: bold;
            background: #eab308;
            border: 1px solid #000;
            padding: 1mm 3mm;
        }

        /* ── Print timestamp + page ── */
        .footer-overlay {
            position: absolute;
            bottom: 3mm;
            left: 5mm;
            right: 5mm;
            display: flex;
            justify-content: flex-end;
            gap: 8mm;
            font-size: 6px;
            color: #444;
        }

        @media print {
            body { background: white !important; }
            .page { margin: 0 !important; box-shadow: none !important; }
            .no-print { display: none !important; }
        }
        .print-bar {
            text-align: center;
            padding: 12px;
            background: #1e3a5f;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
        }
        .print-bar button {
            background: #eab308;
            color: #1e3a5f;
            font-weight: 700;
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }
        .print-bar button:hover { background: #fbbf24; }
    </style>
</head>
<body>

    <div class="print-bar no-print">
        <button onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
    </div>

    <div class="page">
        {{-- Background form image --}}
        <div class="page-bg"></div>

        {{-- ID Bar: Responsável name / employee number --}}
        <div class="id-responsavel">
            {{ auth()->user()->name }}
        </div>
        <div class="id-numero">
             {{ auth()->user()->employee_number ?? '_____' }}
        </div>

        {{-- ID Bar: Unidade --}}
        <div class="id-unidade">{{ $logs->first()->unidade ?? 'C016' }}</div>

        {{-- 9 Data rows overlaid on the PNG grid --}}
        <table class="data-table">
            <colgroup>
                <col class="c-des">
                <col class="c-id">
                {{-- Estabilidade OK/NOK --}}
                <col class="c-w10"><col class="c-w10">
                {{-- Mecanico OK/NOK --}}
                <col class="c-w10"><col class="c-w10">
                {{-- Eletrico OK/NOK/NA --}}
                <col class="c-w7"><col class="c-w10"><col class="c-w7">
                {{-- Projecoes OK/NOK --}}
                <col class="c-w10"><col class="c-w10">
                {{-- Manutencao OK / NOK (NOK mais largo) --}}
                <col class="c-w10"><col class="c-w12">
                {{-- Acessorios OK / NOK (NOK mais largo) --}}
                <col class="c-w10"><col class="c-w12">
                {{-- Maq Nao Auto OK/NOK --}}
                <col class="c-w10"><col class="c-w10">
                {{-- Resultado: Apto / N-Apto (mais largo) / Data / Rubrica --}}
                <col class="c-w10"><col class="c-w12"><col class="c-data"><col class="c-rub">
            </colgroup>
            <tbody>
                @for($i = 0; $i < 9; $i++)
                    @php
                        $log      = $logs->get($i);
                        $checklist = $log ? ($log->checklist ?? []) : [];
                        $v = fn(string $key, string $val) => ($checklist[$key] ?? '') === $val ? 'X' : '';
                    @endphp
                    <tr>
                        <td class="c-des"><div class="cell-2l">{{ $log->ferramenta->designacao ?? '' }}</div></td>
                        <td class="c-id"><div class="cell-2l">{{ $log->ferramenta->referencia ?? ($log->ferramenta->num_serie ?? '') }}</div></td>

                        <td>{{ $v('estabilidade_rotura','ok') }}</td>
                        <td>{{ $v('estabilidade_rotura','not_ok') }}</td>

                        <td>{{ $v('contacto_mecanico','ok') }}</td>
                        <td>{{ $v('contacto_mecanico','not_ok') }}</td>

                        <td>{{ $v('contacto_eletrico','ok') }}</td>
                        <td>{{ $v('contacto_eletrico','not_ok') }}</td>
                        <td>{{ $v('contacto_eletrico','na') }}</td>

                        <td>{{ $v('projecoes','ok') }}</td>
                        <td>{{ $v('projecoes','not_ok') }}</td>

                        <td>{{ $v('manutencao','ok') }}</td>
                        <td>{{ $v('manutencao','not_ok') }}</td>

                        <td>{{ $v('acessorios_cargas','ok') }}</td>
                        <td>{{ $v('acessorios_cargas','not_ok') }}</td>

                        <td>{{ $v('maq_nao_automotores','ok') }}</td>
                        <td>{{ $v('maq_nao_automotores','not_ok') }}</td>

                        <td>{{ $log && $log->apto ? 'X' : '' }}</td>
                        <td>{{ $log && !$log->apto ? 'X' : '' }}</td>
                        <td>{{ $log ? $log->data_verificacao->format('d/m') : '' }}</td>
                        <td>
                            @if($log?->assinatura_path)
                                <div class="sig-box">
                                    <img src="{{ $log->assinatura_path }}" class="sig-img">
                                </div>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- Observations text --}}
        <div class="obs-overlay">
            {{ $logs->pluck('observacoes')->filter()->unique()->join('; ') }}
        </div>

        {{-- Registo number badge --}}
        <div class="registo-overlay">
            Registo: {{ $logs->first()->num_registo_verificacao ?? 'N/A' }}
        </div>

        {{-- Print info --}}
        <div class="footer-overlay">
            <span>Impresso em: {{ now()->format('d/m/Y H:i') }}</span>
            <span>Pág. {{ $folha_id }}</span>
        </div>
    </div>

</body>
</html>
