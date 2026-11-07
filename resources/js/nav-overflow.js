const MORE_BUTTON_SPACE = 44;

const MENU_ITEM_BASE = 'flex w-full items-center px-4 py-2 text-sm';

function itemIsActive(item) {
    if (item.getAttribute('aria-current') === 'page') {
        return true;
    }

    if (item.classList.contains('border-indigo-400')) {
        return true;
    }

    if (item.classList.contains('text-white')
        && (item.classList.contains('bg-indigo-600') || item.classList.contains('bg-emerald-600'))) {
        return true;
    }

    return false;
}

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

        const always = items.filter((item) => item.hasAttribute('data-nav-overflow-always'));
        const flexible = items.filter((item) => ! item.hasAttribute('data-nav-overflow-always'));

        always.forEach((item) => { item.style.display = 'none'; });

        const available = this.$el.clientWidth;
        const gap = parseFloat(getComputedStyle(row).columnGap) || 0;
        const widths = flexible.map((item) => item.offsetWidth);
        const total = widths.reduce((sum, width) => sum + width, 0) + gap * Math.max(flexible.length - 1, 0);

        let flexibleCut = flexible.length;
        const mustShowMore = always.length > 0 || total > available;
        if (mustShowMore) {
            let used = 0;
            const space = available - MORE_BUTTON_SPACE;
            for (let i = 0; i < flexible.length; i++) {
                const next = used + (i > 0 ? gap : 0) + widths[i];
                if (next > space) {
                    flexibleCut = i;
                    break;
                }
                used = next;
            }
        }

        always.forEach((item) => { item.style.display = ''; });

        const overflowItems = [
            ...flexible.slice(flexibleCut),
            ...always,
        ].sort((a, b) => items.indexOf(a) - items.indexOf(b));

        let hasActive = false;
        overflowItems.forEach((item) => {
            const active = itemIsActive(item);
            const disabled = item.getAttribute('aria-disabled') === 'true';
            hasActive ||= active;

            const clone = item.cloneNode(true);
            clone.className = `${MENU_ITEM_BASE} ${disabled
                ? 'text-gray-300 cursor-not-allowed'
                : active
                    ? 'font-semibold text-indigo-700 bg-indigo-50'
                    : 'text-gray-700 hover:bg-gray-50'}`;
            clone.removeAttribute('data-nav-overflow-always');
            clone.setAttribute('role', 'menuitem');
            menu.appendChild(clone);

            item.style.display = 'none';
        });

        this.overflowCount = overflowItems.length;
        this.hasActiveOverflow = hasActive;
        if (this.overflowCount === 0) {
            this.moreOpen = false;
        }
    },
});
