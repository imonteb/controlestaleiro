<div class="p-6 flex flex-col gap-4">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)]">
        <div class="bg-[#09143B] px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Gestão de Activos</span>
                <span style="color:rgba(255,255,255,0.5); font-size:11px;">— Extintores e Kits de Saúde</span>
            </div>
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="type" class="w-40">
                    <flux:select.option value="all">Todos os tipos</flux:select.option>
                    <flux:select.option value="extintor">Extintores</flux:select.option>
                    <flux:select.option value="kit">Kits de Saúde</flux:select.option>
                </flux:select>
                <flux:select wire:model.live="statusFilter" class="w-40">
                    <flux:select.option value="all">Todos os estados</flux:select.option>
                    <flux:select.option value="ok">Conforme</flux:select.option>
                    <flux:select.option value="warning">Próximo Vencimento</flux:select.option>
                    <flux:select.option value="expired">Expirado</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-0">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Pesquisar por S/N, matrícula, local..." />
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="badge-ok px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tabela --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <table class="w-full text-left text-sm">
            <thead>
                <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Asset</th>
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Localização / Veículo</th>
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Validade</th>
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">QR / Token</th>
                    <th class="px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Acções</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                @forelse($assets as $asset)
                @php $rowBg = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB'; @endphp
                <tr class="hover:bg-[#E4E2DF] transition-colors" style="background:{{ $rowBg }};">
                    <td class="px-4 py-4">
                        <div style="color:#1A1A1A; font-weight:700;">{{ $asset['name'] }}</div>
                        <div class="text-xs" style="color:#7A7775;">{{ $asset['description'] }}</div>
                    </td>
                    <td class="px-4 py-4" style="color:#4A4845;">{{ $asset['location'] }}</td>
                    <td class="px-4 py-4">
                        @if($asset['expiry'])
                            @if($asset['status'] === 'expired')
                                <span style="color:#A32D2D; font-weight:600;">{{ $asset['expiry']->format('d/m/Y') }}</span>
                            @elseif($asset['status'] === 'warning')
                                <span style="color:#854F0B; font-weight:600;">{{ $asset['expiry']->format('d/m/Y') }}</span>
                            @else
                                <span style="color:#1A1A1A;">{{ $asset['expiry']->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span style="color:#7A7775; font-style:italic;">Sem data</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-1">
                            @if($asset['needs_restock'])
                                <span class="badge-warn">Reposição</span>
                            @endif
                            @if($asset['status'] === 'expired')
                                <span class="badge-danger">Expirado</span>
                            @elseif($asset['status'] === 'warning')
                                <span class="badge-warn">Atenção</span>
                            @else
                                <span class="badge-ok">OK</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if($asset['token'])
                            <code style="background:rgba(9,20,59,0.06); border:1px solid rgba(9,20,59,0.14); color:#09143B; font-size:10px; padding:2px 6px; border-radius:4px;">{{ $asset['token'] }}</code>
                        @else
                            <button wire:click="generateToken({{ $asset['id'] }}, '{{ $asset['type'] }}')"
                                    style="color:#09143B; font-size:12px; font-weight:600;" class="hover:underline">Gerar Token</button>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <flux:button size="xs" variant="ghost" icon="pencil" title="Editar" />
                            <flux:button size="xs" variant="ghost"
                                :icon="$asset['needs_restock'] ? 'check-circle' : 'exclamation-circle'"
                                wire:click="toggleRestock({{ $asset['id'] }}, '{{ $asset['type'] }}')"
                                :title="$asset['needs_restock'] ? 'Marcar OK' : 'Marcar em Falta'" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center cme-muted italic">Nenhum activo encontrado com estes filtros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
