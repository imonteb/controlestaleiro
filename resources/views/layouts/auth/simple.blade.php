<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            /* ── Auth background — same as welcome page ── */
            body { margin: 0; padding: 0; min-height: 100vh; }

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

            .auth-wrapper {
                position: relative;
                z-index: 1;
                display: flex;
                min-height: 100vh;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 1.5rem;
                padding: 1.5rem;
            }
            @media (min-width: 768px) {
                .auth-wrapper { padding: 2.5rem; }
            }

            .auth-card {
                width: 100%;
                max-width: 24rem;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                background: #2563eb;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: 0 8px 40px rgba(0,0,0,0.40);
                color-scheme: light;
            }

            /* ── Force readable contrast inside the card ── */
            .auth-card,
            .auth-card * {
                --tw-text-opacity: 1;
            }
            .auth-card h1,
            .auth-card h2,
            .auth-card p,
            .auth-card label,
            .auth-card span:not([class*="sr-only"]) {
                color: #ffffff !important;
            }
            .auth-card a {
                color: #fde68a !important;
            }
            .auth-card a:hover {
                color: #fbbf24 !important;
            }
            .auth-card input[type="email"],
            .auth-card input[type="password"],
            .auth-card input[type="text"] {
                background: #ffffff !important;
                color: #111827 !important;
                border: 1px solid #93c5fd !important;
                border-radius: 0.5rem !important;
                color-scheme: light;
            }
            .auth-card input::placeholder {
                color: #9ca3af !important;
            }
            /* Flux primary button → yellow-600 */
            .auth-card [data-test="login-button"],
            .auth-card button[type="submit"] {
                background: #ca8a04 !important;
                color: #111827 !important;
                border: none !important;
                border-radius: 0.5rem !important;
                font-weight: 700 !important;
            }
            .auth-card button[type="submit"]:hover {
                background: #a16207 !important;
            }
            /* Checkbox label */
            .auth-card [type="checkbox"] + * {
                color: #e0f2fe !important;
            }

            .auth-logo-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
                text-decoration: none;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="bg-hero"></div>
        <div class="auth-wrapper">
            <div class="auth-card">
                <a href="{{ route('home') }}" class="auth-logo-link" wire:navigate>
                    <img src="/images/procme_logo.svg" alt="CME" style="height:2.5rem;width:auto;">
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
