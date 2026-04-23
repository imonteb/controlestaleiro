<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Assinatura Digital - CME</title>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #facc15;
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            flex: 1;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .items-list {
            margin-bottom: 20px;
            border-bottom: 1px solid #334155;
            padding-bottom: 15px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .signature-container {
            position: relative;
            width: 100%;
            height: 320px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 15px;
            touch-action: none;
        }

        canvas {
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        button {
            flex: 1;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-clear {
            background-color: #334155;
            color: #f8fafc;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: #000;
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .footer-note {
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.4;
        }

        #success-message {
            display: none;
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            font-size: 48px;
            color: #22c55e;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container" id="sign-view">
        <header>
            <div class="logo">CME</div>
            <div class="title">Solicitação de Assinatura</div>
            <div class="subtitle">Por favor, assine abaixo para confirmar a receção.</div>
        </header>

        <div class="card">
            @if($request->metadata && isset($request->metadata['type']) && $request->metadata['type'] === 'epi_delivery')
                <div style="margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #334155;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Colaborador</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--primary-color); line-height: 1.2;">
                        {{ $request->metadata['colaborador_nombre'] }} 
                    </div>
                    @if($request->metadata['colaborador_numero'])
                        <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Nº Mecanográfico: <strong>{{ $request->metadata['colaborador_numero'] }}</strong></div>
                    @endif
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Equipamentos a Receber</div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @foreach($request->metadata['items'] as $item)
                            <div style="background: rgba(255,255,255,0.05); padding: 10px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 500;">{{ $item['nombre'] }}</span>
                                @if(isset($item['talla']) && $item['talla'])
                                    <span style="background: var(--primary-color); color: #000; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">{{ $item['talla'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="background: #0f172a; padding: 12px; border-radius: 10px; border-left: 4px solid var(--primary-color); margin-bottom: 20px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);">
                    <strong style="font-size: 11px; color: var(--primary-color); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">■ Declaração</strong>
                    <p style="font-size: 11px; line-height: 1.6; margin: 0; text-align: justify; color: #cbd5e1; font-style: italic;">
                        Declaro que recebi os Equipamentos de Protecção Individual (EPI) acima mencionados, gratuitamente, e que fui informado dos respectivos riscos que pretendem proteger, comprometendo-me a utilizá-los correctamente de acordo com las instruções recebidas, a conservá-los e mantê-los em bom estado e a participar ao meu superior hierárquico todas as avarias ou deficiências de que tenha conhecimento.
                    </p>
                </div>

                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; margin-bottom: 24px; padding: 12px; background: rgba(250, 204, 21, 0.05); border-radius: 10px; border: 1px dashed rgba(250, 204, 21, 0.3);">
                    <input type="checkbox" id="terms" style="margin-top: 2px; min-width: 20px; min-height: 20px; accent-color: var(--primary-color); cursor: pointer;">
                    <span style="font-size: 12px; line-height: 1.5; color: #f8fafc;">Li e compreendi a declaração acima e confirmo a receção dos equipamentos listados.</span>
                </label>
            @else
                <div class="items-list">
                    @if($request->metadata && isset($request->metadata['colaborador_nombre']))
                        <div style="margin-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Assinante</div>
                            <div style="font-size: 16px; font-weight: 600; color: #fff;">{{ $request->metadata['colaborador_nombre'] }}</div>
                            @if(isset($request->metadata['colaborador_numero']) && $request->metadata['colaborador_numero'])
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Nº {{ $request->metadata['colaborador_numero'] }}</div>
                            @endif
                        </div>
                    @endif

                    <strong>Assunto:</strong> 
                    @if($signable instanceof \App\Models\User)
                        Firma Oficial de Utilizador
                    @elseif($signable instanceof \App\Models\EpiEntrega)
                        Entrega de EPI
                    @elseif($signable instanceof \App\Models\FerramentaLog)
                        Verificação de Ferramenta
                    @else
                        Documentação CME
                    @endif
                </div>
            @endif

            <div class="signature-container">
                <canvas id="signature-pad"></canvas>
            </div>

            <div class="actions">
                <button type="button" class="btn-clear" id="clear">Limpar</button>
                <button type="button" class="btn-submit" id="save">Confirmar</button>
            </div>
        </div>

        <div class="footer-note">
            Ao assinar, confirma que recebeu os itens ou aceita as condições descritas.<br>
            Esta assinatura é legalmente vinculativa. IP: {{ request()->ip() }}
        </div>
    </div>

    <div class="container" id="success-view" style="display: none;">
        <div class="card" style="text-align: center;">
            <div class="success-icon">✓</div>
            <h2 class="title">Obrigado!</h2>
            <p class="subtitle">A sua assinatura foi enviada com sucesso.</p>
            <p class="subtitle" style="margin-top: 6px;">A regresar à app em <strong id="countdown" style="color: var(--primary-color);">10</strong>s...</p>
            <a href="/phone" id="btn-voltar"
               style="display: inline-block; margin-top: 24px; padding: 14px 32px; background: var(--primary-color); color: #000; font-weight: 700; font-size: 15px; border-radius: 10px; text-decoration: none; letter-spacing: 0.02em;">
                ← Voltar à App
            </a>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signature-pad');

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(0,0,0,0)',
            penColor: 'rgb(30,64,175)',
            minWidth: 3.0,
            maxWidth: 6.0
        });

        document.getElementById('clear').addEventListener('click', () => signaturePad.clear());

        document.getElementById('save').addEventListener('click', async () => {
            const termsBox = document.getElementById('terms');
            if (termsBox && !termsBox.checked) {
                alert("Por favor, aceite a declaração antes de confirmar.");
                return;
            }
            if (signaturePad.isEmpty()) {
                alert("Por favor, assine antes de confirmar.");
                return;
            }

            const button = document.getElementById('save');
            button.disabled = true;
            button.innerText = "Enviando...";

            try {
                const response = await fetch('{{ route("signature.store", $request->token) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ signature: signaturePad.toSVG() })
                });

                if (response.ok) {
                    document.getElementById('sign-view').style.display = 'none';
                    document.getElementById('success-view').style.display = 'block';
                    let secs = 10;
                    const countdown = document.getElementById('countdown');
                    const timer = setInterval(() => {
                        if (--secs <= 0) { clearInterval(timer); window.location.href = '/phone'; }
                        if (countdown) countdown.textContent = secs;
                    }, 1000);
                } else {
                    alert("Erro ao enviar a assinatura. Por favor, tente novamente.");
                    button.disabled = false;
                    button.innerText = "Confirmar";
                }
            } catch (error) {
                console.error(error);
                alert("Erro de conexão. Verifique a sua internet.");
                button.disabled = false;
                button.innerText = "Confirmar";
            }
        });
    </script>
</body>
</html>
