<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[#EEECEA]">
        <style>
            [data-flux-sidebar][data-flux-sidebar-collapsed-desktop] {
                display: none !important;
            }
        </style>
        <flux:sidebar sticky collapsible class="border-e border-blue-700 bg-blue-950 dark:border-blue-700 dark:bg-blue-950">
            <flux:sidebar.header>
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-2 py-2">
                    <div class="sidebar-logo-wrapper">
                        <img src="/images/procme_logo.svg" alt="CME Logo" class="sidebar-logo" />
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-bold text-yellow-600">CME C016</span>
                        <span class="text-xs text-white">Construção e Manutenção Electromecânica S.A</span>
                    </div>
                </a>
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Principal" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Equipas" expandable :open="request()->routeIs('gestao-equipas') || request()->routeIs('publicar-dia') || request()->routeIs('monitor') || request()->routeIs('tv') || request()->routeIs('phone')" class="grid">
                    <flux:sidebar.item icon="calendar-days" :href="route('gestao-equipas')" :current="request()->routeIs('gestao-equipas')" wire:navigate>
                        Gestão de Equipas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="presentation-chart-bar" :href="route('publicar-dia')" :current="request()->routeIs('publicar-dia')" wire:navigate>
                        Publicar em TV
                    </flux:sidebar.item>
                    
                    <flux:menu.separator class="my-2" />

                    <flux:sidebar.item icon="computer-desktop" :href="route('monitor')" :current="request()->routeIs('monitor')" wire:navigate>
                        Monitor de Equipas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tv" :href="route('tv')" target="_blank">
                        Painel para TV (65"+)
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="device-phone-mobile" :href="route('phone')" target="_blank">
                        Vista Mobile
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Estatísticas" expandable class="grid">
                    <flux:sidebar.item icon="table-cells" :href="route('resumo-mensal')" :current="request()->routeIs('resumo-mensal')" wire:navigate>
                        Resumo Mensal
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('estatisticas.veiculos')" :current="request()->routeIs('estatisticas.veiculos')" wire:navigate>
                        Estatísticas de Veículos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-circle" :href="route('estatisticas.colaboradores')" :current="request()->routeIs('estatisticas.colaboradores')" wire:navigate>
                        Estatísticas de Colaboradores
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Dados Base" expandable class="grid">
                    <flux:sidebar.item icon="users" :href="route('colaboradores.index')" :current="request()->routeIs('colaboradores.*')" wire:navigate>
                        Colaboradores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('veiculos.index')" :current="request()->routeIs('veiculos.index') || request()->routeIs('veiculos.crear') || request()->routeIs('veiculos.editar')" wire:navigate>
                        Veículos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard" :href="route('peps.index')" :current="request()->routeIs('peps.*')" wire:navigate>
                        PEPs
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()?->isEpi())
                <flux:sidebar.group heading="EPIs" expandable class="grid">
                    <flux:sidebar.item icon="shield-check" :href="route('epis.index')" :current="request()->routeIs('epis.index') || request()->routeIs('epis.crear') || request()->routeIs('epis.editar')" wire:navigate>
                        Catálogo
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="hand-raised" :href="route('epis.entregas.index')" :current="request()->routeIs('epis.entregas.*')" wire:navigate>
                        Entregas
                    </flux:sidebar.item>
                    
                    @php $pedidosCount = \App\Models\EpiPedido::where('estado', 'pendente')->count(); @endphp
                    <flux:sidebar.item icon="bell" :href="route('epis.pedidos')" :current="request()->routeIs('epis.pedidos')" wire:navigate>
                        Pedidos de EPI
                        @if($pedidosCount > 0)
                            <flux:badge size="sm" color="red" class="ml-auto">{{ $pedidosCount }}</flux:badge>
                        @endif
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="archive-box-arrow-down" :href="route('epis.rececoes.index')" :current="request()->routeIs('epis.rececoes.*')" wire:navigate>
                        Recepções
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-check" :href="route('epis.inventario')" :current="request()->routeIs('epis.inventario')" wire:navigate>
                        Inventário
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('epis.historico')" :current="request()->routeIs('epis.historico')" wire:navigate>
                        Histórico Mensal
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('epis.dotacao')" :current="request()->routeIs('epis.dotacao')" wire:navigate>
                        Dotação
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Transporte" expandable :open="request()->routeIs('guias.*')" class="grid">
                    <flux:sidebar.item icon="document-text" :href="route('guias.index')" :current="request()->routeIs('guias.*')" wire:navigate>
                        Guias de Transporte
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('condutores.registo')" :current="request()->routeIs('condutores.*')" wire:navigate>
                        Registo de Condução
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Logística e Segurança" expandable class="grid">
                    <flux:sidebar.item icon="wrench" :href="route('ferramentas.index')" :current="request()->routeIs('ferramentas.*')" wire:navigate>
                        Ferramentas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="square-3-stack-3d" :href="route('gestao-activos')" :current="request()->routeIs('gestao-activos')" wire:navigate>
                        Gestão de Activos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="fire" :href="route('extintores.index')" :current="request()->routeIs('extintores.*')" wire:navigate>
                        Extintores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('materiais.index')" :current="request()->routeIs('materiais.*') || request()->routeIs('material-categorias.*')" wire:navigate>
                        Catálogo de Materiais
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map-pin" :href="route('locais-frequentes.index')" :current="request()->routeIs('locais-frequentes.*')" wire:navigate>
                        Locais Frequentes
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endif

                @if(auth()->user()?->isAdmin())
                <flux:sidebar.group heading="Administração" expandable class="grid">
                    <flux:sidebar.item icon="user-plus" :href="route('register')" :current="request()->routeIs('register')" wire:navigate>
                        Registar Utilizador
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('utilizadores.index')" :current="request()->routeIs('utilizadores.*')" wire:navigate>
                        Gerir Utilizadores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="key" :href="route('sessoes.index')" :current="request()->routeIs('sessoes.*')" wire:navigate>
                        Gerir Sessões
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-up-tray" :href="route('importar')" :current="request()->routeIs('importar*')" wire:navigate>
                        Importar Dados
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="megaphone" :href="route('avisos-tv.index')" :current="request()->routeIs('avisos-tv.*')" wire:navigate>
                        Avisos TV
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bell-alert" :href="route('notificacoes.index')" :current="request()->routeIs('notificacoes.*')" wire:navigate>
                        Avisos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="lifebuoy" :href="route('seguranca.index')" :current="request()->routeIs('seguranca.*')" wire:navigate>
                        Segurança & Apoio
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('legal-pages.index')" :current="request()->routeIs('legal-pages.*')" wire:navigate>
                        Páginas Legais
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('tipos-trabalho.index')" :current="request()->routeIs('tipos-trabalho.*')" wire:navigate>
                        Tipos de Trabalho
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map-pin" :href="route('localizacoes.index')" :current="request()->routeIs('localizacoes.*')" wire:navigate>
                        Localizações
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="block" :name="auth()->user()->name" />

            <div style="padding: 6px 12px 8px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 4px;">
                <span style="font-size: 0.65rem; color: rgba(255,255,255,0.25); font-weight: 500; letter-spacing: 0.04em;">
                    v1.0.0 &middot; CME C016
                </span>
            </div>
</flux:sidebar>

        <!-- Top navigation bar -->
        <flux:header class="bg-blue-950 border-b border-[rgba(255,255,255,0.08)]">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            @livewire('global-header-notifications')

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    :name="auth()->user()->name"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        <script data-navigate-once>
            // ── Som de notificação ────────────────────────────────────
            // Criamos o AudioContext uma vez e desbloqueamos na primeira interação.
            // Sem isso o browser bloqueia o som em tabs em background.
            let _notifAudioCtx = null;

            function _getAudioCtx() {
                if (!_notifAudioCtx) {
                    _notifAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                return _notifAudioCtx;
            }

            // Desbloquear na primeira interação do utilizador
            document.addEventListener('click', function unlockAudio() {
                const ctx = _getAudioCtx();
                if (ctx.state === 'suspended') ctx.resume();
            });

            function playNotificationSound() {
                try {
                    const ctx = _getAudioCtx();
                    const doPlay = () => {
                        const playTone = (freq, start, dur) => {
                            const osc  = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.type = 'sine';
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0, start);
                            gain.gain.linearRampToValueAtTime(0.3, start + 0.01);
                            gain.gain.linearRampToValueAtTime(0, start + dur);
                            osc.start(start);
                            osc.stop(start + dur);
                        };
                        const t = ctx.currentTime;
                        playTone(880,  t,        0.15);
                        playTone(1100, t + 0.20, 0.15);
                    };
                    if (ctx.state === 'suspended') {
                        ctx.resume().then(doPlay);
                    } else {
                        doPlay();
                    }
                } catch (e) {}
            }

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then(reg => console.log('Service Worker registered', reg))
                        .catch(err => console.error('Service Worker registration failed', err));
                });

                // Ouvir push do service worker → tocar som
                navigator.serviceWorker.addEventListener('message', event => {
                    if (event.data?.type === 'PUSH_RECEIVED') {
                        playNotificationSound();
                    }
                });
            }
        </script>
        @stack('scripts')
    </body>
</html>
