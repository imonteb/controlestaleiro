<div class="flex items-center gap-4 mr-4" wire:poll.20s>
    {{-- EPI Requests --}}
    <a href="{{ route('epis.pedidos') }}" wire:navigate class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500/10 border border-yellow-500/20 text-yellow-600 hover:bg-yellow-500/20 transition-colors group" title="Pedidos de EPI Pendentes">
        <flux:icon.hand-raised variant="mini" />
        @if($epiCount > 0)
            <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white shadow-sm">
                {{ $epiCount }}
            </span>
        @endif
    </a>

    {{-- Transport Guides --}}
    <a href="{{ route('guias.index') }}" wire:navigate class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-600 hover:bg-blue-500/20 transition-colors group" title="Guias de Transporte Pendentes">
        <flux:icon.truck variant="mini" />
        @if($guiaCount > 0)
            <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white shadow-sm">
                {{ $guiaCount }}
            </span>
        @endif
    </a>

    {{-- Incident Reports --}}
    <a href="{{ route('seguranca.index', ['tab' => 'incidentes']) }}" wire:navigate class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 hover:bg-red-500/20 transition-colors group" title="Novos Relatórios de Incidente / Alertas SOS">
        <flux:icon.bell-alert variant="mini" />
        @if($incidentCount > 0)
            <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white shadow-sm">
                {{ $incidentCount }}
            </span>
        @endif
    </a>

    {{-- Veículos sem condutor --}}
    @if($semCondutorCount > 0)
    <a href="{{ route('condutores.registo') }}" wire:navigate class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-500 hover:bg-amber-500/20 transition-colors group animate-pulse" title="{{ $semCondutorCount }} veículo(s) sem condutor registado">
        <flux:icon.truck variant="mini" />
        <span class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold text-black shadow-sm">
            {{ $semCondutorCount }}
        </span>
    </a>
    @endif
</div>
