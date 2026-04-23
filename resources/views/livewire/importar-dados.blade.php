<div class="max-w-3xl mx-auto py-8 px-4">

    <h1 class="text-2xl font-bold uppercase text-yellow-600 mb-6">Importar Dados</h1>

    {{-- Alertas --}}
    @if($success)
        <div class="mb-4 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $successMsg }}
        </div>
    @endif

    @if($hasError)
        <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg flex items-start gap-2">
            <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ $errorMsg }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-gray-200 p-1 rounded-xl w-fit">
        <button wire:click="setTab('colaboradores')"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
            style="{{ $tab === 'colaboradores' ? 'background-color:#09143b; color:#ffd300; box-shadow:0 2px 8px rgba(9,20,59,0.35);' : 'background:transparent; color:#6b7280;' }}">
            👷 Colaboradores
        </button>
        <button wire:click="setTab('veiculos')"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
            style="{{ $tab === 'veiculos' ? 'background-color:#09143b; color:#ffd300; box-shadow:0 2px 8px rgba(9,20,59,0.35);' : 'background:transparent; color:#6b7280;' }}">
            🚗 Veículos
        </button>
        <button wire:click="setTab('peps')"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
            style="{{ $tab === 'peps' ? 'background-color:#09143b; color:#ffd300; box-shadow:0 2px 8px rgba(9,20,59,0.35);' : 'background:transparent; color:#6b7280;' }}">
            📋 PEPs
        </button>
    </div>

    {{-- Panel Colaboradores --}}
    @if($tab === 'colaboradores')
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <h2 class="text-lg font-bold text-(--cme-blue) mb-1">Importar Colaboradores</h2>
        <p class="text-sm text-gray-500 mb-5">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        {{-- Columns reference --}}
        <div class="mb-5 overflow-x-auto">
            <table class="text-xs w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-(--cme-blue) text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">numero_colaborador *</th>
                        <th class="px-3 py-2 text-left">nome *</th>
                        <th class="px-3 py-2 text-left">apelido *</th>
                        <th class="px-3 py-2 text-left">designacao_cargo *</th>
                        <th class="px-3 py-2 text-left">telefone</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50">
                    <tr class="border-t border-gray-200 text-gray-600">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroColaboradores" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-(--cme-blue) file:text-white hover:file:bg-blue-800 file:cursor-pointer cursor-pointer">
                @error('ficheiroColaboradores') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="button" wire:click="importarColaboradores"
                    class="px-6 py-2 bg-(--cme-blue) text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                    <span wire:loading.remove wire:target="importarColaboradores">⬆ Importar</span>
                    <span wire:loading wire:target="importarColaboradores">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'colaboradores') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'colaboradores') }}"
                   class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold shadow-md border-2 transition-all hover:opacity-90 hover:shadow-lg"
                   style="background-color:#16a34a; border-color:#15803d; color:#fff;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    {{-- Panel Veículos --}}
    @if($tab === 'veiculos')
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <h2 class="text-lg font-bold text-(--cme-blue) mb-1">Importar Veículos</h2>
        <p class="text-sm text-gray-500 mb-5">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        <div class="mb-5 overflow-x-auto">
            <table class="text-xs w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-(--cme-blue) text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">matricula *</th>
                        <th class="px-3 py-2 text-left">marca *</th>
                        <th class="px-3 py-2 text-left">modelo *</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50">
                    <tr class="border-t border-gray-200 text-gray-600">
                        <td class="px-3 py-2">MA-00-AA</td>
                        <td class="px-3 py-2">Ford</td>
                        <td class="px-3 py-2">Transit</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="flex flex-col gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroVeiculos" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-(--cme-blue) file:text-white hover:file:bg-blue-800 file:cursor-pointer cursor-pointer">
                @error('ficheiroVeiculos') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="button" wire:click="importarVeiculos"
                    class="px-6 py-2 bg-(--cme-blue) text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                    <span wire:loading.remove wire:target="importarVeiculos">⬆ Importar</span>
                    <span wire:loading wire:target="importarVeiculos">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'veiculos') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'veiculos') }}"
                   class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold shadow-md border-2 transition-all hover:opacity-90 hover:shadow-lg"
                   style="background-color:#16a34a; border-color:#15803d; color:#fff;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    {{-- Panel PEPs --}}
    @if($tab === 'peps')
    <div class="bg-white rounded-xl border border-gray-200 shadow p-6">
        <h2 class="text-lg font-bold text-(--cme-blue) mb-1">Importar PEPs</h2>
        <p class="text-sm text-gray-500 mb-5">
            O ficheiro Excel deve ter as seguintes colunas na primeira linha (cabeçalho):
        </p>

        <div class="mb-5 overflow-x-auto">
            <table class="text-xs w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-(--cme-blue) text-white">
                    <tr>
                        <th class="px-3 py-2 text-left">nome *</th>
                        <th class="px-3 py-2 text-left">localizacao</th>
                        <th class="px-3 py-2 text-left">tipo_trabalho</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50">
                    <tr class="border-t border-gray-200 text-gray-600">
                        <td class="px-3 py-2">P.016.047/001</td>
                        <td class="px-3 py-2">Funchal</td>
                        <td class="px-3 py-2">BT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mb-5">
            As colunas <strong>locacion</strong> e <strong>tipo_trabalho</strong> são criadas automaticamente se não existirem.
        </p>

        <form class="flex flex-col gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ficheiro Excel (.xlsx, .xls, .csv)</label>
                <input type="file" wire:model="ficheiroPeps" accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-(--cme-blue) file:text-white hover:file:bg-blue-800 file:cursor-pointer cursor-pointer">
                @error('ficheiroPeps') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="button" wire:click="importarPeps"
                    class="px-6 py-2 bg-(--cme-blue) text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                    <span wire:loading.remove wire:target="importarPeps">⬆ Importar</span>
                    <span wire:loading wire:target="importarPeps">A importar...</span>
                </button>
                <a href="{{ route('importar.plantilla', 'peps') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    ⬇ Modelo
                </a>
                <a href="{{ route('exportar', 'peps') }}"
                   class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold shadow-md border-2 transition-all hover:opacity-90 hover:shadow-lg"
                   style="background-color:#16a34a; border-color:#15803d; color:#fff;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>
    @endif

</div>
