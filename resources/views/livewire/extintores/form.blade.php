<div>
    <div class="max-w-2xl mx-auto">

        {{-- Header CME --}}
        <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
            <div class="bg-[#09143B] px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="fire" class="text-[#FFD300] w-4 h-4" />
                    <span class="text-white font-medium text-sm">{{ $extintor ? 'Editar Extintor' : 'Novo Extintor' }}</span>
                </div>
                <a href="{{ route('extintores.index') }}" wire:navigate
                   style="color:rgba(255,255,255,0.6); font-size:11px;" class="hover:text-white flex items-center gap-1">
                    ← Voltar
                </a>
            </div>
        </div>

        <form wire:submit="save" style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nº Série --}}
                <div class="md:col-span-2">
                    <label class="cme-label">Número de Série <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="num_serie" placeholder="Ex: ABC-12345" class="cme-input">
                    @error('num_serie') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Tipo Agente --}}
                <div>
                    <label class="cme-label">Tipo de Agente</label>
                    <select wire:model="tipo_agente" class="cme-input">
                        <option value="CO2">CO2 (Dióxido de Carbono)</option>
                        <option value="Pó Químico">Pó Químico (ABC)</option>
                        <option value="Água">Água / Espuma</option>
                        <option value="Halon">Halon / Substitutos</option>
                    </select>
                </div>

                {{-- Tamanho --}}
                <div>
                    <label class="cme-label">Tamanho (Peso)</label>
                    <select wire:model="tamanho" class="cme-input">
                        <option value="2kg">2 kg</option>
                        <option value="5kg">5 kg</option>
                        <option value="6kg">6 kg</option>
                        <option value="9kg">9 kg</option>
                        <option value="12kg">12 kg</option>
                        <option value="50kg">50 kg (Carro)</option>
                    </select>
                </div>

                {{-- Data Verificação --}}
                <div>
                    <label class="cme-label">Data de Verificação</label>
                    <input type="date" wire:model="data_verificacao" class="cme-input">
                </div>

                {{-- Próxima Revisão --}}
                <div>
                    <label class="cme-label">Próxima Revisão</label>
                    <input type="date" wire:model="proxima_revisao" class="cme-input">
                </div>

                {{-- Viatura --}}
                <div class="md:col-span-2">
                    <label class="cme-label">Alocação (Viatura)</label>
                    <select wire:model="veiculo_id" class="cme-input">
                        <option value="">Permanecer em Armazém</option>
                        @foreach($veiculos as $v)
                            <option value="{{ $v->id }}">{{ $v->matricula }} - {{ $v->model }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado --}}
                <div class="md:col-span-2">
                    <label class="cme-label text-center block">Estado de Conservação</label>
                    <div style="background:#E4E2DF; border-radius:8px;" class="flex gap-4 p-2">
                        <label class="flex-1 flex flex-col items-center p-3 cursor-pointer transition-all"
                               style="{{ $estado === 'Conforme' ? 'background:white; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:2px solid #0F6E56; border-radius:8px;' : 'background:transparent; border-radius:8px; border:2px solid transparent;' }}">
                            <input type="radio" wire:model.live="estado" value="Conforme" class="hidden">
                            <span style="{{ $estado === 'Conforme' ? 'color:#0F6E56; font-weight:700; font-size:10px; text-transform:uppercase;' : 'color:#7A7775; font-size:10px; text-transform:uppercase; font-weight:700;' }}">Conforme</span>
                        </label>
                        <label class="flex-1 flex flex-col items-center p-3 cursor-pointer transition-all"
                               style="{{ $estado === 'Não Conforme' ? 'background:white; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:2px solid #A32D2D; border-radius:8px;' : 'background:transparent; border-radius:8px; border:2px solid transparent;' }}">
                            <input type="radio" wire:model.live="estado" value="Não Conforme" class="hidden">
                            <span style="{{ $estado === 'Não Conforme' ? 'color:#A32D2D; font-weight:700; font-size:10px; text-transform:uppercase;' : 'color:#7A7775; font-size:10px; text-transform:uppercase; font-weight:700;' }}">Crítico</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-5 flex justify-end gap-3" style="border-top:1px solid rgba(9,20,59,0.08);">
                <a href="{{ route('extintores.index') }}" wire:navigate class="btn-cme-secondary">Cancelar</a>
                <button type="submit" style="background:#FFD300; color:#09143B; font-weight:700; font-size:11px; padding:10px 28px; border-radius:8px; cursor:pointer; border:none; text-transform:uppercase;">
                    {{ $extintor ? 'Guardar Alterações' : 'Registar Extintor' }}
                </button>
            </div>
        </form>
    </div>
</div>
