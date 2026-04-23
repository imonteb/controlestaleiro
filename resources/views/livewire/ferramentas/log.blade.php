<div x-data="{ drawerOpen: false }" @keydown.escape.window="drawerOpen = false" @open-drawer.window="drawerOpen = true">

    {{-- Tool Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-(--cme-blue)">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" /></svg>
            </div>
            <div>
                <div class="text-[10px] font-bold text-(--cme-blue) uppercase tracking-widest">{{ $ferramenta->familia }}</div>
                <h1 class="text-xl font-bold text-gray-900">{{ $ferramenta->designacao }}</h1>
                <div class="text-sm text-gray-500">Ref: {{ $ferramenta->referencia }} | SN: {{ $ferramenta->num_serie }}</div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $preventivo = $ferramenta->status_preventivo;
                $preventivoColor = str_contains($preventivo, 'ATRASADO') ? 'bg-red-100 text-red-700' :
                                  (str_contains($preventivo, 'DIAS') ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400');
                $operacional = $ferramenta->estado_operacional;
                $operacionalColor = match($operacional) {
                    'Apto' => 'bg-green-600 text-white',
                    'Não Apto' => 'bg-red-600 text-white',
                    'Condicionado' => 'bg-orange-500 text-white',
                    'Abate' => 'bg-black text-white',
                    default => 'bg-gray-200 text-gray-700'
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $preventivoColor }}">{{ $preventivo }}</span>
            <span class="px-4 py-1.5 rounded-lg text-xs font-black uppercase shadow-sm {{ $operacionalColor }}">{{ $operacional }}</span>
            <button @click="drawerOpen = true; $nextTick(() => initPad())"
                class="inline-flex items-center gap-2 bg-(--cme-blue) text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-800 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                Novo Registo
            </button>
        </div>
    </div>

    {{-- Log History (full width) --}}
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <h2 class="text-sm font-bold text-white/70 uppercase tracking-wider italic">Livro de Registos Cronológico (Folhas A4)</h2>
            <div class="flex gap-2">
                <a href="{{ route('ferramentas.imprimir-folha', 'all') }}" target="_blank"
                    class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase shadow-lg hover:bg-blue-500 transition-all flex items-center gap-2">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    Imprimir Todo o Livro
                </a>
                @php $lastFolha = $logs->first()->folha_id ?? 1; @endphp
                <a href="{{ route('ferramentas.imprimir-folha', $lastFolha) }}" target="_blank"
                    class="bg-yellow-500 text-blue-900 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase shadow-lg hover:bg-yellow-400 transition-all flex items-center gap-2">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Imprimir Folha Atual ({{ $lastFolha }})
                </a>
                <a href="{{ route('ferramentas.index') }}" wire:navigate
                    class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-gray-200 transition-all flex items-center gap-2">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Voltar
                </a>
            </div>
        </div>

        @if($logs->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
                <svg class="h-10 w-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <p class="text-sm font-bold uppercase tracking-wider">Sem registos de verificação</p>
                <p class="text-xs mt-1">Clique em "Novo Registo" para adicionar o primeiro.</p>
            </div>
        @else
            @php $groupedLogs = $logs->groupBy('folha_id'); @endphp
            @foreach($groupedLogs as $folhaId => $folhaLogs)
                @php $folhaRegisto = $folhaLogs->first()->num_registo_verificacao ?? "Folha #{$folhaId}"; @endphp
                <div class="space-y-3">
                    <div class="flex items-center gap-4 px-4 py-2 bg-blue-900/30 rounded-xl border border-blue-500/20">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-yellow-500 uppercase tracking-widest">{{ $folhaRegisto }}</span>
                            @if($folhaId)
                                <a href="{{ route('ferramentas.imprimir-folha', $folhaId) }}" target="_blank"
                                   class="p-1 hover:bg-white/10 rounded transition-colors group" title="Imprimir esta Folha">
                                    <svg class="h-3 w-3 text-yellow-500/50 group-hover:text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        <div class="h-px flex-1 bg-blue-500/10"></div>
                        <span class="text-[9px] font-bold text-blue-300 uppercase italic">{{ $folhaLogs->count() }} Registos</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($folhaLogs as $log)
                            <div @class([
                                'bg-white rounded-xl shadow-sm border p-4 hover:border-blue-200 transition-all relative overflow-hidden',
                                'border-yellow-400 ring-1 ring-yellow-300' => $log->id === $lastLogId,
                                'border-gray-100' => $log->id !== $lastLogId,
                            ])>
                                @if(!$log->apto)
                                    <div class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 text-[8px] font-black uppercase rotate-45 translate-x-3 translate-y-1 shadow-sm">Não Apto</div>
                                @endif
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center gap-3">
                                        <div @class([
                                            'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-inner',
                                            'bg-blue-50 text-blue-600' => $log->tipo_movimento === 'verificacao',
                                            'bg-green-50 text-green-600' => $log->tipo_movimento === 'entrega',
                                            'bg-orange-50 text-orange-600' => $log->tipo_movimento === 'devolucao',
                                        ])>
                                            <span class="text-[10px] font-black">Pos. {{ (($log->num_registo_seq - 1) % 9) + 1 }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-sm text-gray-900 capitalize">{{ $log->tipo_movimento }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $log->data_verificacao->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="text-[10px] text-gray-500 font-medium">Assentado por: {{ $log->verificado_por }}</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] font-black text-gray-300 uppercase mb-1">{{ $log->ferramenta->referencia }}</span>
                                        <div class="flex gap-1">
                                            @if($log->assinatura_path)
                                                <div class="bg-gray-50 rounded px-2 py-0.5 text-[8px] font-black text-blue-900 border border-blue-100">SELADO</div>
                                            @endif
                                            <div @class([
                                                'px-2 py-0.5 rounded text-[8px] font-black uppercase',
                                                'bg-green-100 text-green-700' => $log->apto,
                                                'bg-red-100 text-red-700' => !$log->apto,
                                            ])>{{ $log->apto ? 'Apto' : 'N/Apto' }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if($log->id === $lastLogId)
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2">
                                        <button wire:click="editLog({{ $log->id }})"
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-[9px] font-black uppercase hover:bg-blue-100 transition-all">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Editar
                                        </button>
                                        <button wire:click="deleteLastLog"
                                            wire:confirm="Tem a certeza que quer eliminar este registo? Esta acção não pode ser desfeita."
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 rounded-lg bg-red-50 text-red-700 text-[9px] font-black uppercase hover:bg-red-100 transition-all">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Eliminar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Drawer Backdrop --}}
    <div x-show="drawerOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="drawerOpen = false"
         class="fixed inset-0 bg-black/50 z-40"
         x-cloak></div>

    {{-- Drawer Panel --}}
    <div x-show="drawerOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl flex flex-col overflow-hidden"
         x-cloak>

        {{-- Drawer Header --}}
        <div class="flex items-center justify-between px-6 py-4 bg-(--cme-blue) text-white shrink-0">
            <div>
                <h3 class="text-sm font-black uppercase tracking-widest">{{ $editingLogId ? 'Editar Registo' : 'Novo Registo' }}</h3>
                <p class="text-[10px] text-blue-200 mt-0.5">{{ $ferramenta->designacao }}</p>
            </div>
            <button @click="drawerOpen = false" class="p-2 rounded-xl hover:bg-white/10 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Drawer Body --}}
        <form @submit.prevent class="flex-1 overflow-y-auto p-6 space-y-5">

            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tipo de Movimento</label>
                <select wire:model.live="tipo_movimento" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-sm font-bold text-gray-900">
                    <option value="verificacao">Verificação Técnica</option>
                    <option value="entrega">Entrega a Colaborador/Viatura</option>
                    <option value="devolucao">Devolução ao Estaleiro</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Data Registo</label>
                    <div class="relative">
                        <input type="date" wire:model.live="data_verificacao"
                            class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-sm text-gray-900 pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Próxima Verif.</label>
                    <div class="relative">
                        <input type="date" wire:model="proxima_verificacao"
                            class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-sm text-gray-900 pl-10 @if($data_verificacao && $proxima_verificacao && $proxima_verificacao < $data_verificacao) border-red-500 @endif">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nº Registo / Folha</label>
                    <input type="text" wire:model="num_registo_verificacao" placeholder="Ex: RV-001/2026" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-sm font-bold text-gray-900">
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Unidade / Área</label>
                    <input type="text" wire:model="unidade" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-sm font-bold text-gray-900">
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Checklist Oficial (IT.014.I.01)</label>
                    <button type="button" wire:click="$set('checklist', {{ json_encode(array_fill_keys(self::CHECKLIST_ITEMS, 'ok')) }})" class="text-[9px] font-bold text-blue-500 uppercase hover:underline">Tudo OK</button>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 space-y-2 border border-gray-100">
                    @foreach(self::CHECKLIST_ITEMS as $item)
                        <div class="flex items-center justify-between gap-4 p-2 rounded-lg bg-white border border-gray-100 shadow-sm">
                            <span class="text-[11px] font-medium text-gray-700 leading-tight">{{ $item }}</span>
                            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg shrink-0">
                                <button type="button"
                                    wire:click="$set('checklist.{{ $item }}', 'ok')"
                                    @class([
                                        'px-2 py-1 rounded-md text-[9px] font-bold uppercase transition-all',
                                        'bg-green-600 text-white shadow-sm' => ($checklist[$item] ?? '') === 'ok',
                                        'text-gray-400 hover:text-gray-600' => ($checklist[$item] ?? '') !== 'ok'
                                    ])>OK</button>
                                @if($item === 'Riscos Contacto Elétrico, Explosão, Incêndio')
                                    <button type="button"
                                        wire:click="$set('checklist.{{ $item }}', 'na')"
                                        @class([
                                            'px-2 py-1 rounded-md text-[9px] font-bold uppercase transition-all',
                                            'bg-gray-400 text-white shadow-sm' => ($checklist[$item] ?? '') === 'na',
                                            'text-gray-400 hover:text-gray-600' => ($checklist[$item] ?? '') !== 'na'
                                        ])>NA</button>
                                @endif
                                <button type="button"
                                    wire:click="$set('checklist.{{ $item }}', 'not_ok')"
                                    @class([
                                        'px-2 py-1 rounded-md text-[9px] font-bold uppercase transition-all',
                                        'bg-red-600 text-white shadow-sm' => ($checklist[$item] ?? '') === 'not_ok',
                                        'text-gray-400 hover:text-gray-600' => ($checklist[$item] ?? '') !== 'not_ok'
                                    ])>NOK</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Manutenção</label>
                    <select wire:model="manutencao_tipo" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-xs text-gray-900">
                        <option value="Cada Utiliz./Anual">Cada Utiliz./Anual</option>
                        <option value="Semanal">Semanal</option>
                        <option value="Mensal">Mensal</option>
                        <option value="Ocasional">Ocasional</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Estado Final</label>
                    <select wire:model="apto" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-xs font-bold @if($apto) text-green-600 @else text-red-600 @endif">
                        <option value="1">APTO</option>
                        <option value="0">NÃO APTO</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Observações</label>
                <textarea wire:model="observacoes" placeholder="Notas adicionais sobre a verificação..." rows="2" class="w-full rounded-xl border-gray-100 focus:ring-(--cme-blue) text-xs text-gray-900"></textarea>
            </div>

            {{-- Assinatura do utilizador --}}
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Assinatura</label>
                @php $userSignature = auth()->user()?->signature; @endphp
                @if($userSignature)
                    <div class="border border-gray-100 rounded-xl bg-gray-50 p-3 flex items-center gap-3">
                        <img src="{{ $userSignature }}" class="h-10 max-w-30 object-contain">
                        <span class="text-[10px] text-green-600 font-bold uppercase">{{ auth()->user()->name }}</span>
                    </div>
                @else
                    <div class="border border-amber-200 rounded-xl bg-amber-50 p-3">
                        <p class="text-xs font-bold text-amber-800">Sem assinatura registada.</p>
                        <a href="{{ route('utilizadores.index') }}" wire:navigate class="text-[10px] text-amber-700 underline font-bold">Ir a Gerir Utilizadores para adicionar →</a>
                    </div>
                @endif
            </div>

            @if($editingLogId)
                <div class="flex gap-3">
                    <button type="button" wire:click="$set('editingLogId', null)" @click="drawerOpen = false"
                        class="flex-1 py-4 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-all uppercase text-xs tracking-widest">
                        Cancelar Edição
                    </button>
                    <button type="button" @click="$wire.save()"
                        class="flex-1 py-4 rounded-xl bg-(--cme-blue) text-white font-bold hover:bg-blue-800 transition-all shadow-lg active:scale-95 uppercase text-xs tracking-widest">
                        Guardar Alterações
                    </button>
                </div>
            @else
                <button type="button" @click="$wire.save()"
                    class="w-full py-4 rounded-xl bg-(--cme-blue) text-white font-bold hover:bg-blue-800 transition-all shadow-lg active:scale-95 uppercase text-xs tracking-widest relative">
                    Gravar Registo no Livro
                    @if($assinatura)
                        <div class="absolute -top-2 right-2 bg-green-500 text-white text-[8px] px-2 py-0.5 rounded-full border border-white shadow-sm">Móvel ✓</div>
                    @endif
                </button>
            @endif
        </form>
    </div>

</div>
