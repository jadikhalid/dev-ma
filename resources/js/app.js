

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

Alpine.start();
