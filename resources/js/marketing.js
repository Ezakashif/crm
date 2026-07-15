import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('marketingNav', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
            document.body.classList.toggle('overflow-hidden', this.open);
        },
        close() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        },
    }));

    Alpine.data('faqAccordion', (initial = null) => ({
        active: initial,
        toggle(id) {
            this.active = this.active === id ? null : id;
        },
        isOpen(id) {
            return this.active === id;
        },
    }));

    Alpine.data('pricingToggle', (initial = 'monthly') => ({
        billing: initial,
        setBilling(value) {
            this.billing = value;
        },
        isAnnual() {
            return this.billing === 'annual';
        },
    }));
});

Alpine.start();
