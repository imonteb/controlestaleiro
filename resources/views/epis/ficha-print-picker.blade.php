<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha EPI — {{ $colaborador->nombre }} {{ $colaborador->apellido }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }

        /* ── Print bar (same as ficha-print) ── */
        .print-bar {
            background: #1e3a5f;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .print-bar img {
            height: 36px;
            background: white;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .print-bar .title {
            color: #fbbf24;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex: 1;
        }
        .print-bar a {
            background: #6b7280;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }
        .print-bar a:hover { background: #4b5563; }

        /* ── Content ── */
        .content {
            max-width: 520px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .colaborador {
            background: white;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #1e3a8a;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .colaborador .num-badge {
            background: #1e3a8a;
            color: #fbbf24;
            font-weight: 900;
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 6px;
            white-space: nowrap;
        }
        .colaborador .nome { font-size: 16px; font-weight: 700; color: #1e3a8a; }
        .colaborador .cargo { font-size: 12px; color: #6b7280; margin-top: 2px; }

        h2 {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }
        .mes-list { display: flex; flex-direction: column; gap: 8px; }
        .mes-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 18px;
            text-decoration: none;
            color: #111827;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.15s;
        }
        .mes-btn:hover {
            background: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
        }
        .mes-btn .badge {
            font-size: 11px;
            font-weight: 700;
            background: #f3f4f6;
            color: #6b7280;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
        }
        .mes-btn:hover .badge {
            background: rgba(255,255,255,0.15);
            color: white;
            border-color: rgba(255,255,255,0.2);
        }
        .empty {
            text-align: center;
            color: #6b7280;
            padding: 32px;
            background: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="print-bar">
        <img src="{{ asset('storage/logos/ProCME.png') }}" alt="ProCME">
        <span class="title">Ficha EPI — Selecionar Mês</span>
        <a href="{{ route('epis.historico') }}">Histórico</a>
        <a href="{{ url()->previous() }}" style="background:#374151;">← Voltar</a>
    </div>

    <div class="content">
        <div class="colaborador">
            @if($colaborador->numero_colaborador)
                <div class="num-badge">Nº {{ $colaborador->numero_colaborador }}</div>
            @endif
            <div>
                <div class="nome">{{ $colaborador->nombre }} {{ $colaborador->apellido }}</div>
                @if($colaborador->denominacion_cargo ?? $colaborador->cargo ?? null)
                    <div class="cargo">{{ $colaborador->denominacion_cargo ?? $colaborador->cargo }}</div>
                @endif
            </div>
        </div>

        <h2>Escolha o mês a imprimir</h2>

        @if($meses->isEmpty())
            <div class="empty">Nenhuma entrega de EPI registada.</div>
        @else
            <div class="mes-list">
                @foreach($meses as $m)
                    <a href="{{ $m['url'] }}" class="mes-btn">
                        <span>{{ ucfirst($m['label']) }}</span>
                        <span class="badge">{{ $m['total'] }} {{ $m['total'] == 1 ? 'item' : 'itens' }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</body>
</html>
