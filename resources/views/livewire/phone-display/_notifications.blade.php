    {{-- ── AVISOS/NOTIFICAÇÕES GLOBAIS ─────────────────────────── --}}
    @if(isset($notificacoesGlobais) && $notificacoesGlobais->count() > 0)
        <div class="px-3.5 pt-4 flex flex-col gap-2.5">
            @foreach($notificacoesGlobais as $notif)
                @php
                    $tipo = strtolower($notif->tipo);
                    $notifClasses = match($tipo) {
                        'clima', 'info' => ['card' => 'bg-sky-400/15 border-sky-400/40', 'title' => 'text-sky-300'],
                        'seguranca', 'assinatura' => ['card' => 'bg-red-500/15 border-red-500/40', 'title' => 'text-red-400'],
                        'rrhh' => ['card' => 'bg-violet-400/15 border-violet-400/40', 'title' => 'text-violet-300'],
                        default => ['card' => 'bg-white/8 border-white/20', 'title' => 'text-white'],
                    };
                    $icons = ['clima'=>'⛈️','seguranca'=>'🛡️','rrhh'=>'👥','info'=>'ℹ️','assinatura'=>'✍️','geral'=>'ℹ️'];
                    $icon = $icons[$tipo] ?? $icons['geral'];
                @endphp

                <div wire:click="handleNotificationClick({{ $notif->id }})"
                     class="border rounded-xl p-3.5 flex gap-3 items-start relative cursor-pointer transition-opacity hover:opacity-85 {{ $notifClasses['card'] }}">
                    <div class="text-2xl leading-none mt-0.5">{{ $icon }}</div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="text-[0.85rem] font-extrabold m-0 mb-1 pr-5 {{ $notifClasses['title'] }}">{{ $notif->titulo }}</h4>
                            <button wire:click.stop="dismissNotification({{ $notif->id }})" class="bg-transparent border-none text-white/40 text-lg cursor-pointer leading-none absolute top-2.5 right-2.5" title="Descartar">✕</button>
                        </div>
                        <p class="text-white text-[0.75rem] m-0 leading-snug">{!! nl2br(e($notif->mensagem)) !!}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── MENSAGENS DE FEEDBACK ───────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-emerald-400/15 border border-emerald-400/30 text-emerald-300 px-3.5 py-2.5 mx-3.5 mt-2.5 rounded-lg text-[0.8rem] font-semibold">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-400/15 border border-red-400/30 text-red-300 px-3.5 py-2.5 mx-3.5 mt-2.5 rounded-lg text-[0.8rem] font-semibold">
            ❌ {{ session('error') }}
        </div>
    @endif
