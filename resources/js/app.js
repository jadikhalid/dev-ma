

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('magazineTicker', () => ({
    opacity: 1,
    translateY: 0,
    pointerEvents: 'auto',
    _marqueeResizeTimer: null,
    _autoScrollFrame: null,
    _lastAutoScrollTime: null,
    _dragMoved: false,
    _dragStartX: 0,
    _dragStartOffset: 0,
    _canUseArrows: false,
    arrowHoldDirection: 0,
    arrowScrollSpeed: 240,

    marqueeOffset: 0,
    marqueeStart: 0,
    marqueeDistance: 0,
    marqueeSpeed: 72,

    isBannerHovered: false,
    isDragging: false,

    init() {
        this._canUseArrows = window.matchMedia('(min-width: 1024px)').matches;

        const arrowMedia = window.matchMedia('(min-width: 1024px)');
        arrowMedia.addEventListener('change', (event) => {
            this._canUseArrows = event.matches;

            if (! event.matches) {
                this.isBannerHovered = false;
            }
        });

        this.$nextTick(() => {
            this.setupMarquee();
            this.update();
            this.startAutoScroll();
        });
    },

    onResize() {
        clearTimeout(this._marqueeResizeTimer);
        this._marqueeResizeTimer = setTimeout(() => this.setupMarquee(true), 200);
    },

    startAutoScroll() {
        if (this._autoScrollFrame) {
            cancelAnimationFrame(this._autoScrollFrame);
        }

        this._lastAutoScrollTime = null;

        const tick = (time) => {
            this._autoScrollFrame = requestAnimationFrame(tick);

            if (this._lastAutoScrollTime === null) {
                this._lastAutoScrollTime = time;

                return;
            }

            const delta = (time - this._lastAutoScrollTime) / 1000;
            this._lastAutoScrollTime = time;

            if (this.arrowHoldDirection !== 0) {
                this.marqueeOffset += this.arrowHoldDirection * this.arrowScrollSpeed * delta;
                this.normalizeOffset();

                return;
            }

            if (this.isDragging) {
                return;
            }

            if (this.isBannerHovered) {
                return;
            }

            this.marqueeOffset -= this.marqueeSpeed * delta;
            this.normalizeOffset();
        };

        this._autoScrollFrame = requestAnimationFrame(tick);
    },

    setupMarquee(reset = false) {
        const track = this.$refs.marqueeTrack;
        const setA = this.$refs.marqueeSetA;
        const setB = this.$refs.marqueeSetB;
        const container = this.$refs.marqueeViewport;

        if (! track || ! setA || ! setB || ! container) {
            return;
        }

        const initialCount = Number.parseInt(setA.dataset.initialCount ?? '0', 10);

        if (reset && initialCount > 0) {
            while (setA.children.length > initialCount) {
                setA.lastElementChild?.remove();
            }

            setB.innerHTML = '';
        }

        const templateItems = Array.from(setA.children);

        if (! templateItems.length) {
            return;
        }

        const minSetWidth = container.offsetWidth + 64;
        let safety = 0;

        while (setA.scrollWidth < minSetWidth && safety < 40) {
            templateItems.forEach((item) => {
                setA.appendChild(item.cloneNode(true));
            });
            safety++;
        }

        setB.innerHTML = setA.innerHTML;

        const distance = setA.offsetWidth;
        let originalWidth = 0;

        for (let i = 0; i < Math.min(initialCount, setA.children.length); i++) {
            originalWidth += setA.children[i].offsetWidth;
        }

        const initialOffset = Math.max(0, originalWidth - container.offsetWidth);

        this.marqueeStart = -initialOffset;
        this.marqueeDistance = distance;

        if (! reset || ! this.isDragging) {
            this.marqueeOffset = this.marqueeStart;
        }

        this.normalizeOffset();

        setA.querySelectorAll('img').forEach((img) => {
            if (! img.complete) {
                img.addEventListener('load', () => this.setupMarquee(true), { once: true });
            }
        });
    },

    normalizeOffset() {
        if (! this.marqueeDistance) {
            return;
        }

        const end = this.marqueeStart - this.marqueeDistance;

        while (this.marqueeOffset <= end) {
            this.marqueeOffset += this.marqueeDistance;
        }

        while (this.marqueeOffset > this.marqueeStart) {
            this.marqueeOffset -= this.marqueeDistance;
        }
    },

    marqueeTrackStyle() {
        return {
            transform: `translate3d(${this.marqueeOffset}px, 0, 0)`,
        };
    },

    onBannerEnter() {
        this.isBannerHovered = true;
    },

    onBannerLeave() {
        this.isBannerHovered = false;
        this.stopArrowScroll();
    },

    onArrowPointerDown(direction, event) {
        event.preventDefault();

        this.arrowHoldDirection = direction;

        const onRelease = () => {
            this.stopArrowScroll();
            window.removeEventListener('pointerup', onRelease);
            window.removeEventListener('pointercancel', onRelease);
        };

        window.addEventListener('pointerup', onRelease);
        window.addEventListener('pointercancel', onRelease);
    },

    stopArrowScroll() {
        this.arrowHoldDirection = 0;
    },

    onPointerDown(event) {
        if (this._canUseArrows && event.pointerType === 'mouse') {
            return;
        }

        this.isDragging = true;
        this._dragMoved = false;
        this._dragStartX = event.clientX;
        this._dragStartOffset = this.marqueeOffset;
        event.currentTarget.setPointerCapture(event.pointerId);
    },

    onPointerMove(event) {
        if (! this.isDragging) {
            return;
        }

        const delta = event.clientX - this._dragStartX;

        if (Math.abs(delta) > 6) {
            this._dragMoved = true;
        }

        this.marqueeOffset = this._dragStartOffset + delta;
        this.normalizeOffset();
    },

    onPointerUp(event) {
        if (! this.isDragging) {
            return;
        }

        this.isDragging = false;

        if (event.currentTarget.hasPointerCapture?.(event.pointerId)) {
            event.currentTarget.releasePointerCapture(event.pointerId);
        }
    },

    onMarqueeClick(event) {
        if (this._dragMoved) {
            event.preventDefault();
            event.stopPropagation();
            this._dragMoved = false;
        }
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

function initMobileLocaleFromIp() {
    if (! document.body.dataset.mobileLocaleAuto) {
        return;
    }

    const isMobile = window.matchMedia('(max-width: 1023px)').matches;

    if (! isMobile) {
        return;
    }

    if (sessionStorage.getItem('talenma_mobile_locale_checked')) {
        return;
    }

    sessionStorage.setItem('talenma_mobile_locale_checked', '1');

    const url = document.body.dataset.localeSuggestUrl;

    if (! url) {
        return;
    }

    fetch(url, { headers: { Accept: 'application/json' } })
        .then((response) => (response.ok ? response.json() : Promise.reject()))
        .then(({ locale, current }) => {
            if (locale && locale !== current) {
                window.location.href = `/locale/${locale}?manual=0`;
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', initMobileLocaleFromIp);

Alpine.start();
