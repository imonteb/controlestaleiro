<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1920, initial-scale=1.0, minimum-scale=1.0">
    <title>CME C016 — Painel de Equipas</title>
    <script>
        // Auto-refresh each 5 minutes
        setTimeout(() => window.location.reload(), 5 * 60 * 1000);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #0f172a 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* Responsive grid — optimised for Philips 65PUS7406/12 (65" 4K UHD 3840×2160) */
        .tv-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr;
        }
        /* Mobile */
        @media (min-width: 480px)  { .tv-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
        /* Tablet portrait */
        @media (min-width: 768px)  { .tv-grid { grid-template-columns: repeat(3, 1fr); gap: 14px; } }
        /* Laptop / small desktop */
        @media (min-width: 1100px) { .tv-grid { grid-template-columns: repeat(4, 1fr); gap: 14px; } }
        /* Large desktop / 1440p */
        @media (min-width: 1400px) { .tv-grid { grid-template-columns: repeat(5, 1fr); gap: 16px; } }
        /* Full HD 1080p connected display */
        @media (min-width: 1700px) { .tv-grid { grid-template-columns: repeat(6, 1fr); gap: 16px; } }
        /* 1920px — 1080p TV or browser at 200% on 4K */
        @media (min-width: 1920px) { .tv-grid { grid-template-columns: repeat(7, 1fr); gap: 12px; } }
        /* 4K UHD — 65PUS7406/12 native 3840px viewport at 100% Windows scaling */
        @media (min-width: 3200px) { .tv-grid { grid-template-columns: repeat(7, 1fr); gap: 24px; } }

        /* 1080p: compact cards to fit 7 cols */
        @media (min-width: 1920px) and (max-width: 3199px) {
            .tv-card-header  { padding: 10px 12px !important; }
            .tv-card-body    { padding: 10px 12px !important; gap: 6px !important; }
            .tv-pep-name     { font-size: 0.88rem !important; }
            .tv-col-name     { font-size: 0.80rem !important; }
            .tv-col-item     { padding: 5px 9px !important; margin-bottom: 3px !important; border-radius: 6px !important; }
            .tv-grid-wrap    { padding: 10px !important; }
            .tv-header       { padding: 10px 20px !important; }
            .tv-header-date  { font-size: 1.05rem !important; }
            .tv-header-title { font-size: 0.82rem !important; }
            .tv-header-logo  { height: 38px !important; }
            .tv-clock        { font-size: 1.5rem !important; }
        }

        /* 4K native: generous sizing — each card ~540px wide */
        @media (min-width: 3200px) {
            .tv-card-header  { padding: 20px 24px !important; }
            .tv-card-body    { padding: 18px 24px !important; gap: 12px !important; }
            .tv-pep-name     { font-size: 1.35rem !important; }
            .tv-col-name     { font-size: 1.1rem !important; }
            .tv-col-item     { padding: 10px 16px !important; margin-bottom: 7px !important; border-radius: 10px !important; }
            .tv-grid-wrap    { padding: 24px !important; }
            .tv-header       { padding: 24px 48px !important; }
            .tv-header-date  { font-size: 2rem !important; }
            .tv-header-title { font-size: 1.3rem !important; }
            .tv-header-logo  { height: 72px !important; }
            .tv-clock        { font-size: 2.8rem !important; }
        }
    </style>
</head>
<body class="min-h-screen">
    {{ $slot }}
</body>
</html>
