<div class="p-4 lg:p-6 flex flex-col gap-6 max-w-4xl">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold uppercase text-yellow-600">Publicar Dia na TV</h1>
            <p style="color:white;font-size:0.88rem;font-weight:600;margin-top:2px;">
                Selecione o dia a apresentar no painel da televisão
            </p>
        </div>
        <a href="{{ $urlTv }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;background:#1e40af;color:white;font-weight:700;padding:10px 20px;border-radius:10px;font-size:0.85rem;text-decoration:none;border:2px solid #3b82f6;white-space:nowrap;"
           onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#1e40af'">
            📺 Abrir Painel TV
        </a>
    </div>

    {{-- Mensagem OK --}}
    @if($mensajeOk)
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 18px;color:#166534;font-size:0.88rem;font-weight:700;">
        ✅ {{ $mensajeOk }}
    </div>
    @endif

    {{-- Estado atual da TV --}}
    @php $diaActivo = $dias->firstWhere('id', (int)$diaActivoId); @endphp
    <div style="background:rgba(30,58,138,0.85);border:1.5px solid #3b82f6;border-radius:14px;padding:18px 22px;">
        <div style="color:#93c5fd;font-size:0.72rem;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:8px;">
            A mostrar atualmente na TV
        </div>
        @if($diaActivo)
        <div class="flex items-center gap-4 flex-wrap">
            <div>
                <div style="color:#fde047;font-size:1.1rem;font-weight:800;text-transform:capitalize;">
                    {{ $diaActivo->fecha_formato }}
                </div>
                <div style="color:#6ee7b7;font-size:0.82rem;margin-top:3px;">
                    {{ $diaActivo->num_colaboradores }} colaborador{{ $diaActivo->num_colaboradores !== 1 ? 'es' : '' }} atribuído{{ $diaActivo->num_colaboradores !== 1 ? 's' : '' }}
                </div>
            </div>
            <button wire:click="desactivarTv" type="button"
                    style="background:#7f1d1d;color:#fca5a5;border:1px solid #ef4444;padding:7px 16px;border-radius:8px;font-size:0.8rem;font-weight:700;cursor:pointer;margin-left:auto;"
                    onmouseover="this.style.background='#991b1b'" onmouseout="this.style.background='#7f1d1d'">
                ✕ Desativar TV
            </button>
        </div>
        @else
        <div style="color:rgba(255,255,255,0.4);font-size:0.9rem;font-style:italic;">
            Nenhum dia está ativo — o painel TV mostrará a mensagem "Painel não disponível".
        </div>
        @endif
    </div>

    {{-- Lista de dias publicados --}}
    <div>
        <h2 style="color:white;font-size:1rem;font-weight:700;margin-bottom:12px;">Dias marcados como prontos</h2>
        @forelse($dias as $dia)
        @php $esActivo = (string)$dia->id === $diaActivoId; @endphp
        <div style="background:{{ $esActivo ? 'rgba(30,64,175,0.4)' : 'rgba(255,255,255,0.05)' }};border:{{ $esActivo ? '2px solid #3b82f6' : '1px solid rgba(255,255,255,0.1)' }};border-radius:12px;padding:14px 20px;margin-bottom:10px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <div style="color:{{ $esActivo ? '#fde047' : 'white' }};font-size:0.95rem;font-weight:700;text-transform:capitalize;">
                    {{ $dia->fecha_formato }}
                    @if($esActivo)
                    <span style="background:#3b82f6;color:white;font-size:0.65rem;font-weight:800;padding:2px 8px;border-radius:10px;margin-left:8px;letter-spacing:0.05em;vertical-align:middle;">
                        EN TV
                    </span>
                    @endif
                </div>
                <div style="color:rgba(255,255,255,0.45);font-size:0.78rem;margin-top:3px;">
                    {{ $dia->num_colaboradores }} colaborador{{ $dia->num_colaboradores !== 1 ? 'es' : '' }} atribuído{{ $dia->num_colaboradores !== 1 ? 's' : '' }}
                    &middot; Publicado {{ $dia->created_at->diffForHumans() }}
                </div>
                <div style="margin-top:5px;">
                    @if($dia->confirmado_at)
                        <span style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.4);color:#86efac;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;">
                            ✓ Confirmado por {{ $dia->confirmadoPor->name ?? '—' }} · {{ $dia->confirmado_at->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <span style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);color:#fcd34d;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;">
                            ⏳ Por confirmar
                        </span>
                    @endif
                </div>
            </div>
            @if(!$esActivo)
            <button wire:click="activar({{ $dia->id }})" type="button"
                    style="background:#16a34a;color:white;border:none;padding:8px 20px;border-radius:8px;font-size:0.82rem;font-weight:700;cursor:pointer;white-space:nowrap;"
                    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                📺 Mostrar na TV
            </button>
            @else
            <div style="color:#6ee7b7;font-size:0.82rem;font-weight:700;">
                ✅ Ativo
            </div>
            @endif
        </div>
        @empty
        <div style="color:rgba(255,255,255,0.35);font-size:0.9rem;padding:24px;text-align:center;background:rgba(255,255,255,0.04);border:1px dashed rgba(255,255,255,0.1);border-radius:12px;">
            Ainda não há dias publicados. Ative o toggle "Pronto para TV" no Dashboard para publicar um dia.
        </div>
        @endforelse
    </div>

</div>
