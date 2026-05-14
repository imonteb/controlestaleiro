<div class="p-6 space-y-6"
     x-data="{ confirmOpen: false, confirmMessage: '', confirmAction: null }">

    {{-- Header CME con tabs --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                <span class="text-white font-medium text-sm">Gestão de Segurança & Apoio</span>
            </div>
            <div>
                @if($activeTab === 'contactos')
                    <button wire:click="openModal"
                        style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:6px 16px; border-radius:8px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Novo Contacto
                    </button>
                @elseif($activeTab === 'procedimentos')
                    <button wire:click="openModal"
                        style="background:#FFD300; color:#09143B; font-weight:700; font-size:12px; padding:6px 16px; border-radius:8px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Novo Protocolo
                    </button>
                @endif
            </div>
        </div>
        {{-- Tabs --}}
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-2">
            <button wire:click="$set('activeTab', 'contactos')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $activeTab === 'contactos' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/40 border-transparent hover:text-white/70' }}">
                Contactos Emergência
            </button>
            <button wire:click="$set('activeTab', 'procedimentos')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $activeTab === 'procedimentos' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/40 border-transparent hover:text-white/70' }}">
                Procedimentos
            </button>
            <button wire:click="$set('activeTab', 'incidentes')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $activeTab === 'incidentes' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/40 border-transparent hover:text-white/70' }}">
                Relatórios
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="badge-ok px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- TAB: CONTACTOS --}}
    @if($activeTab === 'contactos')
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ordem</th>
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Entidade / Contacto</th>
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Notas</th>
                        <th class="px-5 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($contactos as $c)
                    <tr class="transition-colors hover:bg-[#E4E2DF]" style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-5 py-4 text-sm font-bold italic" style="color:#7A7775;">{{ sprintf('%02d', $c->ordem) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($c->logo)
                                    <div style="width:42px; height:42px; border-radius:10px; background:white; border:1px solid rgba(9,20,59,0.10); overflow:hidden; display:flex; align-items:center; justify-content:center; padding:4px; flex-shrink:0;">
                                        <img src="{{ filter_var($c->logo, FILTER_VALIDATE_URL) ? $c->logo : Storage::url($c->logo) }}" alt="{{ $c->nome }}" class="w-full h-full object-contain">
                                    </div>
                                @else
                                    <div style="width:42px; height:42px; border-radius:10px; background:#E4E2DF; border:1px solid rgba(9,20,59,0.10); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg class="w-5 h-5" style="color:#7A7775;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                @endif
                                <div>
                                    <div style="color:#1A1A1A; font-weight:600; font-size:13px;">{{ $c->nome }}</div>
                                    <div style="color:#7A7775; font-size:13px; font-family:monospace;" class="mt-0.5">{{ $c->telefone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm italic" style="color:#4A4845;">{{ $c->descricao ?: '—' }}</td>
                        <td class="px-5 py-4 text-right space-x-2">
                            <button wire:click="editarContato({{ $c->id }})" class="btn-cme-secondary text-xs px-3 py-1.5 rounded-lg">Editar</button>
                            <button @click="confirmMessage = 'Apagar este contacto?'; confirmAction = () => $wire.apagarContato({{ $c->id }}); confirmOpen = true"
                                    style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); font-size:11px; font-weight:600; padding:6px 12px; border-radius:6px; cursor:pointer;">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-16 text-center cme-muted italic text-sm">
                            Sem contactos registados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- TAB: PROCEDIMENTOS --}}
    @if($activeTab === 'procedimentos')
    <div class="space-y-4">
        @forelse($procedimentos as $p)
        <div wire:key="proc-item-{{ $p->id }}" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-5">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="space-y-3 flex-1">
                    <div class="flex items-center gap-3">
                        @php
                            $tipoStyle = match($p->tipo) {
                                'acidente' => 'background:#fde8e8; color:#A32D2D;',
                                'transito' => 'background:#fdf0c2; color:#854F0B;',
                                'seguro'   => 'background:#d4ede4; color:#0F6E56;',
                                default    => 'background:#dbeafe; color:#1e40af;',
                            };
                        @endphp
                        <span style="{{ $tipoStyle }} font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; padding:3px 8px; border-radius:6px;">
                            {{ $p->tipo }}
                        </span>
                        <div>
                            <div style="color:#7A7775; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em;">{{ $p->ordem }}. {{ $p->titulo }}</div>
                            @if($p->subtitulo)
                                <div style="color:#1A1A1A; font-weight:700; font-size:15px;" class="mt-0.5">{{ $p->subtitulo }}</div>
                            @endif
                        </div>
                    </div>
                    <div style="color:#4A4845; background:white; border:1px solid rgba(9,20,59,0.08);" class="rounded-xl p-4 text-sm leading-relaxed whitespace-pre-line italic">
                        {{ $p->conteudo }}
                    </div>
                </div>
                <div class="flex md:flex-col gap-2">
                    <button wire:click="editarProcedimento({{ $p->id }})" class="btn-cme-secondary p-2 rounded-lg" title="Editar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </button>
                    <button @click="confirmMessage = 'Eliminar este protocolo?'; confirmAction = () => $wire.apagarProcedimento({{ $p->id }}); confirmOpen = true"
                            style="background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20); padding:8px; border-radius:8px; cursor:pointer;"
                            title="Eliminar">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl px-6 py-16 text-center cme-muted italic text-sm">
            Nenhum procedimento operacional publicado.
        </div>
        @endforelse
    </div>
    @endif

    {{-- TAB: INCIDENTES --}}
    @if($activeTab === 'incidentes')
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.14)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr style="background:#E4E2DF; border-bottom:1px solid rgba(9,20,59,0.10);">
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Cronologia</th>
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Colaborador / Risco</th>
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Localização & Factos</th>
                        <th class="px-5 py-3" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Evidência</th>
                        <th class="px-5 py-3 text-right" style="color:#4A4845; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em;">Estado / Gestão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgba(9,20,59,0.06)]">
                    @forelse($incidentes as $inc)
                    <tr class="transition-colors hover:bg-[#E4E2DF]" style="background:{{ $loop->index % 2 === 0 ? '#FFFFFF' : '#F0EEEB' }} !important;">
                        <td class="px-5 py-4">
                            <div style="color:#1A1A1A; font-weight:600; font-size:13px;">{{ $inc->data_hora->format('d/m/Y') }}</div>
                            <div class="cme-muted text-xs mt-0.5">{{ $inc->data_hora->format('H:i') }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div style="color:#1A1A1A; font-weight:600; font-size:13px;">
                                {{ $inc->colaborador->nome }} {{ $inc->colaborador->apellido }}
                            </div>
                            @php
                                $incStyle = match($inc->tipo) {
                                    'acidente_trabalho' => 'background:#fde8e8; color:#A32D2D;',
                                    'acidente_viacao'   => 'background:#fdf0c2; color:#854F0B;',
                                    'quase_acidente'    => 'background:#ffedd5; color:#9a3412;',
                                    default             => 'background:#dbeafe; color:#1e40af;',
                                };
                            @endphp
                            <span style="{{ $incStyle }} font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; padding:2px 6px; border-radius:4px; display:inline-block; margin-top:4px;">
                                {{ str_replace('_', ' ', $inc->tipo) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5 text-xs font-semibold mb-1" style="color:#09143B;">
                                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $inc->localizacao }}
                            </div>
                            <p class="text-sm leading-relaxed line-clamp-2 max-w-sm" style="color:#4A4845;" title="{{ $inc->descricao }}">{{ $inc->descricao }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($inc->photo_path)
                                <a href="{{ Storage::url($inc->photo_path) }}" target="_blank"
                                   style="background:#E4E2DF; border:1px solid rgba(9,20,59,0.12); color:#09143B; border-radius:10px; width:40px; height:40px; display:flex; align-items:center; justify-content:center;"
                                   class="hover:opacity-75 transition-opacity">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </a>
                            @else
                                <span class="cme-muted text-xs italic">N/D</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right space-y-2">
                            @php
                                $estadoStyle = match($inc->estado) {
                                    'novo'       => 'background:#fde8e8; color:#A32D2D; border:1px solid rgba(163,45,45,0.20);',
                                    'em_analise' => 'background:#fdf0c2; color:#854F0B; border:1px solid rgba(133,79,11,0.20);',
                                    default      => 'background:#d4ede4; color:#0F6E56; border:1px solid rgba(15,110,86,0.20);',
                                };
                            @endphp
                            <span style="{{ $estadoStyle }} font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; padding:3px 8px; border-radius:20px; display:inline-block;">
                                {{ str_replace('_', ' ', $inc->estado) }}
                            </span>
                            @if($inc->estado != 'resolvido')
                                <select wire:change="alterarEstadoIncidente({{ $inc->id }}, $event.target.value)"
                                        class="cme-input block ml-auto text-xs" style="max-width:160px;">
                                    <option value="" disabled selected>Gerir estado</option>
                                    @if($inc->estado == 'novo')
                                        <option value="em_analise">Marcar em análise</option>
                                    @endif
                                    <option value="resolvido">Marcar resolvido</option>
                                </select>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center cme-muted italic text-sm">
                            Nenhum reporte via App.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incidentes->hasPages())
        <div class="px-5 py-4" style="border-top:1px solid rgba(9,20,59,0.08);">
            {{ $incidentes->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Modal de Confirmação --}}
    <div x-show="confirmOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
         @keydown.escape.window="confirmOpen = false">
        <div class="w-full max-w-sm mx-4 rounded-2xl overflow-hidden shadow-2xl border border-[rgba(163,45,45,0.30)]">
            <div style="background:#09143B;" class="px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" style="color:#FFD300;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span class="text-white font-medium text-sm">Confirmar eliminação</span>
            </div>
            <div style="background:white;" class="px-6 py-5">
                <p class="text-sm mb-5" style="color:#4A4845;" x-text="confirmMessage"></p>
                <div class="flex justify-end gap-3">
                    <button @click="confirmOpen = false" class="btn-cme-secondary">Cancelar</button>
                    <button @click="confirmAction(); confirmOpen = false"
                        style="background:#A32D2D; color:white; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer;">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl border border-[rgba(9,20,59,0.16)]">
            {{-- Header --}}
            <div class="bg-[#09143B] px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="shield-check" class="text-[#FFD300] w-4 h-4" />
                    <span class="text-white font-medium text-sm">
                        @if($activeTab === 'contactos')
                            {{ $contato_id ? 'Editar Contacto' : 'Novo Contacto' }}
                        @else
                            {{ $procedimento_id ? 'Editar Protocolo' : 'Novo Protocolo' }}
                        @endif
                    </span>
                </div>
                <button wire:click="closeModal" style="background:none; border:none; color:rgba(255,255,255,0.6); cursor:pointer; font-size:1.25rem; line-height:1;" class="hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            {{-- Body --}}
            <div style="background:white;" class="p-6 max-h-[70vh] overflow-y-auto">

                @if($activeTab === 'contactos')
                    <form wire:submit.prevent="salvarContato" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="cme-label">Nome / Entidade</label>
                                <input type="text" wire:model="nome" class="cme-input mt-1">
                                @error('nome') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="cme-label">Telefone / Contacto</label>
                                <input type="text" wire:model="telefone" class="cme-input mt-1">
                                @error('telefone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="cme-label">Descrição curta</label>
                            <input type="text" wire:model="descricao" class="cme-input mt-1">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="cme-label">Ordem Visual</label>
                                <input type="number" wire:model="ordem" class="cme-input mt-1">
                            </div>
                            <div>
                                <label class="cme-label">Link do Logótipo (URL)</label>
                                <input type="text" wire:model="logo" class="cme-input mt-1">
                            </div>
                        </div>
                        <div class="flex gap-3 pt-4" style="border-top:1px solid rgba(9,20,59,0.08);">
                            <button type="button" wire:click="closeModal" class="btn-cme-secondary">Cancelar</button>
                            <button type="submit"
                                style="flex:1; background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; text-transform:uppercase; letter-spacing:0.04em;">
                                {{ $contato_id ? 'Confirmar Alterações' : 'Guardar Contacto' }}
                            </button>
                        </div>
                    </form>

                @else
                    <form wire:submit.prevent="salvarProcedimento" class="space-y-4">
                        <div>
                            <label class="cme-label">Título do Procedimento</label>
                            <input type="text" wire:model="titulo" class="cme-input mt-1">
                            @error('titulo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="cme-label">Subtítulo / Contexto</label>
                            <input type="text" wire:model="subtitulo" class="cme-input mt-1">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="cme-label">Categoria de Risco</label>
                                <select wire:model="tipo_proc" class="cme-input mt-1">
                                    <option value="geral">Instruções Gerais</option>
                                    <option value="acidente">Acidente de Trabalho</option>
                                    <option value="transito">Acidente de Viação</option>
                                    <option value="seguro">Seguros e Apólices</option>
                                </select>
                            </div>
                            <div>
                                <label class="cme-label">Ordem Visual</label>
                                <input type="number" wire:model="ordem_proc" class="cme-input mt-1">
                            </div>
                        </div>
                        <div>
                            <label class="cme-label">Corpo das Instruções</label>
                            <textarea wire:model="conteudo" rows="10" class="cme-input mt-1" style="resize:vertical;"></textarea>
                            @error('conteudo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex gap-3 pt-4" style="border-top:1px solid rgba(9,20,59,0.08);">
                            <button type="button" wire:click="closeModal" class="btn-cme-secondary">Cancelar</button>
                            <button type="submit"
                                style="flex:1; background:#FFD300; color:#09143B; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; text-transform:uppercase; letter-spacing:0.04em;">
                                {{ $procedimento_id ? 'Atualizar Protocolo' : 'Publicar Protocolo' }}
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>
