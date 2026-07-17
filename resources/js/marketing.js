import Alpine from 'alpinejs';

window.Alpine = Alpine;

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function initScrollReveals() {
    const nodes = document.querySelectorAll('[data-mk-reveal]');

    if (!nodes.length) {
        return;
    }

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        nodes.forEach((node) => node.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        },
        {
            threshold: 0.16,
            rootMargin: '0px 0px -8% 0px',
        },
    );

    nodes.forEach((node) => observer.observe(node));
}

function initNavScroll() {
    const nav = document.querySelector('[data-mk-nav]');

    if (!nav) {
        return;
    }

    const update = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 12);
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
}

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

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveals();
    initNavScroll();
});

Alpine.start();
