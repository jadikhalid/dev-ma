

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('magazineTicker', () => ({
    opacity: 1,
    translateY: 0,
    pointerEvents: 'auto',

    init() {
        this.update();
    },

    update() {
        const banner = this.$refs.banner;
        if (! banner) {
            return;
        }

        const scrollY = window.scrollY;
        const threshold = window.innerHeight * 0.3;
        const fadeRange = banner.offsetHeight;

        if (scrollY <= threshold) {
            this.opacity = 1;
            this.translateY = 0;
            this.pointerEvents = 'auto';

            return;
        }

        const progress = Math.min(1, (scrollY - threshold) / fadeRange);
        this.opacity = 1 - progress;
        this.translateY = -progress * fadeRange;
        this.pointerEvents = progress < 0.99 ? 'auto' : 'none';
    },

    bannerStyle() {
        return {
            opacity: this.opacity,
            transform: `translateY(${this.translateY}px)`,
            pointerEvents: this.pointerEvents,
        };
    },
}));

Alpine.data('heroSkillAutocomplete', (config) => ({
    url: config.url,
    query: config.initial ?? '',
    loadingLabel: config.loadingLabel,
    emptyLabel: config.emptyLabel,
    suggestions: [],
    open: false,
    loading: false,
    activeIndex: -1,
    debounceTimer: null,

    onInput() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.fetchSuggestions(), 200);
    },

    onFocus() {
        if (this.query.trim()) {
            this.fetchSuggestions();
        }
    },

    async fetchSuggestions() {
        const term = this.query.trim();

        if (! term) {
            this.suggestions = [];
            this.open = false;
            this.activeIndex = -1;

            return;
        }

        this.loading = true;
        this.open = true;

        try {
            const response = await fetch(`${this.url}?q=${encodeURIComponent(term)}`, {
                headers: { Accept: 'application/json' },
            });

            if (! response.ok) {
                throw new Error('suggestions failed');
            }

            const data = await response.json();
            this.suggestions = data.suggestions ?? [];
            this.activeIndex = -1;
        } catch {
            this.suggestions = [];
        } finally {
            this.loading = false;
        }
    },

    select(item) {
        this.query = item.label;
        this.open = false;
        this.suggestions = [];
        this.activeIndex = -1;
        this.$refs.input?.focus();
    },

    close() {
        this.open = false;
        this.activeIndex = -1;
    },

    onKeydown(event) {
        if (event.key === 'Escape') {
            this.close();

            return;
        }

        if (! this.open || ! this.suggestions.length) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.activeIndex = Math.min(this.activeIndex + 1, this.suggestions.length - 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.activeIndex = Math.max(this.activeIndex - 1, 0);
        } else if (event.key === 'Enter' && this.activeIndex >= 0) {
            event.preventDefault();
            this.select(this.suggestions[this.activeIndex]);
        }
    },
}));

Alpine.start();
