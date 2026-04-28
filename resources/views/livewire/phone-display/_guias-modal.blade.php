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
                    <button type="button" wire:click="alterarOrigem"
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

                    {{-- Rua — busca Nominatim con botón --}}
                    @if($originCC)
                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                        <span class="{{ $ctLbl }}">Rua / Local</span>
                        <div class="flex gap-1.5 mb-1.5">
                            <input type="text" x-model="q"
                                   @keydown.enter.prevent="if (q.length >= 3) { loading = true; searched = true; $wire.call('searchRuas', q).then(r => { res = r; loading = false; }); }"
                                   placeholder="Ex: Caminho Novo de Santana..."
                                   class="flex-1 bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-emerald-500/50"
                                   autocomplete="off">
                            <button type="button"
                                    @click="if (q.length >= 3) { loading = true; searched = true; $wire.call('searchRuas', q).then(r => { res = r; loading = false; }); }"
                                    :disabled="q.length < 3 || loading"
                                    class="bg-emerald-500/20 hover:bg-emerald-500/30 disabled:opacity-30 text-emerald-300 border border-emerald-500/40 rounded-xl px-3 py-2 text-[0.7rem] font-bold transition-colors shrink-0">
                                <span x-show="!loading">🔍</span>
                                <span x-show="loading" class="animate-pulse">⟳</span>
                            </button>
                        </div>
                        <template x-if="!searched">
                            <div class="text-white/20 text-[0.62rem] text-center py-2">Escreva e prima 🔍 para pesquisar</div>
                        </template>
                        <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                            <template x-for="r in res" :key="r.road">
                                <button type="button"
                                        @click="$wire.call('selecionarRuaOrigem', r.road, r.localidade ?? '', r.postcode ?? '')"
                                        class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-emerald-500/15 active:bg-emerald-500/25 border border-white/8 hover:border-emerald-500/30 rounded-xl px-3 py-2 transition-colors group">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-white text-[0.72rem] font-semibold truncate" x-text="r.road"></div>
                                        <div class="text-white/35 text-[0.58rem] truncate"
                                             x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')"></div>
                                    </div>
                                    <span class="text-white/20 group-hover:text-emerald-400 text-[0.65rem] shrink-0 ml-2">→</span>
                                </button>
                            </template>
                            <template x-if="searched && !loading && res.length === 0">
                                <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                            </template>
                        </div>
                    </div>
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

                @if($destinoTab === 'confirmado')
                {{-- Confirmado --}}
                <div class="flex items-center justify-between bg-red-500/10 border border-red-500/30 rounded-xl px-3 py-2.5 mb-2.5">
                    <div>
                        <div class="text-white text-[0.82rem] font-bold">{{ $destinoNome }}</div>
                        @if($destinoMorada || $destinoLocalidade)
                        <div class="text-red-300/70 text-[0.65rem]">
                            {{ implode(' · ', array_filter([$destinoMorada, $destinoLocalidade, $destinoCpostal])) }}
                        </div>
                        @endif
                    </div>
                    <button type="button" wire:click="alterarDestino"
                            class="text-white/40 hover:text-white/70 text-[0.65rem] font-bold border border-white/15 rounded-lg px-2 py-1 shrink-0 bg-transparent">
                        alterar
                    </button>
                </div>

                @else
                {{-- Tabs de modo --}}
                <div class="flex gap-1 bg-black/20 p-1 rounded-xl mb-3">
                    @foreach(['frequente' => '⭐ Freq.', 'pesquisa' => '🔍 Pesquisa', 'manual' => '✏️ Manual'] as $m => $label)
                    <button type="button" wire:click="$set('destinoModo', '{{ $m }}')"
                            class="flex-1 py-1.5 rounded-lg text-[0.58rem] font-bold border-none transition-colors
                                   {{ $destinoModo === $m ? 'bg-red-500/30 text-red-300' : 'bg-transparent text-white/40' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                @if($destinoModo === 'frequente')
                @if(count($locaisFrequentes))
                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                    @foreach($locaisFrequentes as $lf)
                    <button type="button" wire:click="selecionarLocalDestino({{ $lf['id'] }})"
                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 active:bg-red-500/25 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                        <div class="min-w-0 flex-1">
                            <div class="text-white text-[0.72rem] font-semibold leading-tight truncate">{{ $lf['nome'] }}</div>
                            @if($lf['localidade'])
                            <div class="text-white/35 text-[0.58rem] leading-tight truncate">{{ $lf['localidade'] }}@if($lf['cp']) · {{ $lf['cp'] }}@endif</div>
                            @endif
                        </div>
                        <span class="text-white/20 group-hover:text-red-400 text-[0.65rem] ml-2 shrink-0">→</span>
                    </button>
                    @endforeach
                </div>
                @else
                <p class="text-white/30 text-[0.65rem] text-center py-4">Sem locais frequentes registados.</p>
                @endif
                @endif

                @if($destinoModo === 'pesquisa')
                @php
                    $ctBadgeD = 'flex items-center justify-between px-2.5 py-1.5 bg-red-500/12 border border-red-500/25 rounded-xl';
                    $ctResetD = 'text-white/25 text-[0.65rem] border border-white/10 rounded-lg px-1.5 py-0.5 bg-transparent hover:text-white/50 shrink-0';
                @endphp
                <div class="flex flex-col gap-2.5">
                    {{-- Distrito --}}
                    @if($destinoDD)
                    <div class="{{ $ctBadgeD }}">
                        <div>
                            <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Distrito</div>
                            <div class="text-red-200 text-[0.72rem] font-bold">{{ $distritos->firstWhere('dd', $destinoDD)?->desig }}</div>
                        </div>
                        <button type="button" wire:click="$set('destinoDD', '')" class="{{ $ctResetD }}">✕</button>
                    </div>
                    @else
                    <div x-data="{
                        open: false, search: '',
                        items: @js($distritos->map(fn($d) => ['dd' => $d->dd, 'desig' => $d->desig])->values()),
                        norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                        get filtered() { if (!this.search) return this.items; const q = this.norm(this.search); return this.items.filter(i => this.norm(i.desig).includes(q)); }
                    }">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                :class="open ? 'border-red-500/30 bg-red-500/8' : ''">
                            <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Distrito' : 'Selecionar Distrito'"></span>
                            <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                        </button>
                        <div x-show="open" x-transition.duration.150ms class="mt-1">
                            <input type="text" x-model="search" placeholder="Filtrar distrito..."
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-red-500/50 mb-1.5">
                            <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                <template x-for="d in filtered" :key="d.dd">
                                    <button type="button" @click="$wire.set('destinoDD', d.dd)"
                                            class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                        <span class="text-white text-[0.72rem] font-semibold" x-text="d.desig"></span>
                                        <span class="text-white/20 group-hover:text-red-400 text-[0.65rem] shrink-0">→</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Concelho --}}
                    @if($destinoDD && count($destinoConcelhos))
                        @if($destinoCC)
                        <div class="{{ $ctBadgeD }}">
                            <div>
                                <div class="text-white/25 text-[0.5rem] font-bold uppercase leading-none mb-0.5">Concelho</div>
                                <div class="text-red-200 text-[0.72rem] font-bold">{{ $destinoConcelhos->firstWhere('cc', $destinoCC)?->desig }}</div>
                            </div>
                            <button type="button" wire:click="$set('destinoCC', '')" class="{{ $ctResetD }}">✕</button>
                        </div>
                        @else
                        <div x-data="{
                            open: true, search: '',
                            items: @js($destinoConcelhos->map(fn($c) => ['cc' => $c->cc, 'desig' => $c->desig])->values()),
                            norm(s) { return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); },
                            get filtered() { if (!this.search) return this.items; const q = this.norm(this.search); return this.items.filter(i => this.norm(i.desig).includes(q)); }
                        }">
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-3 py-2 transition-colors"
                                    :class="open ? 'border-red-500/30 bg-red-500/8' : ''">
                                <span class="text-white/40 text-[0.72rem]" x-text="open ? 'Concelho' : 'Selecionar Concelho'"></span>
                                <span class="text-white/25 text-[0.65rem]" x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <div x-show="open" x-transition.duration.150ms class="mt-1">
                                <input type="text" x-model="search" placeholder="Filtrar concelho..."
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-red-500/50 mb-1.5">
                                <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                                    <template x-for="c in filtered" :key="c.cc">
                                        <button type="button" @click="$wire.set('destinoCC', c.cc)"
                                                class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                            <span class="text-white text-[0.72rem] font-semibold" x-text="c.desig"></span>
                                            <span class="text-white/20 group-hover:text-red-400 text-[0.65rem] shrink-0">→</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    {{-- Rua Nominatim --}}
                    @if($destinoCC)
                    <div x-data="{ q: '', res: [], loading: false, searched: false }">
                        <span class="text-white/30 text-[0.55rem] font-bold uppercase mb-1 ml-0.5 block">Rua / Local</span>
                        <div class="flex gap-1.5 mb-1.5">
                            <input type="text" x-model="q"
                                   @keydown.enter.prevent="if (q.length >= 3) { loading = true; searched = true; $wire.call('searchRuasDestino', q).then(r => { res = r; loading = false; }); }"
                                   placeholder="Ex: Rua João de Deus..."
                                   class="flex-1 bg-black/20 border border-white/10 rounded-xl px-2.5 py-2 text-white text-[0.72rem] placeholder-white/20 focus:outline-none focus:border-red-500/50"
                                   autocomplete="off">
                            <button type="button"
                                    @click="if (q.length >= 3) { loading = true; searched = true; $wire.call('searchRuasDestino', q).then(r => { res = r; loading = false; }); }"
                                    :disabled="q.length < 3 || loading"
                                    class="bg-red-500/20 hover:bg-red-500/30 disabled:opacity-30 text-red-300 border border-red-500/40 rounded-xl px-3 py-2 text-[0.7rem] font-bold transition-colors shrink-0">
                                <span x-show="!loading">🔍</span>
                                <span x-show="loading" class="animate-pulse">⟳</span>
                            </button>
                        </div>
                        <template x-if="!searched">
                            <div class="text-white/20 text-[0.62rem] text-center py-2">Escreva e prima 🔍 para pesquisar</div>
                        </template>
                        <div class="flex flex-col gap-1 max-h-44 overflow-y-auto pr-0.5">
                            <template x-for="r in res" :key="r.road">
                                <button type="button"
                                        @click="$wire.call('selecionarRuaDestino', r.road, r.localidade ?? '', r.postcode ?? '')"
                                        class="flex items-center justify-between w-full text-left bg-white/5 hover:bg-red-500/15 active:bg-red-500/25 border border-white/8 hover:border-red-500/30 rounded-xl px-3 py-2 transition-colors group">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-white text-[0.72rem] font-semibold truncate" x-text="r.road"></div>
                                        <div class="text-white/35 text-[0.58rem] truncate"
                                             x-text="[r.suburb, r.localidade, r.postcode].filter(Boolean).join(' · ')"></div>
                                    </div>
                                    <span class="text-white/20 group-hover:text-red-400 text-[0.65rem] shrink-0 ml-2">→</span>
                                </button>
                            </template>
                            <template x-if="searched && !loading && res.length === 0">
                                <div class="text-white/25 text-[0.65rem] text-center py-3">Sem resultados</div>
                            </template>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($destinoModo === 'manual')
                <div class="flex flex-col gap-2">
                    <input wire:model.blur="destinoNome" type="text" placeholder="Nome do local..."
                           class="{{ $inp }}">
                    <input wire:model.blur="destinoMorada" type="text" placeholder="Morada / Rua..."
                           class="{{ $inp }}">
                    <input wire:model.blur="destinoLocalidade" type="text" placeholder="Localidade..."
                           class="{{ $inp }}">
                    <button type="button" wire:click="$set('destinoTab', 'confirmado')"
                            class="w-full bg-white/5 hover:bg-white/10 text-white/60 border border-white/10 rounded-xl py-2 text-[0.7rem] font-bold transition-colors">
                        Confirmar
                    </button>
                </div>
                @endif
                @endif

                {{-- Data/Hora descarga --}}
                <div class="flex gap-2 mt-2.5">
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
                                    @input.debounce.300ms="if (q{{ $index }}.length >= 2) { $wire.call('searchMateriais', q{{ $index }}).then(r => { res{{ $index }} = r; show{{ $index }} = r.length > 0; }); } else { res{{ $index }} = []; show{{ $index }} = false; }"
                                    @blur="$wire.set('items.{{ $index }}.descricao', q{{ $index }})"
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
