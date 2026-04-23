<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CME — Controlo de Estaleiro C016</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* ── Background ── */
        .bg-hero {
            position: fixed;
            inset: 0;
            background-image: url('/images/portada.webp');
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        @media (max-width: 640px) {
            .bg-hero {
                background-image: url('/images/portadamovil.webp');
                background-position: center top;
            }
        }
        /* Overlays */
        .bg-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right, rgba(9,20,59,0.58) 0%, rgba(9,20,59,0.30) 55%, rgba(9,20,59,0.05) 100%),
                linear-gradient(to top, rgba(0,0,0,0.22) 0%, transparent 50%);
        }
        @media (max-width: 640px) {
            .bg-hero::after {
                background: linear-gradient(to bottom, rgba(9,20,59,0.40) 0%, rgba(9,20,59,0.55) 60%, rgba(0,0,0,0.65) 100%);
            }
        }
        /* ── Page wrapper ── */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* ── Header ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 48px;
        }
        @media (max-width: 640px) { header { padding: 20px 24px; } }
        .logo-wrap { display: flex; align-items: center; gap: 14px; }
        .logo-img  { height: 44px; background: white; border-radius: 8px; padding: 5px 9px; }
        .logo-text {
            color: white;
            font-size: 0.88rem;
            font-weight: 600;
            line-height: 1.35;
            opacity: 0.82;
        }
        @media (max-width: 640px) { .logo-text { display: none; } }
        .btn-header {
            background: rgba(255,255,255,0.1);
            border: 1.5px solid rgba(255,255,255,0.28);
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 9px 22px;
            border-radius: 8px;
            text-decoration: none;
            backdrop-filter: blur(4px);
            transition: background .2s, border-color .2s, color .2s;
        }
        .btn-header:hover {
            background: rgba(255,211,0,0.18);
            border-color: #FFD300;
            color: #FFD300;
        }
        /* ── Hero ── */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 48px 80px;
            max-width: 700px;
        }
        @media (max-width: 640px) {
            .hero {
                max-width: 100%;
                padding: 36px 24px 56px;
                align-items: center;
                text-align: center;
            }
        }
        /* badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,211,0,0.13);
            border: 1px solid rgba(255,211,0,0.38);
            color: #FFD300;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 26px;
            width: fit-content;
        }
        .badge-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #FFD300;
            flex-shrink: 0;
            animation: blink 2.2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
        /* title */
        .hero-title {
            color: white;
            font-size: clamp(2.1rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.025em;
            margin-bottom: 22px;
        }
        .hero-title em { color: #FFD300; font-style: normal; }
        /* description */
        .hero-desc {
            color: rgba(255,255,255,0.62);
            font-size: 1.05rem;
            line-height: 1.75;
            margin-bottom: 40px;
            max-width: 520px;
        }
        /* CTA */
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #FFD300;
            color: #09143B;
            font-size: 1rem;
            font-weight: 800;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            width: fit-content;
            box-shadow: 0 4px 28px rgba(255,211,0,0.38);
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-cta:hover {
            background: #f0c800;
            transform: translateY(-2px);
            box-shadow: 0 8px 36px rgba(255,211,0,0.5);
        }
        /* chips */
        .chips { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 40px; }
        @media (max-width: 640px) { .chips { justify-content: center; } }
        .chip {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            color: rgba(255,255,255,0.72);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 7px 14px;
            border-radius: 8px;
            backdrop-filter: blur(4px);
        }
        /* ── Footer ── */
        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 18px 24px;
            color: rgba(255,255,255,0.22);
            font-size: 0.73rem;
            letter-spacing: 0.04em;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
    </style>
</head>
<body>
    <div class="bg-hero"></div>
    <div class="page">
        <header>
            <div class="logo-wrap">
                <img src="/images/procme_logo.svg" alt="CME" class="logo-img">
                <div class="logo-text">
                    Construção e Manutenção<br>Electromecânica S.A
                </div>
            </div>
            <a href="{{ route('login') }}" class="btn-header">Iniciar sessão</a>
        </header>

        <div class="hero">
            <div class="badge">
                <span class="badge-dot"></span>
                Unidade C016 &mdash; Sistema ativo
            </div>
            <h1 class="hero-title">
                Controlo de<br>
                <em>Estaleiro</em><br>
                em tempo real
            </h1>
            <p class="hero-desc">
                Gestão centralizada de equipas de trabalho, atribuição de colaboradores
                e veículos para o acompanhamento operativo da unidade C016.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('dashboard') }}" class="btn-cta">
                    Gestão de Estaleiro
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" 
                         stroke="currentColor" stroke-width="2.5" 
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="chips">
                <div class="chip">👷 Formação de equipas</div>
                <div class="chip">📱 Vista Mobile</div>
                <div class="chip">📊 Estatísticas e EPIs</div>
            </div>
        </div>

        <footer>
            &copy; {{ date('Y') }} &nbsp;&middot;&nbsp;
            CME — Construção e Manutenção Electromecânica S.A &nbsp;&middot;&nbsp; Unidade C016
        </footer>
    </div>

    <script>
        (function() {
            // Check for manual override in current URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('manual')) {
                console.log('Manual mode detected via URL.');
                return;
            }
            
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            const isMobile = /Mobile|Android|BlackBerry|iPhone|Windows Phone/i.test(userAgent);

            console.log('Detecting device...', isMobile ? 'Mobile' : 'PC');

            if (isMobile) {
                window.location.href = "{{ route('phone') }}";
            } else {
                window.location.href = "{{ route('dashboard') }}";
            }
        })();
    </script>
</body>
</html>