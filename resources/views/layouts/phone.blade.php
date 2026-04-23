<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CME C016 — Equipas do Dia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#eab308">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body {
            background: linear-gradient(160deg, #100142 0%, #100142 70%, #100142 100%);
            /* background: linear-gradient(160deg, #0f172a 0%, #1e3a8a 70%, #0f172a 100%); */
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            overscroll-behavior-y: contain;
        }
        .ph-search:focus { outline: none; box-shadow: 0 0 0 2px #fde047; }
        .ph-card { transition: opacity 0.15s; }
        .ph-card.hidden { display: none !important; }
        .ph-highlight { background: rgba(253,224,71,0.25); border-radius: 3px; padding: 0 2px; }
    </style>
</head>
<body class="antialiased" style="padding-bottom: 80px;">
    {{ $slot }}

    <x-global-footer />

    <script>
        // ── SERVICE WORKER + WEB PUSH ─────────────────────────────
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            window.addEventListener('load', async () => {
                try {
                    const reg = await navigator.serviceWorker.register('/service-worker.js');
                    console.log('SW registered');

                    // Ativa as notificações push quando o colaborador faz login
                    window.activatePushNotifications = async (colaboradorId) => {
                        if (!colaboradorId) return;

                        // Já subscritos neste browser? Não pede permissão de novo
                        const existing = await reg.pushManager.getSubscription();
                        if (existing) {
                            await syncSubscription(existing, colaboradorId);
                            return;
                        }

                        const permission = await Notification.requestPermission();
                        if (permission !== 'granted') return;

                        // Obtém a VAPID public key do servidor
                        const keyRes = await fetch('/push/public-key');
                        const { publicKey } = await keyRes.json();

                        const subscription = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(publicKey),
                        });

                        await syncSubscription(subscription, colaboradorId);
                    };

                    async function syncSubscription(subscription, colaboradorId) {
                        const key   = subscription.getKey('p256dh');
                        const auth  = subscription.getKey('auth');
                        await fetch('/push/subscribe', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                            body: JSON.stringify({
                                colaborador_id: colaboradorId,
                                endpoint: subscription.endpoint,
                                p256dh: btoa(String.fromCharCode(...new Uint8Array(key))),
                                auth:   btoa(String.fromCharCode(...new Uint8Array(auth))),
                            }),
                        });
                    }
                } catch (err) {
                    console.error('SW registration failed', err);
                }
            });
        } else if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw     = atob(base64);
            return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
        }

        // ── Som de notificação ────────────────────────────────────
        let _notifAudioCtx = null;
        let _audioUnlocked = false;

        function _getAudioCtx() {
            if (!_notifAudioCtx) {
                _notifAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            return _notifAudioCtx;
        }

        // Desbloquear AudioContext na primeira interação do utilizador
        document.addEventListener('click', function() {
            const ctx = _getAudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume().then(() => { _audioUnlocked = true; });
            } else {
                _audioUnlocked = true;
            }
        }, { once: false });

        function _playTones(ctx) {
            const playTone = (freq, start, dur) => {
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0, start);
                gain.gain.linearRampToValueAtTime(0.35, start + 0.01);
                gain.gain.linearRampToValueAtTime(0, start + dur);
                osc.start(start);
                osc.stop(start + dur);
            };
            const t = ctx.currentTime;
            playTone(880,  t,        0.15);
            playTone(1100, t + 0.20, 0.15);
        }

        function playNotificationSound() {
            try {
                const ctx = _getAudioCtx();
                if (ctx.state === 'running') {
                    _playTones(ctx);
                } else if (ctx.state === 'suspended') {
                    ctx.resume().then(() => _playTones(ctx)).catch(() => {});
                }
            } catch (e) {}
        }

        // Recebe mensagens do Service Worker (PUSH_RECEIVED → som + refresh)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data?.type !== 'PUSH_RECEIVED') return;
                playNotificationSound();
                Livewire.dispatch('refresh-notifications');
            });
        }

        // Desbloquear AudioContext ao primeiro toque na página (para Chrome)
        // Mostra um aviso subtil se o áudio ainda não foi desbloqueado após 3s
        setTimeout(() => {
            if (_audioUnlocked) return;
            const banner = document.createElement('div');
            banner.id = 'audio-unlock-banner';
            banner.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(30,41,59,0.95);border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.7);font-size:11px;padding:6px 14px;border-radius:20px;z-index:9999;pointer-events:none;white-space:nowrap;';
            banner.textContent = '🔔 Toque na página para ativar o som das notificações';
            document.body.appendChild(banner);
            document.addEventListener('click', () => {
                banner.remove();
            }, { once: true });
        }, 3000);

        // Ativa push quando o colaborador faz login
        window.addEventListener('colaborador-loggedin', e => {
            if (window.activatePushNotifications) {
                window.activatePushNotifications(e.detail.colaboradorId);
            }
        });

        // Auto-logout por inatividade (10 minutos)
        ;(function () {
            const TIMEOUT_MS = 10 * 60 * 1000;
            let timer;

            function resetTimer() {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    // Só faz logout se houver colaborador activo
                    const liveEl = document.querySelector('[wire\\:id]');
                    if (!liveEl) return;
                    const wireId = liveEl.getAttribute('wire:id');
                    const component = Livewire.find(wireId);
                    if (component && component.get('activeColaboradorId')) {
                        component.call('logoutColaborador');
                    }
                }, TIMEOUT_MS);
            }

            ['touchstart', 'touchmove', 'click', 'keydown', 'scroll'].forEach(function (evt) {
                document.addEventListener(evt, resetTimer, { passive: true });
            });

            resetTimer();
        })();
    </script>
</body>
</html>
