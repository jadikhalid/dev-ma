

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
    _dragStartScroll: 0,
    _canUseArrows: false,
    _marqueeOriginals: null,
    arrowHoldDirection: 0,
    arrowScrollSpeed: 240,

    marqueeScrollPx: 0,
    marqueeCenterBase: 0,
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
                this.marqueeScrollPx -= this.arrowHoldDirection * this.arrowScrollSpeed * delta;

                return;
            }

            if (this.isDragging) {
                return;
            }

            if (this.isBannerHovered) {
                return;
            }

            this.marqueeScrollPx += this.marqueeSpeed * delta;
        };

        this._autoScrollFrame = requestAnimationFrame(tick);
    },

    setupMarquee(reset = false) {
        const setA = this.$refs.marqueeSetA;
        const setB = this.$refs.marqueeSetB;
        const leadSpacer = this.$refs.marqueeLeadSpacer;
        const container = this.$refs.marqueeViewport;

        if (! setA || ! setB || ! leadSpacer || ! container) {
            return;
        }

        if (! this._marqueeOriginals?.length) {
            this._marqueeOriginals = Array.from(setA.children).map((item) => item.cloneNode(true));
        }

        const originals = this._marqueeOriginals;
        const containerWidth = container.offsetWidth;

        if (! originals.length || ! containerWidth) {
            return;
        }

        const appendCycle = (markNewest = false) => {
            originals.forEach((template, index) => {
                const clone = template.cloneNode(true);

                if (markNewest && index === 0) {
                    clone.dataset.newest = '1';
                }

                setA.appendChild(clone);
            });
        };

        setA.innerHTML = '';
        leadSpacer.style.width = '0px';

        const widthProbe = originals[0].cloneNode(true);
        setA.appendChild(widthProbe);
        const firstItemWidth = widthProbe.offsetWidth;
        setA.removeChild(widthProbe);

        const targetPrepend = Math.max(0, (containerWidth - firstItemWidth) / 2);
        let prependWidth = 0;
        let safety = 0;

        if (originals.length > 1) {
            let sourceIndex = originals.length - 1;

            while (prependWidth < targetPrepend && safety < 48) {
                const clone = originals[sourceIndex].cloneNode(true);
                setA.insertBefore(clone, setA.firstChild);
                prependWidth += clone.offsetWidth;
                safety++;

                sourceIndex--;

                if (sourceIndex < 1) {
                    sourceIndex = originals.length - 1;
                }
            }
        }

        appendCycle(true);

        const newest = setA.querySelector('[data-newest="1"]');

        if (! newest) {
            return;
        }

        let cycleWidth = 0;
        let cycleNode = newest;

        for (let index = 0; index < originals.length; index++) {
            if (! cycleNode) {
                break;
            }

            cycleWidth += cycleNode.offsetWidth;
            cycleNode = cycleNode.nextElementSibling;
        }

        if (! cycleWidth) {
            return;
        }

        const newestPosition = newest.offsetLeft;

        while (setA.scrollWidth < newestPosition + cycleWidth + containerWidth + 64 && safety < 80) {
            appendCycle(false);
            safety++;
        }

        setB.innerHTML = setA.innerHTML;

        let centerBase = newestPosition + firstItemWidth / 2 - containerWidth / 2;

        if (centerBase < 0) {
            leadSpacer.style.width = `${Math.ceil(-centerBase)}px`;
            centerBase = leadSpacer.offsetWidth + newestPosition + firstItemWidth / 2 - containerWidth / 2;
        }

        this.marqueeDistance = cycleWidth;
        this.marqueeCenterBase = centerBase;

        if (! reset || ! this.isDragging) {
            this.marqueeScrollPx = 0;
        } else if (this.marqueeDistance > 0) {
            this.marqueeScrollPx = ((this.marqueeScrollPx % this.marqueeDistance) + this.marqueeDistance) % this.marqueeDistance;
        }

        setA.querySelectorAll('img').forEach((img) => {
            if (! img.complete) {
                img.addEventListener('load', () => this.setupMarquee(true), { once: true });
            }
        });
    },

    marqueeTrackStyle() {
        const distance = this.marqueeDistance;

        if (! distance) {
            return {
                transform: 'translate3d(0px, 0, 0)',
            };
        }

        const looped = ((this.marqueeScrollPx % distance) + distance) % distance;

        return {
            transform: `translate3d(${-(this.marqueeCenterBase + looped)}px, 0, 0)`,
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
        this._dragStartScroll = this.marqueeScrollPx;
        this._dragStartX = event.clientX;
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

        this.marqueeScrollPx = this._dragStartScroll - delta;
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

Alpine.data('socialPostsSlider', (config) => ({
    items: config.items ?? [],
    prevLabel: config.prevLabel ?? '',
    nextLabel: config.nextLabel ?? '',
    startIndex: 0,
    visibleCount: 3,
    cardWidthPx: 0,
    stepPx: 0,
    gapPx: 20,
    _resizeTimer: null,

    init() {
        this.$nextTick(() => this.updateLayout());

        window.addEventListener('resize', () => {
            clearTimeout(this._resizeTimer);
            this._resizeTimer = setTimeout(() => this.updateLayout(), 150);
        });
    },

    updateLayout() {
        const width = window.innerWidth;

        if (width >= 1024) {
            this.visibleCount = 3;
        } else if (width >= 640) {
            this.visibleCount = 2;
        } else {
            this.visibleCount = 1;
        }

        this.startIndex = Math.min(this.startIndex, this.maxIndex);
        this.$nextTick(() => this.measure());
    },

    measure() {
        const viewport = this.$refs.viewport;

        if (! viewport) {
            return;
        }

        const gaps = (this.visibleCount - 1) * this.gapPx;
        this.cardWidthPx = (viewport.offsetWidth - gaps) / this.visibleCount;
        this.stepPx = this.cardWidthPx + this.gapPx;
    },

    get maxIndex() {
        return Math.max(0, this.items.length - this.visibleCount);
    },

    get canPrev() {
        return this.startIndex > 0;
    },

    get canNext() {
        return this.startIndex < this.maxIndex;
    },

    prev() {
        if (this.canPrev) {
            this.startIndex--;
        }
    },

    next() {
        if (this.canNext) {
            this.startIndex++;
        }
    },

    cardStyle() {
        if (! this.cardWidthPx) {
            return {};
        }

        return {
            width: `${this.cardWidthPx}px`,
        };
    },

    trackStyle() {
        return {
            transform: `translate3d(-${this.startIndex * this.stepPx}px, 0, 0)`,
        };
    },
}));

Alpine.data('heroProgressiveSearch', (config) => ({
    sectors: config.sectors ?? [],
    sectorSlug: config.initialSector ?? '',
    professionSlug: config.initialProfession ?? '',
    query: config.initialKeyword ?? '',
    specializationAllLabel: config.specializationAllLabel ?? '',
    specializationSelectProfessionLabel: config.specializationSelectProfessionLabel ?? '',
    titleInputId: config.titleInputId ?? null,

    get filteredProfessions() {
        if (! this.sectorSlug) {
            return this.sectors.flatMap((sector) => sector.professions ?? []);
        }

        const sector = this.sectors.find((item) => item.slug === this.sectorSlug);

        return sector?.professions ?? [];
    },

    get selectedProfession() {
        return this.filteredProfessions.find((item) => item.slug === this.professionSlug)
            ?? this.sectors
                .flatMap((sector) => sector.professions ?? [])
                .find((item) => item.slug === this.professionSlug)
            ?? null;
    },

    get filteredSpecializations() {
        if (! this.professionSlug) {
            if (! this.sectorSlug) {
                return [];
            }

            return this.filteredProfessions.flatMap((profession) => profession.specializations ?? []);
        }

        return this.selectedProfession?.specializations ?? [];
    },

    get specializationPlaceholder() {
        if (! this.professionSlug && ! this.sectorSlug) {
            return this.specializationSelectProfessionLabel;
        }

        return this.specializationAllLabel;
    },

    onSectorChange() {
        const isValidProfession = this.filteredProfessions.some(
            (profession) => profession.slug === this.professionSlug
        );

        if (! isValidProfession) {
            this.professionSlug = '';
        }

        this.resetKeywordIfInvalid();
    },

    onProfessionChange() {
        this.resetKeywordIfInvalid();
    },

    resetKeywordIfInvalid() {
        if (! this.query) {
            return;
        }

        if (! this.filteredSpecializations.includes(this.query)) {
            this.query = '';
        }
    },

    suggestTitle() {
        if (! this.titleInputId || ! this.query) {
            return;
        }

        const titleInput = document.getElementById(this.titleInputId);

        if (! titleInput || titleInput.value.trim()) {
            return;
        }

        titleInput.value = this.query;
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

Alpine.data('toastStack', (initialToasts = []) => ({
    toasts: [],

    init() {
        initialToasts.forEach((toast) => {
            this.push(toast.type, toast.message);
        });
    },

    push(type, message) {
        const id = `${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

        this.toasts.push({
            id,
            type,
            message,
            visible: true,
        });

        window.setTimeout(() => this.dismiss(id), type === 'success' ? 7000 : 9000);
    },

    dismiss(id) {
        const toast = this.toasts.find((item) => item.id === id);

        if (! toast) {
            return;
        }

        toast.visible = false;

        window.setTimeout(() => {
            this.toasts = this.toasts.filter((item) => item.id !== id);
        }, 300);
    },
}));

Alpine.start();
