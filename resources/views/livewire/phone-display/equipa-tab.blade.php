<div x-data="{
        conflito: false,
        conflitoVehicleId: null,
        conflitoNome: '',
        conflitoDesde: '',
        jaConduz: false,
        jaConduzMatricula: '',
        jaConduzDesde: ''
    }"
    x-on:condutor-conflito.window="
        conflito = true;
        conflitoVehicleId = $event.detail[0].vehicleId;
        conflitoNome = $event.detail[0].condutorNome;
        conflitoDesde = $event.detail[0].desde;
    "
    x-on:condutor-ja-a-conduzir.window="
        jaConduz = true;
        jaConduzMatricula = $event.detail[0].matricula;
        jaConduzDesde = $event.detail[0].desde;
    ">

    {{-- ── MODAL CONFLITO DE CONDUTOR ─────────────────────────── --}}
    <div x-show="conflito" x-cloak
         class="fixed inset-0 z-50 flex items-end justify-center pb-6 px-4"
         style="background:rgba(0,0,0,0.7);">
        <div class="bg-[#1a2540] border border-amber-500/40 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="bg-amber-500/20 border-b border-amber-500/30 px-5 py-4 flex items-center gap-3">
                <span class="text-2xl">🚗</span>
                <div>
                    <div class="text-amber-300 font-black text-sm">Condutor ativo registado</div>
                    <div class="text-amber-200/70 text-xs mt-0.5">Este veículo já tem um condutor</div>
                </div>
            </div>
            <div class="px-5 py-4">
                <p class="text-white/80 text-sm">
                    <strong class="text-white" x-text="conflitoNome"></strong>
                    está registado como condutor desde as <strong class="text-amber-300" x-text="conflitoDesde"></strong>.
                </p>
                <p class="text-white/55 text-xs mt-2">Se o veículo está consigo, pode assumir a condução. O condutor anterior será notificado.</p>
            </div>
            <div class="px-5 pb-5 flex gap-2.5">
                <button @click="conflito = false"
                        class="flex-1 bg-white/8 border border-white/20 text-white/80 font-bold py-2.5 rounded-xl text-sm">
                    Cancelar
                </button>
                <button @click="$wire.assumirConducao(conflitoVehicleId); conflito = false"
                        class="flex-1 bg-amber-500 text-amber-950 font-black py-2.5 rounded-xl text-sm">
                    Assumir condução
                </button>
            </div>
        </div>
    </div>

    {{-- ── MODAL JÁ ESTÁ A CONDUZIR OUTRO VEÍCULO ────────────────── --}}
    <div x-show="jaConduz" x-cloak
         class="fixed inset-0 z-50 flex items-end justify-center pb-6 px-4"
         style="background:rgba(0,0,0,0.7);">
        <div class="bg-[#1a2540] border border-red-500/40 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="bg-red-500/20 border-b border-red-500/30 px-5 py-4 flex items-center gap-3">
                <span class="text-2xl">🚫</span>
                <div>
                    <div class="text-red-300 font-black text-sm">Já está a conduzir</div>
                    <div class="text-red-200/70 text-xs mt-0.5">Um colaborador não pode conduzir dois veículos</div>
                </div>
            </div>
            <div class="px-5 py-4">
                <p class="text-white/80 text-sm">
                    Já está registado como condutor do <strong class="text-white" x-text="jaConduzMatricula"></strong>
                    desde as <strong class="text-red-300" x-text="jaConduzDesde"></strong>.
                </p>
                <p class="text-white/55 text-xs mt-2">Libere o veículo atual antes de registar condução noutro.</p>
            </div>
            <div class="px-5 pb-5">
                <button @click="jaConduz = false"
                        class="w-full bg-white/8 border border-white/20 text-white/80 font-bold py-2.5 rounded-xl text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>

    {{-- ── EQUIPAMENTOS PENDENTES DE VERIFICAÇÃO ───────────────── --}}
    @if(isset($equipamentosPendentes) && $equipamentosPendentes->count() > 0)
        <div class="px-3.5 pt-4">
            <div class="bg-amber-500/20 border border-amber-500/40 rounded-xl p-3.5 flex flex-col gap-2.5">
                <div class="flex items-center gap-2.5 relative">
                    <div class="bg-amber-400 text-amber-950 w-8 h-8 rounded-full flex items-center justify-center font-black shrink-0">!</div>
                    <div class="flex-1">
                        <h4 class="text-amber-300 text-[0.85rem] font-extrabold m-0">Verificação Mensal Obrigatória</h4>
                        <p class="text-white text-xs m-0 mt-0.5">O equipamento logístico da sua viatura precisa ser verificado hoje.</p>
                    </div>
                    <button wire:click.prevent="dismissAlert('equipamento')" class="bg-transparent border-none text-white/40 text-lg cursor-pointer leading-none p-1" title="Descartar">✕</button>
                </div>

                <div class="bg-black/30 rounded-lg p-2.5 flex flex-col gap-1.5">
                    @foreach($equipamentosPendentes as $eq)
                        <div class="flex justify-between items-center border-b border-white/5 pb-1.5">
                            <div>
                                <span class="text-white text-[0.8rem] font-bold block">{{ $eq['tipo'] }}: {{ $eq['nome'] }}</span>
                                <span class="text-white/90 text-[0.65rem]">Viatura: {{ $eq['veiculo'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button wire:click="$dispatch('open-scanner')"
                    class="bg-amber-400 text-amber-950 font-extrabold py-3 rounded-[10px] border-none w-full flex justify-center items-center gap-2 text-[0.85rem] shadow-[0_4px_15px_rgba(245,158,11,0.3)] cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    ABRIR SCANNER QR
                </button>
            </div>
        </div>
    @endif

    @include('livewire.phone-display._pep-cards')

    <div class="px-3.5 pb-4 flex justify-center">
        <button wire:click="requestPinChange"
            class="bg-white/6 border border-white/15 text-white/50 text-[0.6rem] font-bold uppercase tracking-widest px-4 py-2 rounded-full">
            🔑 Alterar PIN
        </button>
    </div>
</div>
