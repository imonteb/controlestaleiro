import Sortable from 'sortablejs';

window.Sortable = Sortable;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('dragAndDrop', () => ({
        init() {
            console.log("Alpine init()");
            setTimeout(() => {
                this.initSortable();
            }, 500); // Increased timeout to ensure Livewire DOM is ready

            // Re-init Sortable anytime Livewire updates the DOM
            document.addEventListener('livewire:navigated', () => {
                console.log("livewire:navigated");
                setTimeout(() => {
                    this.initSortable();
                }, 100);
            });

            document.addEventListener('livewire:initialized', () => {
                window.Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        setTimeout(() => {
                            this.initSortable();
                        }, 50);
                    });
                });
            });
        },
        initSortable() {
            const lists = document.querySelectorAll('#colaboradores-list, #veiculos-list, .pep-colaboradores-list, .pep-veiculos-list');

            if (lists.length === 0) {
                console.log("Nenhuma lista encontrada para inicializar SortableJS.");
            } else {
                console.log("Inicializando Sortable em " + lists.length + " listas");
            }

            lists.forEach(el => {
                if (el.sortable) {
                    el.sortable.destroy();
                }

                let isColab = el.dataset.listType === 'colaborador';

                el.sortable = new Sortable(el, {
                    group: {
                        name: isColab ? 'colaboradores' : 'veiculos',
                        pull: true,
                        put: isColab ? ['colaboradores'] : ['veiculos']
                    },
                    animation: 150,
                    delay: 0,
                    forceFallback: false, // Disabled forceFallback to fix general dragging feeling on desktop
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    ghostClass: 'bg-yellow-100',
                    chosenClass: 'opacity-50',
                    dragClass: 'opacity-100',
                    onEnd: (evt) => {
                        this.handleDrop(evt);
                    },
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
            const item = evt.item;
            const toList = evt.to;
            const fromList = evt.from;

            if(toList === fromList) return;

            const recursoId = item.dataset.id;
            const recursoType = item.dataset.type;

            const dropZone = toList.closest('.drop-zone');
            if(!dropZone) {
                console.error("Dropzone não encontrada.");
                return;
            }

            const destType = dropZone.dataset.type;
            const destId = dropZone.dataset.pepId;

            // Nuevos parámetros de rol de equipo sacados de la lista donde cayó
            const equipoTipo = toList.dataset.equipoTipo || 'principal';
            const esJefe = toList.dataset.esJefe === 'true';

            console.log(`Movendo ${recursoType} ${recursoId} para ${destType} ${destId} (${equipoTipo}, chefe: ${esJefe})`);

            // Trigger the Livewire component method
            if (this.$wire) {
                this.$wire.atribuirRecurso(recursoType, recursoId, destType, destId, equipoTipo, esJefe);
            } else {
                console.error("Componente Livewire ($wire) não encontrado neste contexto Alpine.");
            }
        }
    }));
});

