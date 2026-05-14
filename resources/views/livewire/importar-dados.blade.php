<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- Header CME com tabs --}}
    <div class="rounded-xl overflow-hidden border border-[rgba(9,20,59,0.16)] mb-6">
        <div class="bg-[#09143B] px-4 py-3 flex items-center gap-2">
            <flux:icon name="arrow-up-tray" class="text-[#FFD300] w-4 h-4" />
            <span class="text-white font-medium text-sm">Importar Dados</span>
        </div>
        <div class="bg-[#0d1a4a] flex border-b border-[rgba(255,255,255,0.06)] px-2">
            <button wire:click="setTab('colaboradores')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $tab === 'colaboradores' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/65 border-transparent hover:text-white/90' }}">
                👷 Colaboradores
            </button>
            <button wire:click="setTab('veiculos')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $tab === 'veiculos' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/65 border-transparent hover:text-white/90' }}">
                🚗 Veículos
            </button>
            <button wire:click="setTab('peps')"
                    class="px-4 py-2.5 text-[11px] font-semibold transition-colors border-b-2 {{ $tab === 'peps' ? 'text-[#FFD300] border-[#FFD300]' : 'text-white/65 border-transparent hover:text-white/90' }}">
                📋 PEPs
            </button>
        </div>
    </div>

    {{-- Alertas --}}
    @if($success)
        <div class="badge-ok px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $successMsg }}
        </div>
    @endif

    @if($hasError)
        <div class="badge-danger px-4 py-3 rounded-xl mb-4 flex items-start gap-2">
            <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ $errorMsg }}</span>
        </div>
    @endif

    {{-- Panel Colaboradores --}}
    @if($tab === 'colaboradores')
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <h2 class="text-base font-bold mb-1" style="color:#09143B;">Importar Colaboradores</h2>
        <p class="text-sm mb-5 cme-muted">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        <div class="mb-5 overflow-x-auto rounded-lg overflow-hidden border border-[rgba(9,20,59,0.12)]">
            <table class="text-xs w-full">
                <thead>
                    <tr style="background:#09143B;">
                        <th class="px-3 py-2 text-left text-white font-semibold">numero_colaborador *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">nome *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">apelido *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">designacao_cargo *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">telefone</th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <tr style="border-top:1px solid rgba(9,20,59,0.08); color:#4A4845;">
                        <td class="px-3 py-2">C-001</td>
                        <td class="px-3 py-2">João</td>
                        <td class="px-3 py-2">Silva</td>
                        <td class="px-3 py-2">Electricista</td>
                        <td class="px-3 py-2">+351 912 000 000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="flex flex-col gap-4">
            <div>
                <label class="cme-label">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroColaboradores" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-[#4A4845] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.18)] rounded-lg bg-white mt-1">
                @error('ficheiroColaboradores') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="importarColaboradores"
                    style="background:#09143B; color:#FFD300; font-weight:700; font-size:12px; padding:8px 24px; border-radius:8px; border:none; cursor:pointer;">
                    <span wire:loading.remove wire:target="importarColaboradores">⬆ Importar</span>
                    <span wire:loading wire:target="importarColaboradores">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'colaboradores') }}" class="btn-cme-secondary">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'colaboradores') }}"
                   style="background:#16a34a; color:#fff; font-weight:700; font-size:12px; padding:8px 20px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;"
                   class="hover:opacity-90 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    {{-- Panel Veículos --}}
    @if($tab === 'veiculos')
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <h2 class="text-base font-bold mb-1" style="color:#09143B;">Importar Veículos</h2>
        <p class="text-sm mb-5 cme-muted">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        <div class="mb-5 overflow-x-auto rounded-lg overflow-hidden border border-[rgba(9,20,59,0.12)]">
            <table class="text-xs w-full">
                <thead>
                    <tr style="background:#09143B;">
                        <th class="px-3 py-2 text-left text-white font-semibold">matricula *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">marca *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">modelo *</th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <tr style="border-top:1px solid rgba(9,20,59,0.08); color:#4A4845;">
                        <td class="px-3 py-2">MA-00-AA</td>
                        <td class="px-3 py-2">Ford</td>
                        <td class="px-3 py-2">Transit</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="flex flex-col gap-4">
            <div>
                <label class="cme-label">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroVeiculos" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-[#4A4845] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.18)] rounded-lg bg-white mt-1">
                @error('ficheiroVeiculos') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="importarVeiculos"
                    style="background:#09143B; color:#FFD300; font-weight:700; font-size:12px; padding:8px 24px; border-radius:8px; border:none; cursor:pointer;">
                    <span wire:loading.remove wire:target="importarVeiculos">⬆ Importar</span>
                    <span wire:loading wire:target="importarVeiculos">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'veiculos') }}" class="btn-cme-secondary">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'veiculos') }}"
                   style="background:#16a34a; color:#fff; font-weight:700; font-size:12px; padding:8px 20px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;"
                   class="hover:opacity-90 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    {{-- Panel PEPs --}}
    @if($tab === 'peps')
    <div style="background:#F0EEEB; border:1px solid rgba(9,20,59,0.14);" class="rounded-xl p-6">
        <h2 class="text-base font-bold mb-1" style="color:#09143B;">Importar PEPs</h2>
        <p class="text-sm mb-5 cme-muted">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        <div class="mb-5 overflow-x-auto rounded-lg overflow-hidden border border-[rgba(9,20,59,0.12)]">
            <table class="text-xs w-full">
                <thead>
                    <tr style="background:#09143B;">
                        <th class="px-3 py-2 text-left text-white font-semibold">nome *</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">localizacao</th>
                        <th class="px-3 py-2 text-left text-white font-semibold">tipo_trabalho</th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <tr style="border-top:1px solid rgba(9,20,59,0.08); color:#4A4845;">
                        <td class="px-3 py-2">P.016.047/001</td>
                        <td class="px-3 py-2">Funchal</td>
                        <td class="px-3 py-2">BT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs mb-5 cme-muted">
            As colunas <strong>localizacao</strong> e <strong>tipo_trabalho</strong> são criadas automaticamente se não existirem.
        </p>

        <form class="flex flex-col gap-4">
            <div>
                <label class="cme-label">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroPeps" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-[#4A4845] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:uppercase file:bg-[#09143B] file:text-[#FFD300] hover:file:opacity-90 cursor-pointer border border-[rgba(9,20,59,0.18)] rounded-lg bg-white mt-1">
                @error('ficheiroPeps') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" wire:click="importarPeps"
                    style="background:#09143B; color:#FFD300; font-weight:700; font-size:12px; padding:8px 24px; border-radius:8px; border:none; cursor:pointer;">
                    <span wire:loading.remove wire:target="importarPeps">⬆ Importar</span>
                    <span wire:loading wire:target="importarPeps">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'peps') }}" class="btn-cme-secondary">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'peps') }}"
                   style="background:#16a34a; color:#fff; font-weight:700; font-size:12px; padding:8px 20px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;"
                   class="hover:opacity-90 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

</div>
