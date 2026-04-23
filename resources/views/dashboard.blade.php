<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-col lg:flex-row gap-6 w-full items-start" x-data="dragAndDrop()">

        <!-- Panel Izquierdo: PEPs y Áreas Especiales -->
        <div class="w-full lg:w-2/3 flex flex-col gap-6">
            <livewire:pep-board />

            <div class="mt-4">
                <h2 class="text-xl font-bold text-[var(--cme-blue)] mb-2">Outras Áreas / Estados</h2>
                <div class="grid gap-4 md:grid-cols-2 grid-cols-1">
                    <div class="bg-red-50 rounded-xl border border-red-200 shadow p-4 min-h-[150px] flex flex-col items-center justify-center drop-zone" data-pep-id="baja" data-type="estado">
                        <span class="font-semibold text-red-700 mb-2">Baixas / Licenças / Férias</span>
                        <div class="pep-colaboradores-list w-full min-h-[50px] p-2 border border-dashed border-red-300 rounded bg-white flex flex-col gap-1" data-list-type="colaborador">
                             <div class="text-center text-xs text-red-400 py-2 ptr-placeholder">(Largar pessoal aqui)</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-xl border border-blue-200 shadow p-4 min-h-[150px] flex flex-col items-center justify-center drop-zone" data-pep-id="formacion" data-type="estado">
                        <span class="font-semibold text-[var(--cme-blue)] mb-2">Consultas Médicas / Formação</span>
                         <div class="pep-colaboradores-list w-full min-h-[50px] p-2 border border-dashed border-blue-300 rounded bg-white flex flex-col gap-1" data-list-type="colaborador">
                             <div class="text-center text-xs text-blue-400 py-2 ptr-placeholder">(Largar pessoal aqui)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Estaleiro Central (Recursos Disponibles) -->
        <div class="w-full lg:w-1/3 sticky top-4">
            <livewire:recursos-board />
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dragAndDrop', () => ({
                init() {
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                initSortable() {
                    // Configurar listas de origen (Estaleiro)
                    const colabList = document.getElementById('colaboradores-list');
                    const vehisList = document.getElementById('vehiculos-list');

                    if(colabList) {
                        new Sortable(colabList, {
                            group: {
                                name: 'colaboradores',
                                pull: true,
                                put: true
                            },
                            animation: 150,
                            onEnd: (evt) => this.handleDrop(evt)
                        });
                    }

                    if(vehisList) {
                        new Sortable(vehisList, {
                            group: {
                                name: 'vehiculos',
                                pull: true,
                                put: true
                            },
                            animation: 150,
                            onEnd: (evt) => this.handleDrop(evt)
                        });
                    }

                    // Configurar listas de destino (PEPs y Estados)
                    document.querySelectorAll('.pep-colaboradores-list').forEach(el => {
                        new Sortable(el, {
                            group: 'colaboradores',
                            animation: 150,
                            onAdd: function (evt) {
                                // Remover placeholder si se añade un item
                                const placeholder = evt.to.querySelector('.ptr-placeholder');
                                if(placeholder) placeholder.style.display = 'none';
                            },
                            onRemove: function (evt) {
                                // Mostrar placeholder si lista está vacía
                                if(evt.from.children.length === 1 && evt.from.querySelector('.ptr-placeholder')) {
                                    evt.from.querySelector('.ptr-placeholder').style.display = 'block';
                                }
                            }
                        });
                    });

                    document.querySelectorAll('.pep-vehiculos-list').forEach(el => {
                        new Sortable(el, {
                            group: 'vehiculos',
                            animation: 150,
                            onAdd: function (evt) {
                                const placeholder = evt.to.querySelector('.ptr-placeholder');
                                if(placeholder) placeholder.style.display = 'none';
                            },
                            onRemove: function (evt) {
                                if(evt.from.children.length === 1 && evt.from.querySelector('.ptr-placeholder')) {
                                    evt.from.querySelector('.ptr-placeholder').style.display = 'block';
                                }
                            }
                        });
                    });
                },
                handleDrop(evt) {
                    // Logic to send array updates to Livewire goes here
                    console.log('Item moved!');
                }
            }));
        });
    </script>
    @endpush
</x-layouts::app>

