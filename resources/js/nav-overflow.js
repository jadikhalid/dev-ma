const MORE_BUTTON_SPACE = 44;

const MENU_ITEM_BASE = 'flex w-full items-center px-4 py-2 text-sm';

export default () => ({
    overflowCount: 0,
    hasActiveOverflow: false,
    moreOpen: false,

    init() {
        this.update = this.update.bind(this);
        this.$nextTick(this.update);
        this.observer = new ResizeObserver(() => window.requestAnimationFrame(this.update));
        this.observer.observe(this.$el);
        document.fonts?.ready.then(this.update);
    },

    destroy() {
        this.observer?.disconnect();
    },

    update() {
        const row = this.$refs.row;
        const menu = this.$refs.menu;
        if (! row || ! menu) {
            return;
        }

        const items = Array.from(row.children);
        items.forEach((item) => { item.style.display = ''; });
        menu.replaceChildren();

        const available = this.$el.clientWidth;
        const gap = parseFloat(getComputedStyle(row).columnGap) || 0;
        const widths = items.map((item) => item.offsetWidth);
        const total = widths.reduce((sum, width) => sum + width, 0) + gap * Math.max(items.length - 1, 0);

        let cut = items.length;
        if (total > available) {
            let used = 0;
            for (let i = 0; i < items.length; i++) {
                const next = used + (i > 0 ? gap : 0) + widths[i];
                if (next > available - MORE_BUTTON_SPACE) {
                    cut = i;
                    break;
                }
                used = next;
            }
        }

        let hasActive = false;
        items.slice(cut).forEach((item) => {
            const active = item.classList.contains('border-indigo-400');
            const disabled = item.getAttribute('aria-disabled') === 'true';
            hasActive ||= active;

            const clone = item.cloneNode(true);
            clone.className = `${MENU_ITEM_BASE} ${disabled
                ? 'text-gray-300 cursor-not-allowed'
                : active
                    ? 'font-semibold text-indigo-700 bg-indigo-50'
                    : 'text-gray-700 hover:bg-gray-50'}`;
            clone.setAttribute('role', 'menuitem');
            menu.appendChild(clone);

            item.style.display = 'none';
        });

        this.overflowCount = items.length - cut;
        this.hasActiveOverflow = hasActive;
        if (this.overflowCount === 0) {
            this.moreOpen = false;
        }
    },
});
