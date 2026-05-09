<div class="p-4 max-w-4xl">
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">

        {{-- HEADER --}}
        <div style="background:#09143B;" class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="tv" class="text-[#FFD300] w-4 h-4" />
                <span style="color:white;" class="text-sm font-medium">Publicar Dia na TV</span>
            </div>
            <a href="{{ $urlTv }}" target="_blank"
               style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; text-decoration:none; white-space:nowrap;">
                📺 Abrir Painel TV
            </a>
        </div>

        {{-- BODY --}}
        <div style="background:#EEECEA;" class="p-4 flex flex-col gap-4">

            {{-- MENSAGEM OK --}}
            @if($mensajeOk)
            <div style="background:#d4ede4; border:1px solid rgba(15,110,86,0.25); color:#0F6E56; border-radius:8px; padding:10px 14px; font-size:12px; font-weight:600;">
                ✅ {{ $mensajeOk }}
            </div>
            @endif

            {{-- ESTADO ATUAL DA TV --}}
            @php $diaActivo = $dias->firstWhere('id', (int)$diaActivoId); @endphp
            <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.12)]">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center gap-2">
                    <flux:icon name="signal" class="text-[#FFD300] w-4 h-4" />
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">A mostrar atualmente na TV</span>
                </div>
                <div style="background:#F0EEEB;" class="px-4 py-3 flex items-center justify-between gap-4 flex-wrap">
                    @if($diaActivo)
                    <div>
                        <div style="color:#1A1A1A;" class="text-[13px] font-bold capitalize">{{ $diaActivo->fecha_formato }}</div>
                        <div style="color:#7A7775;" class="text-[11px] mt-0.5">
                            {{ $diaActivo->num_colaboradores }} colaborador{{ $diaActivo->num_colaboradores !== 1 ? 'es' : '' }} atribuído{{ $diaActivo->num_colaboradores !== 1 ? 's' : '' }}
                        </div>
                    </div>
                    <button wire:click="desactivarTv" type="button"
                            style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.25); padding:6px 14px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer;">
                        ✕ Desativar TV
                    </button>
                    @else
                    <span style="color:#7A7775;" class="text-[12px] italic">
                        Nenhum dia está ativo — o painel TV mostrará a mensagem "Painel não disponível".
                    </span>
                    @endif
                </div>
            </div>

            {{-- LISTA DE DIAS --}}
            <div class="rounded-lg overflow-hidden border border-[rgba(9,20,59,0.12)]">
                <div style="background:#09143B;" class="px-3 py-2 flex items-center gap-2">
                    <flux:icon name="calendar-days" class="text-[#FFD300] w-4 h-4" />
                    <span style="color:white;" class="text-[11px] font-medium uppercase tracking-widest">Dias marcados como prontos</span>
                </div>
                <div style="background:#F0EEEB;" class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($dias as $dia)
                    @php $esActivo = (string)$dia->id === $diaActivoId; @endphp
                    <div style="background:{{ $esActivo ? 'rgba(9,20,59,0.04)' : 'transparent' }}; {{ $esActivo ? 'border-left:3px solid #09143B;' : '' }}"
                         class="px-4 py-3 flex items-center gap-4 flex-wrap">
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex items-center gap-2">
                                <span style="color:#1A1A1A;" class="text-[13px] font-bold capitalize">{{ $dia->fecha_formato }}</span>
                                @if($esActivo)
                                <span style="background:#09143B; color:#FFD300; font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px;">EN TV</span>
                                @endif
                            </div>
                            <div style="color:#7A7775;" class="text-[11px] mt-0.5">
                                {{ $dia->num_colaboradores }} colaborador{{ $dia->num_colaboradores !== 1 ? 'es' : '' }} atribuído{{ $dia->num_colaboradores !== 1 ? 's' : '' }}
                                · Publicado {{ $dia->created_at->diffForHumans() }}
                            </div>
                            <div class="mt-1.5">
                                @if($dia->confirmado_at)
                                <span style="background:#d4ede4; border:1px solid rgba(15,110,86,0.25); color:#0F6E56; font-size:10px; font-weight:600; padding:2px 8px; border-radius:4px;">
                                    ✓ Confirmado por {{ $dia->confirmadoPor->name ?? '—' }} · {{ $dia->confirmado_at->format('d/m/Y H:i') }}
                                </span>
                                @else
                                <span style="background:#fdf0c2; border:1px solid rgba(133,79,11,0.20); color:#854F0B; font-size:10px; font-weight:600; padding:2px 8px; border-radius:4px;">
                                    ⏳ Por confirmar
                                </span>
                                @endif
                            </div>
                        </div>
                        @if(!$esActivo)
                        <button wire:click="activar({{ $dia->id }})" type="button"
                                style="background:#09143B; color:#FFD300; border:none; padding:7px 16px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; white-space:nowrap;">
                            📺 Mostrar na TV
                        </button>
                        @else
                        <span style="background:#d4ede4; color:#0F6E56; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px;">
                            ✅ Ativo
                        </span>
                        @endif
                    </div>
                    @empty
                    <div style="color:#7A7775; background:#EEECEA;" class="text-[12px] px-4 py-8 text-center italic">
                        Ainda não há dias publicados. Ative o toggle "Pronto para TV" no Dashboard para publicar um dia.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- /body --}}
    </div>{{-- /card principal --}}
</div>
