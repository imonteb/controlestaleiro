<div class="w-full max-w-5xl mx-auto px-4 py-6">

    {{-- Header CME --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-4">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="map-pin" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Locais Frequentes</span>
                <span class="text-[10px] bg-[rgba(255,211,0,0.15)] text-[#FFD300] px-2 py-1 rounded font-medium tracking-wide">Endereços para guias de transporte</span>
            </div>
            <a href="{{ route('locais-frequentes.crear') }}" wire:navigate
               style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:6px 14px; border-radius:6px; white-space:nowrap; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none;">
                + Novo Local
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="cme-card rounded-xl border border-[rgba(9,20,59,0.14)] p-3 flex flex-wrap gap-3 mb-4">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Pesquisar por nome, localidade, morada..."
               class="cme-input flex-1" style="min-width:220px;">

        <select wire:model.live="filtroTipo" class="cme-input" style="min-width:160px;">
            <option value="">Todos os tipos</option>
            <option value="portugal">Portugal</option>
            <option value="internacional">Internacional</option>
        </select>

        <select wire:model.live="filtroActivo" class="cme-input" style="min-width:140px;">
            <option value="activos">Activos</option>
            <option value="inactivos">Inactivos</option>
            <option value="">Todos</option>
        </select>
    </div>

    {{-- Tabela --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#E4E2DF !important; border-bottom:1px solid rgba(9,20,59,0.10);">
                    <th class="text-left px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Nome</th>
                    <th class="text-left px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Endereço</th>
                    <th class="text-left px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">CP</th>
                    <th class="text-left px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">País</th>
                    <th class="text-center px-4 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($locais as $local)
                @php $zebraBase = $loop->index % 2 === 0 ? '#ffffff' : '#F0EEEB'; @endphp
                <tr class="hover:bg-[#EEF2FF] transition-colors" style="background:{{ $zebraBase }}; border-bottom:1px solid rgba(9,20,59,0.06);">
                    <td class="px-4 py-3">
                        <div style="color:#1A1A1A; font-weight:600; font-size:13px;">{{ $local->nome }}</div>
                        <div class="mt-0.5">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase
                                {{ $local->tipo === 'portugal' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $local->tipo }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span style="color:#4A4845;">{{ $local->morada }}</span>
                        @if($local->localidade)
                            <div style="color:#7A7775; font-size:11px;">{{ $local->localidade }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-xs" style="color:#4A4845;">
                        @if($local->cp4 && $local->cp3)
                            {{ $local->cp4 }}-{{ $local->cp3 }}
                            @if($local->cpalf)
                                <div style="color:#7A7775;">{{ $local->cpalf }}</div>
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs" style="color:#7A7775;">{{ $local->pais }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($local->activo)
                            <span class="badge-ok">Activo</span>
                        @else
                            <span class="badge-neutral">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('locais-frequentes.editar', $local) }}" wire:navigate
                               style="color:#09143B; font-size:12px; font-weight:600;" class="hover:underline">Editar</a>
                            <button wire:click="pedirEliminar({{ $local->id }})"
                                    style="color:#A32D2D; font-size:12px; font-weight:600;" class="hover:underline">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center cme-muted text-sm">Sem locais registados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($locais->hasPages())
    <div style="border-top:1px solid rgba(9,20,59,0.08); padding:12px 0; margin-top:0;">
        {{ $locais->links() }}
    </div>
    @endif

    {{-- Modal confirmar eliminar --}}
    @if($confirmandoEliminar)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-bold mb-2" style="color:#1A1A1A;">Eliminar local?</h3>
            <p class="text-sm mb-5" style="color:#7A7775;">Esta acção não pode ser revertida.</p>
            <div class="flex gap-3 justify-end">
                <button wire:click="cancelarEliminar" class="btn-cme-secondary">Cancelar</button>
                <button wire:click="eliminar" style="background:#A32D2D; color:white; font-weight:700; font-size:12px; padding:6px 16px; border-radius:6px; border:none; cursor:pointer;">Eliminar</button>
            </div>
        </div>
    </div>
    @endif

</div>
