import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-tomselect]')?.forEach(el => {
        if (!el.tomselect) {
                new TomSelect(el, {
                    create: false,
                    allowEmptyOption: true,
                    sortField: 'text',
                    placeholder: el.getAttribute('placeholder') || '',
                    highlight: true
                });
        }
    });
});

document.addEventListener('livewire:initialized', () => {
    document.querySelectorAll('[data-tomselect]')?.forEach(el => {
        if (!el.tomselect) {
                new TomSelect(el, {
                    create: false,
                    allowEmptyOption: true,
                    sortField: 'text',
                    placeholder: el.getAttribute('placeholder') || '',
                    highlight: true
                });
        }
    });
});
