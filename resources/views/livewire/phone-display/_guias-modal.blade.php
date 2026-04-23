<div class="flex-1 overflow-hidden flex flex-col bg-linear-[160deg] from-slate-900 to-blue-900">

    {{-- HEADER --}}
    <div class="p-5 border-b border-white/8 shrink-0">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-blue-400 text-xl font-black m-0 leading-none tracking-[0.01em]">Guias de Transporte</h3>
                <p class="text-white/60 text-xs mt-1 m-0">Solicitar e acompanhar pedidos</p>
            </div>
            @if($activeGuiaTab === 'solicitar')
            <button type="button" wire:click="limparFormulario"
                    class="bg-white/5 text-white/40 border border-white/10 px-2.5 py-1 rounded-lg text-[0.6rem] font-bold uppercase">
                Limpar
            </button>
            @endif
        </div>

        {{-- TABS --}}
        <div class="flex gap-1.5 bg-black/30 p-1 rounded-xl mt-4">
            <button wire:click="$set('activeGuiaTab', 'solicitar')"
                    class="flex-1 py-2.5 rounded-[10px] border-none text-[0.65rem] font-extrabold transition-colors
                           {{ $activeGuiaTab === 'solicitar' ? 'bg-blue-500 text-white' : 'bg-transparent text-white/50' }}">
                SOLICITAR
            </button>
            <button wire:click="$set('activeGuiaTab', 'historico')"
                    class="flex-1 py-2.5 rounded-[10px] border-none text-[0.65rem] font-extrabold transition-colors
                           {{ $activeGuiaTab === 'historico' ? 'bg-blue-500 text-white' : 'bg-transparent text-white/50' }}">
                HISTÓRICO
            </button>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="flex-1 overflow-y-auto p-5 pb-24">

        @if($sucesso)
        <div class="bg-emerald-500/15 border border-emerald-500/40 rounded-2xl p-4 mb-4 text-center">
            <div class="text-emerald-300 text-2xl mb-1">✓</div>
            <div class="text-emerald-300 font-bold text-sm">Pedido enviado!</div>
            <div class="text-white/60 text-xs mt-1">Aguarda aprovação do responsável.</div>
        </div>
        @endif

        {{-- ─── TAB: SOLICITAR ─────────────────────────── --}}
        @if($activeGuiaTab === 'solicitar')

        @php
            $inp  = 'w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2.5 text-white text-[0.82rem] placeholder-white/25 focus:outline-none focus:border-blue-500/60';
            $inpE = 'w-full bg-black/20 border border-red-500/60 rounded-xl px-3 py-2.5 text-white text-[0.82rem]';
            $lbl  = 'text-white/40 text-[0.6rem] font-bold uppercase ml-1 mb-0.5 block';
            $lblB = 'text-blue-400 text-[0.6rem] font-bold uppercase ml-1 mb-0.5 block';
            $sel  = 'w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-1.5 text-white text-[0.72rem] focus:outline-none focus:border-blue-500/60';
        @endphp

        <form wire:submit.prevent="enviar" class="flex flex-col gap-4">

            {{-- TIPO + MATRÍCULA --}}
            <div class="flex gap-3">
                <div class="w-28 shrink-0">
                    <label class="{{ $lblB }}">Tipo</label>
                    <select wire:model="tipo" class="{{ $sel }}">
                        <option value="normal" class="bg-slate-800">Normal</option>
                        <option value="global" class="bg-slate-800">Global</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="{{ $lblB }}">Matrícula *</label>
                    <input wire:model="matricula" type="text" placeholder="00-XX-00"
                           class="{{ $errors->has('matricula') ? $inpE : $inp }} uppercase">
                    @error('matricula') <span class="text-red-300 text-[0.6rem] mt-0.5 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ── ORIGEM ──────────────────────────────── --}}
            <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-2xl p-3.5"
                 x-data="{
                     geoState: 'idle',
                     async getLocalizacao() {
                         if (!navigator.geolocation) { this.geoState = 'error'; return; }
                         this.geoState = 'loading';
                         navigator.geolocation.getCurrentPosition(
                             pos => this.geocodeInverso(pos.coords.latitude, pos.coords.longitude),
                             ()  => { this.geoState = 'error'; },
                             { timeout: 10000, enableHighAccuracy: true }
                         );
                     },
                     async geocodeInverso(lat, lon) {
                         try {
                             const r = await fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat='+lat+'&lon='+lon+'&accept-language=pt', { headers: {'Accept':'application/json'} });
                             const d = await r.json();
                             const a = d.address || {};
                             const morada = [a.road, a.house_number].filter(Boolean).join(' ');
                             const localidade = a.city || a.town || a.village || a.county || '';
                             await $wire.set('localCargaNome', morada || d.display_name?.split(',')[0] || '');
                             await $wire.set('localCargaMorada', morada);
                             await $wire.set('localCargaLocalidade', localidade);
                             await $wire.set('localCargaCpostal', a.postcode || '');
                             await $wire.set('originTab', 'confirmado');
                             this.geoState = 'success';
                         } catch(e) { this.geoState = 'error'; }
                     }
                 }">

                <div class="text-emerald-300 text-[0.65rem] font-black uppercase mb-3">↑ ORIGEM / CARGA</div>

                @if($originTab === 'confirmado')
                {{-- Confirmado --}}
                <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-3 py-2.5 mb-2.5">
                    <div>
                        <div class="text-white text-[0.82rem] font-bold">{{ $localCargaNome }}</div>
                        @if($localCargaMorada || $localCargaLocalidade)
                        <div class="text-emerald-300/70 text-[0.65rem]">
                            {{ implode(' · ', array_filter([$localCargaMorada, $localCargaLocalidade, $localCargaCpostal])) }}
                        </div>
                        @endif
                    </div>
                    <button type="button" wire:click="$set('originTab', 'escolher')"
                            class="text-white/40 hover:text-white/70 text-[0.65rem] font-bold border border-white/15 rounded-lg px-2 py-1 shrink-0 bg-transparent">
                        alterar
                    </button>
                </div>

                @else
                {{-- Tabs de modo --}}
                <div class="flex gap-1 bg-black/20 p-1 rounded-xl mb-3">
                    @foreach(['gps' => '📍 GPS', 'frequente' => '⭐ Freq.', 'pesquisa' => '🔍 CT', 'manual' => '✏️ Manual'] as $m => $label)
                    <button type="button" wire:click="$set('originModo', '{{ $m }}')"
                            class="flex-1 py-1.5 rounded-lg text-[0.58rem] font-bold border-none transition-colors
                                   {{ $originModo === $m ? 'bg-emerald-500/30 text-emerald-300' : 'bg-transparent text-white/40' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- GPS --}}
                @if($originModo === 'gps')
                <button type="button" @click="getLocalizacao()" :disabled="geoState === 'loading'"
                        class="w-full flex items-center gap-3 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/35 rounded-xl px-3.5 py-3 transition-colors text-left">
                    <span class="text-xl shrink-0">📍</span>
                    <div class="flex-1">
                        <div class="text-emerald-200 text-[0.78rem] font-bold"
                             x-text="geoState === 'loading' ? 'A obter localização...' : 'Usar localização atual'"></div>
                        <div class="text-[0.6rem]"
                             :class="geoState === 'error' ? 'text-red-400' : 'text-emerald-400/60'"
                             x-text="geoState === 'error' ? 'Não foi possível obter localização' : 'GPS do dispositivo'"></div>
                    </div>
                    <span x-show="geoState === 'loading'" class="text-emerald-400 animate-pulse text-lg">⟳</span>
                    <span x-show="geoState === 'success'" class="text-emerald-400">✓</span>
                </button>
                @endif

                {{-- Frequente --}}
                @if($originModo === 'frequente')
                @if(count($locaisFrequentes))
                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                    @foreach($locaisFrequentes as $lf)
                    <button type="button"
                            wire:click="selecionarLocalCarga({{ $lf['id'] }})"
                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                        <div class="min-w-0 flex-1">
                            <div class="text-white text-[0.72rem] font-semibold leading-tight truncate">{{ $lf['nome'] }}</div>
                            @if($lf['localidade'])
                            <div class="text-white/35 text-[0.58rem] leading-tight truncate">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>
                            @endif
                        </div>
                        <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] ml-2 shrink-0">→</span>
                    </button>
                    @endforeach
                </div>
                @else
                <p class="text-white/30 text-[0.65rem] text-center py-4">Sem locais frequentes registados.</p>
                @endif
                @endif

                {{-- Pesquisa CT --}}
                @if($originModo === 'pesquisa')
                <div class="flex flex-col gap-2.5">

                    @php
                        $ctBadge = 'flex items-center justify-between px-2.5 py-1.5 bg-emerald-500/12 border border-emerald-500/25 rounded-xl';
                        $ctReset = 'text-white/25 text-[0.65rem] border border-white/10 rounded-lg px-1.5 py-0.5 bg-transparent hover:text-white/50 shrink-0';
                        $ctSel   = 'w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white focus:outline-none focus:border-emerald-500/50';
                        $ctLbl   = 'text-white/30 text-[0.55rem] font-bold uppercase mb-1 ml-0.5 block';
                    @endphp

                    {{-- Distrito --}}
                    @if($originDD)
                    <div class="{{ $ctBadge }}">
                        <div>
                            <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Distrito</div>
                            <div class="text-emerald-200 text-[0.72rem] font-bold">{{ $distritos->firstWhere('dd', $originDD)?->desig }}</div>
                        </div>
                        <button type="button" wire:click="$set('originDD', '')" class="{{ $ctReset }}">✕</button>
                    </div>
                    @else
                    <div x-data="{
                        open: false, search: '',
                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                        get filtered() {
                            if (!this.search) return this.items;
                            const q = this.norm(this.search);
                            return this.items.filter(i => this.norm(i.desig).includes(q));
                        }
                    }">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                :class="open ? 'border-emerald-500/30 bg-emerald-500/8' : ''">
                            <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Distrito' : 'Selecionar Distrito'"></span>
                            <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                        </button>
                        <div x-show="open" x-transition.duration.150ms class="mt-1">
                            <input type="text" x-model="search" placeholder="Filtrar distrito..."
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50 mb-1.5">
                            <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                <template x-for="d in filtered" :key="d.dd">
                                    <button type="button" @click="$wire.set('originDD', d.dd)"
                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                        <span class="text-white text-[0.72rem] font-semibold" x-text="d.desig"></span>
                                        <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0">→</span>
                                    </button>
                                </template>
                                <template x-if="filtered.length === 0">
                                    <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                                </template>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Concelho --}}
                    @if($originDD && count($concelhos))
                        @if($originCC)
                        <div class="{{ $ctBadge }}">
                            <div>
                                <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Concelho</div>
                                <div class="text-emerald-200 text-[0.72rem] font-bold">{{ $concelhos->firstWhere('cc', $originCC)?->desig }}</div>
                            </div>
                            <button type="button" wire:click="$set('originCC', '')" class="{{ $ctReset }}">✕</button>
                        </div>
                        @else
                        <div x-data="{
                            open: true, search: '',
                            items: @js($concelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                            get filtered() {
                                if (!this.search) return this.items;
                                const q = this.norm(this.search);
                                return this.items.filter(i => this.norm(i.desig).includes(q));
                            }
                        }">
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                    :class="open ? 'border-emerald-500/30 bg-emerald-500/8' : ''">
                                <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Concelho' : 'Selecionar Concelho'"></span>
                                <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                <input type="text" x-model="search" placeholder="Filtrar concelho..."
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50 mb-1.5">
                                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                    <template x-for="c in filtered" :key="c.cc">
                                        <button type="button" @click="$wire.set('originCC', c.cc)"
                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                            <span class="text-white text-[0.72rem] font-semibold" x-text="c.desig"></span>
                                            <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0">→</span>
                                        </button>
                                    </template>
                                    <template x-if="filtered.length === 0">
                                        <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    {{-- Localidade --}}
                    @if($originCC && count($localidades))
                        @if($originLocalidade)
                        <div class="{{ $ctBadge }}">
                            <div>
                                <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Localidade</div>
                                <div class="text-emerald-200 text-[0.72rem] font-bold">{{ $originLocalidade }}</div>
                            </div>
                            <button type="button" wire:click="$set('originLocalidade', '')" class="{{ $ctReset }}">✕</button>
                        </div>
                        @else
                        <div x-data="{
                            open: true, search: '',
                            items: @js($localidades->values()),
                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                            get filtered() {
                                if (!this.search) return this.items;
                                const q = this.norm(this.search);
                                return this.items.filter(l => this.norm(l).includes(q));
                            }
                        }">
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                    :class="open ? 'border-emerald-500/30 bg-emerald-500/8' : ''">
                                <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Localidade' : 'Selecionar Localidade'"></span>
                                <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                <input type="text" x-model="search" placeholder="Filtrar localidade..."
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50 mb-1.5">
                                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                    <template x-for="l in filtered" :key="l">
                                        <button type="button" @click="$wire.set('originLocalidade', l)"
                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                            <span class="text-white text-[0.72rem] font-semibold" x-text="l"></span>
                                            <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0">→</span>
                                        </button>
                                    </template>
                                    <template x-if="filtered.length === 0">
                                        <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    {{-- Sublocal / Freguesia --}}
                    @if($originLocalidade && count($artLocals))
                        @if($originArtLocal)
                        <div class="{{ $ctBadge }}">
                            <div>
                                <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Freguesia</div>
                                <div class="text-emerald-200 text-[0.72rem] font-bold">{{ $originArtLocal }}</div>
                            </div>
                            <button type="button" wire:click="$set('originArtLocal', '')" class="{{ $ctReset }}">✕</button>
                        </div>
                        @else
                        <div x-data="{
                            open: true, search: '',
                            items: @js($artLocals->values()),
                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                            get filtered() {
                                if (!this.search) return this.items;
                                const q = this.norm(this.search);
                                return this.items.filter(l => this.norm(l).includes(q));
                            }
                        }">
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                    :class="open ? 'border-emerald-500/30 bg-emerald-500/8' : ''">
                                <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Freguesia' : 'Selecionar Freguesia'"></span>
                                <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                <input type="text" x-model="search" placeholder="Filtrar freguesia..."
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50 mb-1.5">
                                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                    <template x-for="l in filtered" :key="l">
                                        <button type="button" @click="$wire.set('originArtLocal', l)"
                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                            <span class="text-white text-[0.72rem] font-semibold" x-text="l"></span>
                                            <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0">→</span>
                                        </button>
                                    </template>
                                    <template x-if="filtered.length === 0">
                                        <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    {{-- Rua (server-side search con debounce) --}}
                    @if($originLocalidade)
                        @if($originRua)
                        <div class="{{ $ctBadge }}">
                            <div>
                                <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Rua (opcional)</div>
                                <div class="text-emerald-200 text-[0.72rem] font-bold">{{ $originRua }}</div>
                            </div>
                            <button type="button" wire:click="$set('originRua', '')" class="{{ $ctReset }}">✕</button>
                        </div>
                        @else
                        <div x-data="{ q: '', res: [] }">
                            <span class="{{ $ctLbl }}">Rua (opcional)</span>
                            <input type="text" x-model="q"
                                   @input.debounce.350ms="if (q.length >= 2) { $wire.call('searchRuas', q).then(r => { res = r; }); } else { res = []; }"
                                   placeholder="Ex: Caminho Novo de Santana..."
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50 mb-1.5"
                                   autocomplete="off">
                            <template x-if="q.length < 2">
                                <div class="text-white/20 text-[0.62rem] text-center py-2">Escreva 2+ letras para pesquisar</div>
                            </template>
                            <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                <template x-for="r in res" :key="r">
                                    <button type="button" @click="$wire.set('originRua', r)"
                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                        <span class="text-white text-[0.72rem] font-semibold" x-text="r"></span>
                                        <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0">→</span>
                                    </button>
                                </template>
                                <template x-if="q.length >= 2 && res.length === 0">
                                    <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                                </template>
                            </div>
                        </div>
                        @endif
                    @endif

                    @if($originLocalidade)
                    <button type="button" wire:click="aplicarPesquisaOrigem"
                            class="w-full bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 rounded-xl py-2 text-[0.72rem] font-bold transition-colors">
                        ✓ Usar esta localização
                    </button>
                    @endif

                </div>
                @endif

                {{-- Manual --}}
                @if($originModo === 'manual')
                <div>
                    <input wire:model.blur="localCargaNome" type="text" placeholder="Ex: ESTALEIRO CME"
                           class="{{ $errors->has('localCargaNome') ? $inpE : $inp }}">
                    @error('localCargaNome') <span class="text-red-300 text-[0.6rem] mt-0.5 block">{{ $message }}</span> @enderror
                    <button type="button" wire:click="$set('originTab', 'confirmado')"
                            class="w-full mt-2 bg-white/5 hover:bg-white/10 text-white/60 border border-white/10 rounded-xl py-2 text-[0.7rem] font-bold transition-colors">
                        Confirmar
                    </button>
                </div>
                @endif
                @endif {{-- end originTab --}}

                {{-- Data/Hora --}}
                <div class="flex gap-2 mt-2.5">
                    <div class="flex-1">
                        <label class="{{ $lbl }}">Data *</label>
                        <input wire:model="dataInicio" type="date"
                               class="{{ $errors->has('dataInicio') ? $inpE : $inp }}">
                    </div>
                    <div class="w-24 shrink-0">
                        <label class="{{ $lbl }}">Hora *</label>
                        <input wire:model="horaInicio" type="time"
                               class="{{ $errors->has('horaInicio') ? $inpE : $inp }}">
                    </div>
                </div>
            </div>

            {{-- ── DESTINO ──────────────────────────────── --}}
            <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-3.5">
                <div class="text-red-400 text-[0.65rem] font-black uppercase mb-3">↓ DESTINO / DESCARGA</div>

                {{-- Localidade autocomplete (CT) --}}
                <div class="mb-2.5"
                     x-data="{ q: @js($destinoLocalidade), res: [], open: false }"
                     @click.outside="open = false">
                    <label class="{{ $lblB }}">Localidade</label>
                    <div class="relative">
                        <input type="text" x-model="q"
                               @input.debounce.350ms="if (q.length >= 2) { $wire.call('searchLocalidades', q).then(r => { res = r; open = r.length > 0; }); } else { res = []; open = false; }"
                               placeholder="ex: Funchal, Machico, Caniçal..."
                               class="w-full bg-red-500/10 border border-red-500/25 rounded-xl px-3 py-2.5 text-white text-[0.82rem] placeholder-red-400/40 focus:outline-none"
                               autocomplete="off">
                        <div x-show="open" x-transition
                             class="absolute z-30 left-0 right-0 mt-1 bg-slate-800 border border-white/20 rounded-xl shadow-xl overflow-hidden max-h-44 overflow-y-auto">
                            <template x-for="r in res" :key="r.localidade">
                                <button type="button"
                                        @click="$wire.call('selecionarLocalidade', r.localidade, r.cp); q = r.localidade; open = false;"
                                        class="w-full text-left px-3 py-2.5 hover:bg-white/10 flex justify-between items-center gap-2 border-b border-white/5 last:border-0">
                                    <span class="text-white text-[0.82rem] font-semibold" x-text="r.localidade"></span>
                                    <span class="text-white/40 text-[0.65rem] font-mono shrink-0" x-text="r.cp"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @if($destinoLocalidade)
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-emerald-300 text-[0.65rem]">✓ {{ $destinoLocalidade }}@if($destinoCpostal) · {{ $destinoCpostal }}@endif</span>
                    </div>
                    @endif
                </div>

                {{-- Local frequente --}}
                <div class="mb-2.5"
                     x-data="{ q: '', res: [], open: false }"
                     @click.outside="open = false">
                    <label class="{{ $lbl }}">ou escolher local frequente</label>
                    <div class="relative">
                        <input type="text" x-model="q"
                               @input.debounce.300ms="if (q.length >= 1) { $wire.call('searchLocaisFrequentes', q).then(r => { res = r; open = r.length > 0; }); } else { res = []; open = false; }"
                               placeholder="⭐ Pesquisar local frequente..."
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white/70 text-[0.78rem] placeholder-white/25 focus:outline-none"
                               autocomplete="off">
                        <div x-show="open" x-transition
                             class="absolute z-30 left-0 right-0 mt-1 bg-slate-800 border border-white/20 rounded-xl shadow-xl overflow-hidden">
                            <template x-for="l in res" :key="l.id">
                                <button type="button"
                                        @click="$wire.call('selecionarLocalDestino', l.id); q = l.nome; open = false;"
                                        class="w-full text-left px-3 py-2.5 hover:bg-white/10 flex justify-between items-center gap-2 border-b border-white/5 last:border-0">
                                    <div>
                                        <div class="text-white text-[0.78rem] font-semibold" x-text="l.nome"></div>
                                        <div class="text-white/40 text-[0.65rem]" x-text="l.localidade"></div>
                                    </div>
                                    <span class="text-white/40 text-[0.65rem] font-mono shrink-0" x-text="l.cp"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5">
                    <div>
                        <label class="{{ $lbl }}">Morada / Rua</label>
                        <input wire:model="destinoMorada" type="text" placeholder="Rua, número..."
                               class="{{ $inp }}">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="{{ $lbl }}">Data descarga</label>
                            <input wire:model="dataFim" type="date" class="{{ $inp }}">
                        </div>
                        <div class="w-24 shrink-0">
                            <label class="{{ $lbl }}">Hora</label>
                            <input wire:model="horaFim" type="time" class="{{ $inp }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── BENS A TRANSPORTAR ───────────────────── --}}
            <div class="bg-white/3 border border-white/8 rounded-2xl p-3.5">
                <div class="flex justify-between items-center mb-3">
                    <div class="text-blue-400 text-[0.65rem] font-black uppercase">📦 Bens a transportar</div>
                    <button type="button" wire:click="addItem"
                            class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2.5 py-1 rounded-lg text-[0.6rem] font-extrabold">
                        + ITEM
                    </button>
                </div>
                <div class="flex flex-col gap-2.5">
                    @foreach($items as $index => $item)
                    <div class="bg-black/20 p-2.5 rounded-xl"
                         x-data="{ q{{ $index }}: @js($item['descricao']), res{{ $index }}: [], show{{ $index }}: false }"
                         @click.outside="show{{ $index }} = false">
                        <div class="flex flex-col gap-2">
                            <div class="relative">
                                <input
                                    x-model="q{{ $index }}"
                                    @input.debounce.300ms="$wire.set('items.{{ $index }}.descricao', q{{ $index }}); if (q{{ $index }}.length >= 2) { $wire.call('searchMateriais', q{{ $index }}).then(r => { res{{ $index }} = r; show{{ $index }} = r.length > 0; }); } else { res{{ $index }} = []; show{{ $index }} = false; }"
                                    @focus="if (q{{ $index }}.length >= 2 && res{{ $index }}.length > 0) show{{ $index }} = true"
                                    type="text"
                                    placeholder="Descrição do bem..."
                                    class="w-full bg-white/5 border {{ $errors->has('items.'.$index.'.descricao') ? 'border-red-500/60' : 'border-white/10' }} rounded-lg px-2.5 py-2 text-white text-[0.82rem] placeholder-white/25 focus:outline-none"
                                    autocomplete="off">
                                <div x-show="show{{ $index }}" x-transition
                                     class="absolute z-30 left-0 right-0 mt-1 bg-slate-800 border border-white/20 rounded-lg shadow-xl overflow-hidden">
                                    <template x-for="m in res{{ $index }}" :key="m.codigo">
                                        <button type="button"
                                                @click="$wire.set('items.{{ $index }}.descricao', m.nome); $wire.set('items.{{ $index }}.unidade', m.unidade); q{{ $index }} = m.nome; show{{ $index }} = false"
                                                class="w-full text-left px-3 py-2 hover:bg-white/10 flex justify-between items-center gap-2 border-b border-white/5 last:border-0">
                                            <span class="text-white text-[0.78rem] font-semibold" x-text="m.nome"></span>
                                            <span class="text-white/40 text-[0.65rem] font-mono shrink-0" x-text="m.codigo + ' · ' + m.unidade"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <input wire:model.blur="items.{{ $index }}.quantidade"
                                       type="number" step="0.01" min="0.01" placeholder="Qtd."
                                       class="w-20 bg-white/5 border {{ $errors->has('items.'.$index.'.quantidade') ? 'border-red-500/60' : 'border-white/10' }} rounded-lg px-2.5 py-2 text-white text-[0.82rem] focus:outline-none">
                                <select wire:model="items.{{ $index }}.unidade"
                                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-2.5 py-2 text-white text-[0.82rem] focus:outline-none">
                                    @foreach(\App\Models\Material::UNIDADES as $u)
                                        <option value="{{ $u }}" class="bg-slate-800">{{ $u }}</option>
                                    @endforeach
                                </select>
                                @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-red-400/70 hover:text-red-400 bg-transparent border-none text-lg leading-none shrink-0">✕</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-black py-4 rounded-2xl border-none shadow-[0_10px_20px_rgba(59,130,246,0.25)] text-[0.9rem] transition-colors">
                <span wire:loading.remove>ENVIAR PEDIDO</span>
                <span wire:loading wire:target="enviar">A PROCESSAR...</span>
            </button>

        </form>

        {{-- ─── TAB: HISTÓRICO ──────────────────────────── --}}
        @elseif($activeGuiaTab === 'historico')

        <div class="flex flex-col gap-3">
            @forelse($minhasGuias as $g)
            @php
                $stClasses = match($g->estado) {
                    'pendente' => 'text-yellow-400 bg-yellow-400/15 border-yellow-400/30',
                    'emitida'  => 'text-emerald-400 bg-emerald-400/15 border-emerald-400/30',
                    'recusada' => 'text-red-400 bg-red-400/15 border-red-400/30',
                    default    => 'text-gray-400 bg-gray-400/15 border-gray-400/30',
                };
                $stLabel = match($g->estado) {
                    'pendente' => 'PENDENTE',
                    'emitida'  => 'EMITIDA',
                    'recusada' => 'RECUSADA',
                    default    => strtoupper($g->estado),
                };
            @endphp
            <div class="bg-white/5 border border-white/10 rounded-2xl p-3.5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="text-white font-extrabold text-[0.9rem]">{{ $g->matricula }}</div>
                        <div class="text-white/40 text-[0.65rem]">{{ $g->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-lg text-[0.6rem] font-black border {{ $stClasses }}">{{ $stLabel }}</span>
                </div>

                @if($g->estado === 'emitida' && $g->numero_at)
                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-3 py-2 mb-2.5">
                    <div class="text-emerald-300 text-[0.55rem] font-black uppercase">Nº AT — Autoridade Tributária</div>
                    <div class="text-white text-[1.05rem] font-black tracking-[0.05em]">{{ $g->numero_at }}</div>
                </div>
                @endif

                @if($g->estado === 'recusada' && $g->motivo_recusa)
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl px-3 py-2 mb-2.5">
                    <div class="text-red-300 text-[0.55rem] font-black uppercase">Motivo</div>
                    <div class="text-white/80 text-[0.78rem]">{{ $g->motivo_recusa }}</div>
                </div>
                @endif

                <div class="text-white/50 text-[0.75rem] mb-3">
                    {{ $g->local_carga_nome ?: $g->local_carga_localidade }}
                    → {{ $g->destino_nome ?: $g->destino_localidade ?: '—' }}
                </div>

                <button type="button" wire:click="repetirGuia({{ $g->id }})"
                        class="w-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 text-[0.7rem] font-bold py-2 rounded-xl transition-colors">
                    ↺ Repetir este pedido
                </button>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="text-white/20 text-5xl mb-3">📋</div>
                <p class="text-white/30 text-sm">Sem guias registadas.</p>
                <button wire:click="$set('activeGuiaTab', 'solicitar')"
                        class="mt-4 bg-blue-500/20 text-blue-300 border border-blue-500/30 px-4 py-2 rounded-xl text-[0.75rem] font-bold">
                    Fazer primeiro pedido
                </button>
            </div>
            @endforelse
        </div>

        @endif
    </div>
</div>
