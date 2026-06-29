

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
        const track = this.$refs.marqueeTrack;
        const setA = this.$refs.marqueeSetA;
        const setB = this.$refs.marqueeSetB;
        const container = this.$refs.marqueeViewport;

        if (! track || ! setA || ! setB || ! container) {
            return;
        }

        if (! this._marqueeOriginals?.length) {
            this._marqueeOriginals = Array.from(setA.children).map((item) => item.cloneNode(true));
        }

        setA.innerHTML = '';
        this._marqueeOriginals.forEach((item) => {
            setA.appendChild(item.cloneNode(true));
        });

        const originals = Array.from(setA.children);

        if (! originals.length) {
            return;
        }

        const containerWidth = container.offsetWidth;
        const firstItemWidth = originals[0].offsetWidth;
        let cycleWidth = 0;

        originals.forEach((item) => {
            cycleWidth += item.offsetWidth;
        });

        const targetPrepend = Math.max(0, (containerWidth - firstItemWidth) / 2);
        let prependWidth = 0;
        let safety = 0;

        if (originals.length > 1) {
            let sourceIndex = originals.length - 1;

            while (prependWidth < targetPrepend && safety < 40) {
                const clone = originals[sourceIndex].cloneNode(true);
                setA.insertBefore(clone, setA.firstChild);
                prependWidth += clone.offsetWidth;
                safety++;

                sourceIndex--;
                if (sourceIndex <= 0) {
                    sourceIndex = originals.length - 1;
                }
            }
        }

        const newestPosition = prependWidth;

        while (setA.scrollWidth < newestPosition + cycleWidth + containerWidth + 64 && safety < 80) {
            originals.forEach((item) => {
                setA.appendChild(item.cloneNode(true));
            });
            safety++;
        }

        setB.innerHTML = setA.innerHTML;

        this.marqueeDistance = cycleWidth;
        this.marqueeCenterBase = newestPosition + firstItemWidth / 2 - containerWidth / 2;

        if (! reset || ! this.isDragging) {
            this.marqueeScrollPx = 0;
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

Alpine.data('heroProgressiveSearch', (config) => ({
    sectors: config.sectors ?? [],
    suggestionsUrl: config.suggestionsUrl,
    sectorSlug: config.initialSector ?? '',
    professionSlug: config.initialProfession ?? '',
    query: config.initialKeyword ?? '',
    loadingLabel: config.loadingLabel,
    emptyLabel: config.emptyLabel,
    placeholderDefault: config.placeholderDefault ?? '',
    placeholderSector: config.placeholderSector ?? '',
    placeholderProfession: config.placeholderProfession ?? '',
    placeholderProfessionShort: config.placeholderProfessionShort ?? '',
    suggestions: [],
    open: false,
    loading: false,
    activeIndex: -1,
    debounceTimer: null,

    get filteredProfessions() {
        if (! this.sectorSlug) {
            return this.sectors.flatMap((sector) => sector.professions ?? []);
        }

        const sector = this.sectors.find((item) => item.slug === this.sectorSlug);

        return sector?.professions ?? [];
    },

    get selectedSector() {
        return this.sectors.find((item) => item.slug === this.sectorSlug) ?? null;
    },

    get selectedProfession() {
        return this.filteredProfessions.find((item) => item.slug === this.professionSlug)
            ?? this.sectors
                .flatMap((sector) => sector.professions ?? [])
                .find((item) => item.slug === this.professionSlug)
            ?? null;
    },

    get placeholder() {
        const profession = this.selectedProfession;
        const sector = this.selectedSector;

        if (profession) {
            const examples = (profession.examples ?? [])
                .slice(0, 2)
                .map((example) => `« ${example} »`)
                .join(', ');

            if (examples) {
                return this.placeholderProfession
                    .replace(':profession', profession.name)
                    .replace(':examples', examples);
            }

            return this.placeholderProfessionShort.replace(':profession', profession.name);
        }

        if (sector) {
            return this.placeholderSector.replace(':sector', sector.name);
        }

        return this.placeholderDefault;
    },

    onSectorChange() {
        const isValidProfession = this.filteredProfessions.some(
            (profession) => profession.slug === this.professionSlug
        );

        if (! isValidProfession) {
            this.professionSlug = '';
        }

        if (this.query.trim()) {
            this.fetchSuggestions();
        }
    },

    onProfessionChange() {
        if (this.query.trim()) {
            this.fetchSuggestions();
        }
    },

    onInput() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.fetchSuggestions(), 200);
    },

    onFocus() {
        if (this.query.trim()) {
            this.fetchSuggestions();
        }
    },

    buildSuggestionsUrl() {
        const params = new URLSearchParams({ q: this.query.trim() });

        if (this.professionSlug) {
            params.set('profession', this.professionSlug);
        }

        if (this.sectorSlug) {
            params.set('sector', this.sectorSlug);
        }

        return `${this.suggestionsUrl}?${params.toString()}`;
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
            const response = await fetch(this.buildSuggestionsUrl(), {
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

    selectSuggestion(item) {
        this.query = item.label;

        if (item.profession_slug) {
            this.professionSlug = item.profession_slug;
        }

        if (item.sector_slug) {
            this.sectorSlug = item.sector_slug;
        }

        this.closeSuggestions();
        this.$refs.input?.focus();
    },

    closeSuggestions() {
        this.open = false;
        this.activeIndex = -1;
    },

    onKeydown(event) {
        if (event.key === 'Escape') {
            this.closeSuggestions();

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
            this.selectSuggestion(this.suggestions[this.activeIndex]);
        }
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
