<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-linear-to-b from-blue-800 to-blue-950 dark:from-blue-900 dark:to-(--cme-blue)">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item icon="table-cells" :href="route('resumo-mensal')" :current="request()->routeIs('resumo-mensal')" wire:navigate>
                    Resumo Mensal
                </flux:navbar.item>
                <flux:navbar.item icon="users" :href="route('colaboradores.index')" :current="request()->routeIs('colaboradores.*')" wire:navigate>
                    Colaboradores
                </flux:navbar.item>
                <flux:navbar.item icon="truck" :href="route('veiculos.index')" :current="request()->routeIs('veiculos.*')" wire:navigate>
                    Veículos
                </flux:navbar.item>
                <flux:navbar.item icon="clipboard" :href="route('peps.index')" :current="request()->routeIs('peps.*')" wire:navigate>
                    PEPs
                </flux:navbar.item>
                <flux:navbar.item icon="briefcase" :href="route('tipos-trabalho.index')" :current="request()->routeIs('tipos-trabalho.*')" wire:navigate>
                    Tipos de Trabalho
                </flux:navbar.item>
                <flux:navbar.item icon="map-pin" :href="route('localizacoes.index')" :current="request()->routeIs('localizacoes.*')" wire:navigate>
                    Localizações
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="h-10! [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="table-cells" :href="route('resumo-mensal')" :current="request()->routeIs('resumo-mensal')" wire:navigate>
                        Resumo Mensal
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="presentation-chart-bar" :href="route('publicar-dia')" :current="request()->routeIs('publicar-dia')" wire:navigate>
                        Publicar em TV
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('colaboradores.index')" :current="request()->routeIs('colaboradores.*')" wire:navigate>
                        Colaboradores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('veiculos.index')" :current="request()->routeIs('veiculos.*')" wire:navigate>
                        Veículos
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard" :href="route('peps.index')" :current="request()->routeIs('peps.*')" wire:navigate>
                        PEPs
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('tipos-trabalho.index')" :current="request()->routeIs('tipos-trabalho.*')" wire:navigate>
                        Tipos de Trabalho
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map-pin" :href="route('localizacoes.index')" :current="request()->routeIs('localizacoes.*')" wire:navigate>
                        Localizações
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()?->isEpi())
                <flux:sidebar.group :heading="__('EPIs')">
                    <flux:sidebar.item icon="shield-check" :href="route('epis.index')" :current="request()->routeIs('epis.index')" wire:navigate>
                        Catálogo
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="hand-raised" :href="route('epis.entregas.index')" :current="request()->routeIs('epis.entregas.*')" wire:navigate>
                        Entregas
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
                </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
