    {{-- ── SEGURANÇA E APOIO MODAL ───────────────────────────────────── --}}
    <div class="flex-1 overflow-hidden flex flex-col bg-linear-[160deg] from-slate-900 to-[#162555]">

            {{-- HEADER --}}
            <div class="p-5 border-b border-white/8 shrink-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-yellow-300 text-xl font-black m-0 leading-none tracking-[0.01em]">Segurança & Apoio</h3>
                        <p class="text-white/90 text-xs mt-1 m-0">Prevenção e Gestão de Emergências</p>
                    </div>
                </div>

                <div class="border-t border-yellow-300/15 pt-4 mt-4 flex gap-2">
                    <button wire:click="$set('activeSegurancaTab', 'procedimentos')"
                        class="flex-1 py-2.5 px-1.5 rounded-xl border text-[0.7rem] font-black uppercase tracking-[0.05em] transition-all duration-200 {{ $activeSegurancaTab === 'procedimentos' ? 'bg-linear-to-br from-blue-900 to-blue-800 text-yellow-300 border-yellow-300/40 shadow-[0_4px_15px_rgba(30,58,138,0.4)]' : 'bg-white/5 text-white/40 border-white/5' }}">
                        Ajuda
                    </button>
                    <button wire:click="$set('activeSegurancaTab', 'contactos')"
                        class="flex-1 py-2.5 px-1.5 rounded-xl border text-[0.7rem] font-black uppercase tracking-[0.05em] transition-all duration-200 {{ $activeSegurancaTab === 'contactos' ? 'bg-linear-to-br from-blue-900 to-blue-800 text-yellow-300 border-yellow-300/40 shadow-[0_4px_15px_rgba(30,58,138,0.4)]' : 'bg-white/5 text-white/40 border-white/5' }}">
                        Telefones
                    </button>
                    <button wire:click="$set('activeSegurancaTab', 'reportar')"
                        class="flex-1 py-2.5 px-1.5 rounded-xl border text-[0.7rem] font-black uppercase tracking-[0.05em] transition-all duration-200 {{ $activeSegurancaTab === 'reportar' ? 'bg-linear-to-br from-red-950 to-red-900 text-red-300 border-red-500/40 shadow-[0_4px_15px_rgba(127,29,29,0.4)]' : 'bg-white/5 text-white/40 border-white/5' }}">
                        SOS
                    </button>
                </div>
            </div>

            {{-- CONTENT AREA --}}
            <div class="flex-1 overflow-y-auto p-5 pb-20">

                @if($activeSegurancaTab === 'procedimentos')
                    <div class="flex flex-col gap-4 pb-24">
                        @forelse($procedimentosEmergencia as $proc)
                            <div class="bg-linear-[145deg] from-blue-900 to-[#172554] border border-yellow-300/30 rounded-2xl p-5 shadow-[0_10px_25px_-5px_rgba(15,23,42,0.6)] relative overflow-hidden mb-4">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-[0.03] rounded-full translate-x-6 -translate-y-6"></div>

                                <h4 class="text-yellow-300 text-xs font-black uppercase tracking-widest mb-2 flex items-center gap-2.5">
                                    @php
                                        $icon = match($proc->id) {
                                            1 => '📋', 4 => '🚨', 2 => '🛡️', 3 => '📞', 5 => '📍', 6 => '🌍', default => '📄'
                                        };
                                    @endphp
                                    <span class="text-xl drop-shadow-sm">{{ $icon }}</span>
                                    {{ $proc->titulo }}
                                </h4>

                                @if($proc->subtitulo)
                                    <div class="bg-yellow-300/12 text-yellow-300 text-[0.6rem] uppercase font-black px-2.5 py-1 rounded-lg inline-block mb-4 tracking-[0.05em] border border-yellow-300/20">
                                        {{ $proc->subtitulo }}
                                    </div>
                                @endif

                                <div class="text-white/95 text-[0.85rem] leading-[1.7] font-medium tracking-[0.01em]">
                                    {!! nl2br(e($proc->conteudo)) !!}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 px-5 text-white/20 text-[0.7rem] uppercase tracking-widest">
                                Nenhum procedimento operacional configurado.
                            </div>
                        @endforelse
                    </div>
                @endif

                @if($activeSegurancaTab === 'contactos')
                    <div class="flex flex-col gap-3">
                        <a href="tel:112" class="bg-linear-to-br from-red-500 to-red-700 rounded-2xl p-4.5 no-underline flex items-center gap-4 shadow-[0_8px_25px_rgba(239,68,68,0.4)] border border-white/20 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-12 h-12 bg-white opacity-10 rounded-full translate-x-4 -translate-y-4"></div>
                            <div class="bg-white text-red-500 w-11 h-11 rounded-xl flex items-center justify-center text-2xl font-black shadow-[0_4px_10px_rgba(0,0,0,0.2)]">☎</div>
                            <div>
                                <h4 class="text-white text-[1.1rem] font-black m-0 tracking-widest">112</h4>
                                <p class="text-white/90 text-xs m-0 font-semibold uppercase tracking-[0.02em]">Emergência Nacional</p>
                            </div>
                        </a>

                        <div class="my-2.5 h-px bg-white/5"></div>

                        @forelse($contactosEmergencia as $contato)
                        <a href="tel:{{ str_replace(' ', '', $contato->telefone) }}" class="bg-white/8 border border-white/15 rounded-2xl px-4.5 py-3.5 no-underline flex items-center justify-between gap-3 shadow-[0_4px_15px_rgba(0,0,0,0.2)] mb-1">
                            <div class="flex items-center gap-3.5 flex-1 min-w-0">
                                @if($contato->logo)
                                <div class="w-11.5 h-11.5 bg-white rounded-xl p-1 shrink-0 flex items-center justify-content shadow-[0_6px_15px_rgba(0,0,0,0.3)]">
                                    <img src="{{ filter_var($contato->logo, FILTER_VALIDATE_URL) ? $contato->logo : Storage::url($contato->logo) }}"
                                         alt="{{ $contato->nome }}"
                                         class="max-w-full max-h-full object-contain">
                                </div>
                                @else
                                <div class="w-11.5 h-11.5 bg-white/8 border border-white/10 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="text-[1.3rem]">📞</span>
                                </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <h4 class="text-white text-[0.95rem] font-extrabold m-0 truncate tracking-tight">{{ $contato->nome }}</h4>
                                    @if($contato->descricao)
                                    <p class="text-white/50 text-[0.65rem] mt-0.5 mb-1 truncate font-medium">{{ $contato->descricao }}</p>
                                    @endif
                                    <div class="text-yellow-300 text-[0.9rem] font-black tracking-[0.02em]">{{ $contato->telefone }}</div>
                                </div>
                            </div>
                            <div class="bg-blue-500/15 text-blue-400 w-9.5 h-9.5 rounded-xl flex items-center justify-center shrink-0 border border-blue-500/30 shadow-[0_4px_10px_rgba(59,130,246,0.2)]">
                                <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/></svg>
                            </div>
                        </a>
                        @empty
                        <p class="text-white/40 text-[0.85rem] text-center">Não existem contactos adicionais.</p>
                        @endforelse
                    </div>
                @endif

                @if($activeSegurancaTab === 'reportar')
                    <div class="bg-white/3 backdrop-blur-sm rounded-2xl p-5 border border-white/8 shadow-[0_15px_35px_rgba(0,0,0,0.3)]">
                        <form wire:submit.prevent="enviarIncidente" class="flex flex-col gap-4.5">
                            <div>
                                <label class="text-yellow-300/90 text-[0.65rem] font-black uppercase tracking-widest mb-2 block">Tipo de Incidente</label>
                                <select wire:model="incidenteTipo" class="w-full bg-white/6 border border-white/15 rounded-xl px-3 py-3 text-white text-[0.9rem] outline-none transition-colors">
                                    <option value="" class="bg-slate-800">Selecione o tipo...</option>
                                    <option value="acidente_trabalho" class="bg-slate-800">Acidente de Trabalho (Pessoal)</option>
                                    <option value="acidente_viacao" class="bg-slate-800">Acidente de Viação / Sinistro Automóvel</option>
                                    <option value="quase_acidente" class="bg-slate-800">Quase Acidente (Near Miss)</option>
                                    <option value="condicao_insegura" class="bg-slate-800">Condição de Risco (Insegura)</option>
                                </select>
                                @error('incidenteTipo') <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-yellow-300/90 text-[0.65rem] font-black uppercase tracking-widest mb-2 block">Localização Exata</label>
                                <input wire:model="incidenteLocalizacao" type="text" placeholder="Rua, KM, ou referência..." class="w-full bg-white/6 border border-white/15 rounded-xl px-3 py-3 text-white text-[0.9rem] outline-none">
                                @error('incidenteLocalizacao') <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-yellow-300/90 text-[0.65rem] font-black uppercase tracking-widest mb-2 block">Descrição do Ocorrido</label>
                                <textarea wire:model="incidenteDescricao" placeholder="Como aconteceu? Houve ferimentos? Danos materiais?" rows="4" class="w-full bg-white/6 border border-white/15 rounded-xl px-3 py-3 text-white text-[0.9rem] outline-none resize-none"></textarea>
                                @error('incidenteDescricao') <span class="text-red-300 text-[0.7rem] mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-yellow-300/90 text-[0.65rem] font-black uppercase tracking-widest mb-2 block">Anexar Fotografia (Opcional)</label>
                                <div class="bg-white/4 border border-dashed border-white/20 rounded-xl p-4 text-center">
                                    <input wire:model="incidenteFoto" type="file" accept="image/*" capture="environment" class="w-full text-white/70 text-[0.8rem]">
                                </div>
                                @error('incidenteFoto') <span class="text-red-300 text-[0.7rem] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="bg-red-500/10 border-l-4 border-red-500 rounded-r px-3 py-3 my-1">
                                <p class="text-red-300 text-[0.7rem] leading-relaxed m-0 font-medium">⚠️ Em caso de feridos graves, chame o **112** imediatamente. Só depois preencha este relatório.</p>
                            </div>

                            <button type="submit" class="bg-linear-to-br from-red-500 to-red-600 text-white font-black py-4.5 rounded-[14px] mt-1 border border-white/20 text-[0.9rem] uppercase shadow-[0_8px_25px_rgba(239,68,68,0.4)] tracking-[0.08em] cursor-pointer">
                                ENVIAR RELATÓRIO SOS
                            </button>
                        </form>
                    </div>
                @endif
            </div>
    </div>

