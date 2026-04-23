<div class="bg-white rounded-xl border border-(--cme-gray-medium) shadow p-5 flex flex-col gap-4">
    <h2 class="text-xl font-bold text-(--cme-blue) flex justify-between items-center pb-2 border-b border-gray-200">
        <span>Estaleiro (Disponíveis)</span>
        <input type="date" wire:model.live="data" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-(--cme-yellow) focus:border-(--cme-yellow) text-gray-700" style="color-scheme:light;">
    </h2>

    <div class="grid grid-cols-2 gap-4">
        <!-- Coluna de Colaboradores -->
        <div>
            <h3 class="font-semibold text-(--cme-blue) mb-2 bg-gray-50 p-2 rounded text-center">Colaboradores</h3>
            <div id="colaboradores-list" class="flex flex-col gap-2 min-h-[300px] max-h-[500px] overflow-y-auto pr-1 pb-10 bg-gray-50/50 rounded" data-drag-type="colaborador">
                @forelse ($colaboradores_livres as $colaborador)
                    <div class="p-2 border border-gray-200 rounded shadow-sm bg-white cursor-grab hover:border-(--cme-yellow) transition-colors draggable-item" data-id="{{ $colaborador->id }}" data-type="colaborador">
                        <div class="font-medium text-sm text-(--cme-blue)">{{ $colaborador->numero_colaborador }} - {{ $colaborador->nombre }} {{ $colaborador->apellido }}</div>
                        <div class="text-xs text-gray-500">{{ $colaborador->denominacion_cargo }}</div>
                    </div>
                @empty
                    <div class="text-center text-sm text-gray-400 italic py-4 empty-msg">Nenhum colaborador disponível</div>
                @endforelse
            </div>
        </div>

        <!-- Coluna de Veículos -->
        <div>
             <h3 class="font-semibold text-(--cme-blue) mb-2 bg-gray-50 p-2 rounded text-center">Veículos</h3>
             <div id="veiculos-list" class="flex flex-col gap-2 min-h-[300px] max-h-[500px] overflow-y-auto pr-1 pb-10 bg-gray-50/50 rounded" data-drag-type="veiculo">
                 @forelse ($veiculos_livres as $veiculo)
                    <div class="p-2 border border-gray-200 rounded shadow-sm bg-white cursor-grab hover:border-(--cme-yellow) transition-colors draggable-item" data-id="{{ $veiculo->id }}" data-type="veiculo">
                        <div class="font-medium text-sm text-(--cme-blue) flex items-center justify-between">
                            {{ $veiculo->matricula }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-xs text-gray-500">{{ $veiculo->marca }} {{ $veiculo->modelo }}</div>
                    </div>
                 @empty
                    <div class="text-center text-sm text-gray-400 italic py-4 empty-msg">Nenhum veículo disponível</div>
                 @endforelse
             </div>
        </div>
    </div>
</div>
