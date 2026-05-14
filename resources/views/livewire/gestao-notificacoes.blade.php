<div class="p-6 space-y-6">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center gap-2">
            <flux:icon name="bell" class="text-[#FFD300] w-4 h-4" />
            <span class="text-white font-medium text-sm">Gestão de Notificações PWA</span>
            <span style="color:rgba(255,255,255,0.4); font-size:11px;">— Avisos em tempo real</span>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="badge-ok px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna Esquerda: Formulário --}}
        <div class="lg:col-span-1">
            <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)] sticky top-24">
                {{-- Header strip --}}
                <div class="bg-[#09143B] px-5 py-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FFD300]"></span>
                    <span class="text-white font-medium text-sm">Nova Notificação</span>
                </div>
                {{-- Form body --}}
                <div style="background:#F0EEEB;" class="p-5">
                    <form wire:submit.prevent="criarNotificacao" class="space-y-4">

                        <div>
                            <label class="cme-label">Tipo de Aviso</label>
                            <select wire:model="tipo" class="cme-input mt-1">
                                <option value="geral">Geral / Informativo</option>
                                <option value="clima">Alerta Meteorológico (Clima)</option>
                                <option value="rrhh">Recursos Humanos (RH)</option>
                                <option value="seguranca">Higiene e Segurança (HSA)</option>
                            </select>
                            @error('tipo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="cme-label">Título</label>
                            <input type="text" wire:model="titulo" placeholder="Ex: Aviso de Chuva Forte" maxlength="100" class="cme-input mt-1">
                            @error('titulo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="cme-label">Mensagem</label>
                            <textarea wire:model="mensagem" rows="4" placeholder="Detalhes do aviso..." maxlength="1000"
                                      class="cme-input mt-1" style="resize:none;"></textarea>
                            @error('mensagem') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="cme-label">Válido até</label>
                            <input type="datetime-local" wire:model="data_expiracao" class="cme-input mt-1">
                            <p class="cme-muted text-xs mt-1 italic">Após esta data, o aviso expira automaticamente.</p>
                            @error('data_expiracao') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit"
                            style="background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; width:100%; text-transform:uppercase; letter-spacing:0.05em;">
                            Publicar Agora
                        </button>

                    </form>
                </div>
            </div>
        </div>

        {{-- Coluna Direita: Listas --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Ativas --}}
            <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
                <div class="bg-[#09143B] px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-white font-medium text-sm">Emissão Ativa na App</span>
                    </div>
                    <span class="badge-ok text-xs px-2 py-0.5">{{ $notificacoesActivas->count() }} ativos</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                                <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Tipo / Conteúdo</th>
                                <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Expiração</th>
                                <th class="px-5 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                            @forelse($notificacoesActivas as $notif)
                                <tr class="transition-colors hover:bg-[#E4E2DF]" style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div style="background:#E4E2DF; border:1px solid rgba(9,20,59,0.10); border-radius:10px; width:38px; height:38px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                                @if($notif->tipo === 'clima') ⛈️ @elseif($notif->tipo === 'seguranca') 🛡️ @elseif($notif->tipo === 'rrhh') 👥 @else ℹ️ @endif
                                            </div>
                                            <div>
                                                @if($notif->tipo === 'seguranca')
                                                    <a href="{{ route('seguranca.index', ['tab' => 'incidentes']) }}" wire:navigate
                                                       class="flex items-center gap-1 hover:underline"
                                                       style="color:#09143B; font-weight:600; font-size:13px;">
                                                        {{ $notif->titulo }}
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                                    </a>
                                                @else
                                                    <div style="color:#1A1A1A; font-weight:600; font-size:13px;">{{ $notif->titulo }}</div>
                                                @endif
                                                <div class="cme-muted text-xs mt-0.5 line-clamp-1 italic">{{ $notif->mensagem }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($notif->data_expiracao)
                                            <div class="text-sm font-semibold {{ $notif->data_expiracao < now()->addHours(12) ? 'text-orange-500' : '' }}"
                                                 style="{{ $notif->data_expiracao >= now()->addHours(12) ? 'color:#1A1A1A;' : '' }}">
                                                {{ $notif->data_expiracao->format('d/m/Y') }}
                                            </div>
                                            <div class="cme-muted text-xs">{{ $notif->data_expiracao->format('H:i') }}</div>
                                        @else
                                            <span class="badge-neutral text-xs px-2 py-0.5">Permanente</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button wire:click="desactivar({{ $notif->id }})"
                                                onclick="confirm('Desativar e remover esta notificação da App?') || event.stopImmediatePropagation()"
                                                style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer;">
                                            Desativar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-12 text-center cme-muted italic text-sm">
                                        Nenhum aviso em curso.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Histórico/Inativas --}}
            @if($notificacoesInactivas->count() > 0)
            <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.10)]">
                <div style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.08);" class="px-5 py-3">
                    <span style="color:#7A7775; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Arquivo Histórico</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                            @foreach($notificacoesInactivas as $notif)
                                <tr style="background:#F0EEEB;" class="transition-colors hover:bg-[#E4E2DF]">
                                    <td class="px-5 py-3">
                                        <div class="text-sm" style="color:#7A7775; text-decoration:line-through;">{{ $notif->titulo }}</div>
                                    </td>
                                    <td class="px-5 py-3 cme-muted text-xs">
                                        Publicado {{ $notif->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button wire:click="apagar({{ $notif->id }})"
                                                title="Eliminar Permanentemente"
                                                onclick="confirm('Eliminar permanentemente este registo?') || event.stopImmediatePropagation()"
                                                style="background:none; border:none; cursor:pointer; color:#A32D2D; padding:4px;">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
